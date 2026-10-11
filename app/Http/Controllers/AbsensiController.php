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
        $departemen = $request->input('departemen', 'PRODUKSI');
        $mode = $request->input('mode', null); // null (auto-detect), 'input', or 'fiks'

        // Daftar Bagian Resmi: Internal Pabrik, Subcont, & ENT (FA tidak ada absen)
        $plantDepts = \App\Services\HseDepartmentService::getPlantDepartments();
        $plantSubconts = \App\Services\HseDepartmentService::getPlantSubconts();
        
        $selectableDivisions = array_merge(
            array_keys($plantDepts),
            array_keys($plantSubconts),
            ['ENT']
        );

        // Normalisasi departemen yang dipilih
        $normalizedDept = \App\Services\HseDepartmentService::normalizeCode($departemen);

        // Fetch Manpower untuk departemen yang dipilih (khusus pekerja aktif, kecualikan FA & non-pekerja)
        $manpowers = Manpower::where('status', 'Aktif')
            ->whereNotIn('departemen', ['FA', 'FA/PROJECT', 'FA PROJECT', 'KLIEN', 'VISITOR', 'PKL'])
            ->when($normalizedDept != 'SEMUA', function($q) use ($normalizedDept) {
                return $q->where(function($subQ) use ($normalizedDept) {
                    $subQ->where('departemen', $normalizedDept)
                         ->orWhere('departemen', str_replace('SUB.', '', $normalizedDept));
                });
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

        // ========================================================
        // REKAP ABSENSI SMT (KHUSUS SMT)
        // ========================================================
        // Rumus Kehadiran:
        // - Yang Hadir = Pekerja dengan status 'Hadir' (tepat waktu)
        // - Yang Telat = Pekerja dengan status 'Telat'
        // - Tidak Hadir = Pekerja yang tidak hadir (tidak masuk dalam hitungan pembagi/pengali)
        // - Harusnya Hadir = Hadir + Telat (seluruh pekerja yang harusnya hadir termasuk yang telat)
        // - Persentase = (Hadir / Harusnya Hadir) * 100%
        // FA tidak ada absen ("fa gaada absen")

        $allActiveWorkers = Manpower::where('status', 'Aktif')
            ->whereNotIn('departemen', ['FA', 'FA/PROJECT', 'FA PROJECT', 'KLIEN', 'VISITOR', 'PKL'])
            ->get();

        $workersByDept = $allActiveWorkers->groupBy(function($item) {
            return \App\Services\HseDepartmentService::normalizeCode($item->departemen);
        });

        // Seluruh record absensi SMT pada tanggal terpilih
        $allSmtRecords = Absensi::where('tanggal', $tanggal)
            ->where('jenis_kegiatan', 'SMT')
            ->get();

        $smtRecordsByDept = $allSmtRecords->groupBy(function($item) {
            return \App\Services\HseDepartmentService::normalizeCode($item->departemen_subcont);
        });

        $recapCategories = [
            'internal' => [
                'title' => 'Departemen Internal Perusahaan',
                'items' => [],
                'totals' => ['total_pekerja' => 0, 'hadir' => 0, 'telat' => 0, 'tidak_hadir' => 0, 'harusnya_hadir' => 0, 'persentase' => 0]
            ],
            'subcont' => [
                'title' => 'Subkontraktor Mitra',
                'items' => [],
                'totals' => ['total_pekerja' => 0, 'hadir' => 0, 'telat' => 0, 'tidak_hadir' => 0, 'harusnya_hadir' => 0, 'persentase' => 0]
            ],
            'ent' => [
                'title' => 'Proyek Konstruksi Luar (ENT)',
                'items' => [],
                'totals' => ['total_pekerja' => 0, 'hadir' => 0, 'telat' => 0, 'tidak_hadir' => 0, 'harusnya_hadir' => 0, 'persentase' => 0]
            ],
        ];

        $grandTotals = [
            'total_pekerja' => 0,
            'hadir' => 0,
            'telat' => 0,
            'tidak_hadir' => 0,
            'harusnya_hadir' => 0,
            'persentase' => 0,
        ];

        // 1. Departemen Internal Pabrik
        foreach ($plantDepts as $code => $name) {
            $norm = \App\Services\HseDepartmentService::normalizeCode($code);
            $deptWorkers = $workersByDept->get($norm, collect());
            $deptRecords = $smtRecordsByDept->get($norm, collect());
            
            $row = self::computeSmtRow($code, $name, 'Internal', $deptWorkers, $deptRecords);
            $recapCategories['internal']['items'][$code] = $row;
            self::accumulateTotals($recapCategories['internal']['totals'], $row);
            self::accumulateTotals($grandTotals, $row);
        }

        // 2. Subkontraktor Resmi Pabrik
        foreach ($plantSubconts as $code => $name) {
            $norm = \App\Services\HseDepartmentService::normalizeCode($code);
            $subWorkers = $workersByDept->get($norm, collect());
            $subRecords = $smtRecordsByDept->get($norm, collect());
            
            $row = self::computeSmtRow($code, $name, 'Subcont', $subWorkers, $subRecords);
            $recapCategories['subcont']['items'][$code] = $row;
            self::accumulateTotals($recapCategories['subcont']['totals'], $row);
            self::accumulateTotals($grandTotals, $row);
        }

        // 3. Proyek Konstruksi Luar (ENT)
        $entWorkers = $workersByDept->get('ENT', collect());
        $entRecords = $smtRecordsByDept->get('ENT', collect());
        $entRow = self::computeSmtRow('ENT', 'ENT (Energy Nusantara Tech)', 'ENT', $entWorkers, $entRecords);
        $recapCategories['ent']['items']['ENT'] = $entRow;
        self::accumulateTotals($recapCategories['ent']['totals'], $entRow);
        self::accumulateTotals($grandTotals, $entRow);

        // Hitung persentase akhir untuk subtotal kategori & grand total
        foreach ($recapCategories as &$cat) {
            $hh = $cat['totals']['harusnya_hadir'];
            $h = $cat['totals']['hadir'];
            $cat['totals']['persentase'] = ($hh > 0) ? round(($h / $hh) * 100, 1) : 0;
        }
        $ghh = $grandTotals['harusnya_hadir'];
        $gh = $grandTotals['hadir'];
        $grandTotals['persentase'] = ($ghh > 0) ? round(($gh / $ghh) * 100, 1) : 0;

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

        // Kalkulasi Persentase Kehadiran SMT Perminggu & Nilai Terendah
        $smtWeeklyStats = \App\Services\HseDepartmentService::calculateWeeklySmtLowest($tanggal);

        return view('absensi.index', compact(
            'manpowers',
            'existing',
            'jenisKegiatan',
            'tanggal',
            'departemen',
            'normalizedDept',
            'selectableDivisions',
            'plantDepts',
            'plantSubconts',
            'recapCategories',
            'grandTotals',
            'isSaved',
            'viewMode',
            'historyLogs',
            'smtWeeklyStats'
        ));
    }

    protected static function computeSmtRow(string $code, string $name, string $category, $workers, $records): array
    {
        $totalPekerja = $workers->count();
        $isRecorded = $records->isNotEmpty();

        $hadir = $records->where('status_kehadiran', 'Hadir')->count();
        $telat = $records->where('status_kehadiran', 'Telat')->count();
        $tidakHadir = $records->whereNotIn('status_kehadiran', ['Hadir', 'Telat'])->count();

        $harusnyaHadir = $hadir + $telat;
        $persentase = ($harusnyaHadir > 0) ? round(($hadir / $harusnyaHadir) * 100, 1) : 0;

        return [
            'code' => $code,
            'name' => $name,
            'category' => $category,
            'total_pekerja' => $totalPekerja,
            'is_recorded' => $isRecorded,
            'hadir' => $hadir,
            'telat' => $telat,
            'tidak_hadir' => $tidakHadir,
            'harusnya_hadir' => $harusnyaHadir,
            'persentase' => $persentase,
        ];
    }

    protected static function accumulateTotals(array &$totals, array $item): void
    {
        $totals['total_pekerja'] += $item['total_pekerja'];
        $totals['hadir'] += $item['hadir'];
        $totals['telat'] += $item['telat'];
        $totals['tidak_hadir'] += $item['tidak_hadir'];
        $totals['harusnya_hadir'] += $item['harusnya_hadir'];
    }

    public function storeFastAttendance(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $jenisKegiatan = $request->input('jenis_kegiatan', 'SMT');
        $departemen = $request->input('departemen_subcont', 'PRODUKSI');
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
            'mode' => 'fiks',
            'tab' => 'input'
        ])->with('success', "Absensi {$jenisKegiatan} tanggal " . date('d/m/Y', strtotime($tanggal)) . " telah disimpan dan dikunci sebagai Data Fiks!");
    }

    public function updateSingleStatus(Request $request)
    {
        $request->validate([
            'absensi_id' => 'required|exists:absensis,id',
            'status_kehadiran' => 'required|string',
            'alasan_perubahan' => 'required|string',
        ]);

        $absensi = Absensi::findOrFail($request->absensi_id);
        $absensi->update([
            'status_kehadiran' => $request->status_kehadiran,
            'keterangan'       => $request->keterangan,
            'alasan_perubahan' => $request->alasan_perubahan,
        ]);

        return redirect()->route('absensi.index', ['tab' => 'history'])->with('success', "Status absensi untuk {$absensi->nama_personel} berhasil diperbarui di Riwayat Absensi!");
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
            $subcontName = $subcontCompany ? $subcontCompany->name : $subcontCode;
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
