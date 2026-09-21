<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Manpower;
use App\Models\SubcontPersonnel;
use App\Models\SubcontCompany;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $jenisKegiatan = $request->input('jenis_kegiatan', 'SMT');
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $departemen = $request->input('departemen', 'Produksi');
        $mode = $request->input('mode', null); // null (auto-detect), 'input', or 'fiks'

        // Fetch Manpowers for this department/subcont (only Active personnel)
        $manpowers = Manpower::where('status', 'Aktif')
            ->when($departemen != 'SEMUA', function($q) use ($departemen) {
                return $q->where('departemen', $departemen);
            })
            ->orderBy('nama', 'asc')
            ->get();

        // Fetch existing attendance records for date & activity
        $existing = Absensi::where('tanggal', $tanggal)
            ->where('jenis_kegiatan', $jenisKegiatan)
            ->get()
            ->keyBy('manpower_id');

        $isSaved = $existing->count() > 0;
        
        if ($mode === null) {
            $viewMode = $isSaved ? 'fiks' : 'input';
        } else {
            $viewMode = $mode;
        }

        $departments = [
            'Produksi', 'MSB', 'Built Up 9', 'Cutting Drilling', 'PPC', 'SD', 'QC',
            'MNT', 'Warehouse', 'HR/GA', 'PJC', 'EXP', 'DKP', 'BM', 'SB', 'SUM',
            'AGS', 'PKN', 'CM', 'NC', 'ENT', 'Projek'
        ];

        $subconts = SubcontCompany::where('status', 'Aktif')->get();

        // Persistent Attendance History Logs Query
        $histStartDate = $request->input('hist_start_date');
        $histEndDate = $request->input('hist_end_date');
        $histDept = $request->input('hist_dept');

        $historyLogs = Absensi::with('manpower')
            ->when($histStartDate, function($q) use ($histStartDate) {
                return $q->where('tanggal', '>=', $histStartDate);
            })
            ->when($histEndDate, function($q) use ($histEndDate) {
                return $q->where('tanggal', '<=', $histEndDate);
            })
            ->when($histDept && $histDept != 'SEMUA', function($q) use ($histDept) {
                return $q->where('departemen_subcont', $histDept);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('absensi.index', compact(
            'manpowers',
            'existing',
            'jenisKegiatan',
            'tanggal',
            'departemen',
            'departments',
            'subconts',
            'isSaved',
            'viewMode',
            'historyLogs'
        ));
    }

    public function storeFastAttendance(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $jenisKegiatan = $request->input('jenis_kegiatan', 'SMT');
        $departemen = $request->input('departemen_subcont', 'Produksi');
        $alasanPerubahan = $request->input('alasan_perubahan', null);
        $attendances = $request->input('attendance', []);

        foreach ($attendances as $mpId => $data) {
            $status = isset($data['status']) ? $data['status'] : 'Hadir';
            $keterangan = isset($data['keterangan']) ? $data['keterangan'] : null;
            $nama = isset($data['nama']) ? $data['nama'] : 'Pekerja';

            Absensi::updateOrCreate(
                [
                    'tanggal' => $tanggal,
                    'jenis_kegiatan' => $jenisKegiatan,
                    'manpower_id' => $mpId
                ],
                [
                    'departemen_subcont' => $departemen,
                    'nama_personel' => $nama,
                    'status_kehadiran' => $status,
                    'keterangan' => $keterangan,
                    'alasan_perubahan' => $alasanPerubahan,
                    'is_fixed' => true,
                ]
            );
        }

        return redirect()->route('absensi.index', [
            'jenis_kegiatan' => $jenisKegiatan,
            'tanggal' => $tanggal,
            'departemen' => $departemen,
            'mode' => 'fiks'
        ])->with('success', "Absensi {$jenisKegiatan} tanggal " . date('d/m/Y', strtotime($tanggal)) . " telah disimpan dan dikunci sebagai Data Fiks!");
    }

    public function updateSingleStatus(Request $request)
    {
        $request->validate([
            'absensi_id' => 'required|exists:absensis,id',
            'status_kehadiran' => 'required|in:Hadir,Izin,Sakit,Telat,Alpha',
            'alasan_perubahan' => 'required|string',
        ]);

        $absensi = Absensi::findOrFail($request->absensi_id);
        $absensi->update([
            'status_kehadiran' => $request->status_kehadiran,
            'keterangan'       => $request->keterangan,
            'alasan_perubahan' => $request->alasan_perubahan,
        ]);

        return redirect()->route('absensi.index')->with('success', "Status absensi untuk {$absensi->nama_personel} berhasil diperbarui di Riwayat Absensi!");
    }

    public function setupForm(Request $request)
    {
        $plantSubconts = \App\Services\HseDepartmentService::getPlantSubconts();
        $subconts = SubcontCompany::where('status', 'Aktif')
            ->where(function($q) use ($plantSubconts) {
                $q->whereIn('code', array_keys($plantSubconts))
                  ->orWhere('code', 'LIKE', 'SUB.%');
            })
            ->orderBy('code', 'asc')
            ->get();
        $departments = array_keys(\App\Services\HseDepartmentService::getInternalDepartments());
        $totalActiveWorkers = Manpower::where('status', 'Aktif')->count();

        return view('absensi.setup', compact('subconts', 'departments', 'totalActiveWorkers'));
    }

    public function printForm(Request $request)
    {
        $jenisKegiatan = $request->input('jenis_kegiatan', 'Safety Morning Talk (SMT)');
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $subcontCode = $request->input('subcont', 'SEMUA');

        // Resolve Subcont Name for Header
        if ($subcontCode === 'SEMUA' || empty($subcontCode)) {
            $subcontName = 'Semua Departemen & Subkontraktor';
        } else {
            $subcontCompany = SubcontCompany::where('code', $subcontCode)->first();
            $subcontName = $subcontCompany ? "{$subcontCompany->name} ({$subcontCompany->code})" : $subcontCode;
        }

        // Query active personnel dynamically
        $query = Manpower::where('status', 'Aktif');
        if ($subcontCode !== 'SEMUA' && !empty($subcontCode)) {
            $query->where('departemen', $subcontCode);
        }

        $activeWorkers = $query->orderBy('departemen', 'asc')->orderBy('nama', 'asc')->get();

        // Fallback to SubcontPersonnel if manpower table empty for that code
        if ($activeWorkers->isEmpty() && $subcontCode !== 'SEMUA') {
            $scWorkers = SubcontPersonnel::where('status', 'Aktif')
                ->where('company_code', $subcontCode)
                ->orderBy('nama', 'asc')
                ->get();
            
            foreach ($scWorkers as $sc) {
                $activeWorkers->push((object)[
                    'no_id' => 'EMP-' . $sc->company_code . '-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT),
                    'nama' => $sc->nama,
                    'jabatan' => $sc->posisi,
                    'departemen' => $sc->company_code,
                ]);
            }
        }

        return view('absensi.print', compact('activeWorkers', 'jenisKegiatan', 'tanggal', 'subcontCode', 'subcontName'));
    }
}
