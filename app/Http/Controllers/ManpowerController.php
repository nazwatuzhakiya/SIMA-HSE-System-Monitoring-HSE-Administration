<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manpower;
use App\Models\SubcontCompany;
use App\Models\SubcontPersonnel;
use App\Models\LogistikApd;
use App\Services\HseDepartmentService;

class ManpowerController extends Controller
{
    /**
     * Master List Manpower (Pekerja Pabrik & Subcont)
     */
    public function index(Request $request)
    {
        return $this->renderManpowerPage($request, 'plant');
    }

    /**
     * Master List Manpower Proyek (ENT)
     * Terpisah dari manpower pekerja pabrik sesuai instruksi: "nah ent juga punya man power tersendiri yang terpisah sama manpower pekerja"
     */
    public function proyek(Request $request)
    {
        return $this->renderManpowerPage($request, 'proyek');
    }

    protected function renderManpowerPage(Request $request, string $scope = 'plant')
    {
        // 1. Bersihkan kategori Non-Pekerja (FA, KLIEN, VISITOR, PKL) dari Master List Manpower
        // Sesuai Aturan: Kategori ini HANYA dicatat pada menu Monitoring saja
        Manpower::whereIn('departemen', ['KLIEN', 'VISITOR', 'PKL', 'FA', 'FA/PROJECT', 'FA PROJECT'])->delete();

        // 2. Auto-sync dari SubcontPersonnel ke Manpower HANYA untuk status pekerja (Internal, Subcont, & ENT)
        $workerSubconts = SubcontPersonnel::whereNotIn('company_code', ['KLIEN', 'VISITOR', 'PKL', 'FA', 'FA/PROJECT', 'FA PROJECT'])->get();
        foreach ($workerSubconts as $sc) {
            $normalizedDept = HseDepartmentService::normalizeCode($sc->company_code);
            $noId = 'EMP-' . $normalizedDept . '-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT);
            Manpower::updateOrCreate(
                ['no_id' => $noId],
                [
                    'nama' => $sc->nama,
                    'tgl_lahir' => $sc->tgl_lahir,
                    'tgl_masuk' => $sc->tgl_masuk,
                    'tgl_induction' => $sc->tgl_induction ?: $sc->tgl_masuk,
                    'tgl_induction_ulang' => $sc->tgl_induction_ulang,
                    'tgl_keluar' => $sc->tgl_keluar,
                    'departemen' => $normalizedDept,
                    'plant_location' => ($normalizedDept === 'ENT') ? 'Proyek / Konstruksi Luar' : 'Plant Kalijati Subang',
                    'jabatan' => $sc->posisi,
                    'status' => $sc->status ?: 'Aktif',
                    'apd_helmet_issued' => true,
                ]
            );

            // Otomatis mendata pengambilan APD Safety Helmet jika belum tercatat
            if (!LogistikApd::where('nama_personel', $sc->nama)->where('jenis_apd', 'Safety Helmet')->exists()) {
                LogistikApd::create([
                    'nama_personel' => $sc->nama,
                    'departemen_subcont' => $normalizedDept,
                    'tanggal_pengambilan' => $sc->tgl_induction ?: ($sc->tgl_masuk ?: date('Y-m-d')),
                    'jenis_apd' => 'Safety Helmet',
                    'jumlah' => 1,
                    'keterangan' => 'Pengambilan Safety Helmet Pertama (Otomatis Pekerja Baru)'
                ]);
            }
        }

        // 3. Logika Pemisahan per Tahun
        $currentYear = (int) date('Y');
        $earliestYear = 2020;
        $earliestRecord = Manpower::orderBy('tgl_induction', 'asc')->first();
        if ($earliestRecord && $earliestRecord->tgl_induction) {
            $recYear = (int) date('Y', strtotime($earliestRecord->tgl_induction));
            if ($recYear > 1990 && $recYear < $earliestYear) {
                $earliestYear = $recYear;
            }
        }
        $years = range($currentYear, $earliestYear);
        $selectedYear = (int) $request->input('tahun', $currentYear);

        // 4. Logika Pemisahan 12 Periode Siklus (Tanggal 26 s/d 25) - Tanpa kata Cut-off
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $periods = [];
        $today = date('Y-m-d');
        $defaultPeriodKey = null;

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        for ($m = 1; $m <= 12; $m++) {
            if ($m == 1) {
                $start = ($selectedYear - 1) . '-12-26';
                $end = $selectedYear . '-01-25';
                $label = '26 Des ' . ($selectedYear - 1) . ' - 25 Jan ' . $selectedYear;
            } else {
                $prevM = str_pad($m - 1, 2, '0', STR_PAD_LEFT);
                $currM = str_pad($m, 2, '0', STR_PAD_LEFT);
                $start = $selectedYear . '-' . $prevM . '-26';
                $end = $selectedYear . '-' . $currM . '-25';
                $label = '26 ' . substr($monthNames[$m - 1], 0, 3) . ' - 25 ' . substr($monthNames[$m], 0, 3) . ' ' . $selectedYear;
            }

            $isFrozen = false;
            if ($selectedYear > $currentYear || ($selectedYear == $currentYear && $m > $currentMonth)) {
                $isFrozen = true;
            }

            $key = 'P' . $m;
            $periods[$key] = [
                'key' => $key,
                'period_num' => $m,
                'label' => $label,
                'month_name' => $monthNames[$m],
                'start_date' => $start,
                'end_date' => $end,
                'is_frozen' => $isFrozen,
            ];

            if ($today >= $start && $today <= $end && $selectedYear == (int)date('Y')) {
                $defaultPeriodKey = $key;
            }
        }

        if (!$defaultPeriodKey) {
            $currM = (int) date('m');
            $defaultPeriodKey = 'P' . $currM;
        }

        $selectedPeriodKey = $request->input('periode', $defaultPeriodKey);
        $selectedPeriod = $periods[$selectedPeriodKey] ?? null;

        // 5. Query Data Manpower
        $query = Manpower::query();

        // Filter Scope: Pabrik vs Proyek ENT
        if ($scope === 'proyek') {
            $query->where('departemen', 'ENT');
            $pageTitle = 'Data Manpower Proyek (ENT) per Periode (26 s/d 25)';
            $departments = ['ENT'];
        } else {
            $query->whereNotIn('departemen', ['ENT', 'FA', 'FA/PROJECT', 'FA PROJECT', 'KLIEN', 'VISITOR', 'PKL']);
            $pageTitle = 'Data Manpower Subcont per Periode (26 s/d 25)';
            $internalDepts = array_keys(HseDepartmentService::getPlantDepartments());
            $subconts = array_keys(HseDepartmentService::getPlantSubconts());
            $departments = array_merge($internalDepts, $subconts);
        }

        $filterMode = $request->input('filter_mode', 'all');

        if ($selectedPeriodKey !== 'all_year' && $selectedPeriod) {
            $pStart = $selectedPeriod['start_date'];
            $pEnd = $selectedPeriod['end_date'];

            if ($filterMode === 'new_induction') {
                $query->whereBetween('tgl_induction', [$pStart, $pEnd]);
            } elseif ($filterMode === 'active_only') {
                $query->where(function($q) use ($pEnd) {
                    $q->where('status', 'Aktif')->where('tgl_induction', '<=', $pEnd);
                });
            } else {
                $query->where('tgl_induction', '<=', $pEnd);
            }
        } else {
            $pStart = $selectedYear . '-01-01';
            $pEnd = $selectedYear . '-12-31';
            $query->whereYear('tgl_induction', '<=', $selectedYear);
            if ($filterMode === 'active_only') {
                $query->where('status', 'Aktif');
            }
        }

        if ($request->filled('departemen')) {
            $query->where('departemen', HseDepartmentService::normalizeCode($request->departemen));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('no_id', 'LIKE', "%$search%")
                  ->orWhere('jabatan', 'LIKE', "%$search%");
            });
        }

        $manpowers = $query->orderBy('departemen', 'asc')
                           ->orderByRaw("CASE WHEN status = 'Aktif' THEN 0 ELSE 1 END ASC")
                           ->orderBy('tgl_induction', 'asc')
                           ->orderBy('id', 'asc')
                           ->get();

        // Menentukan status efektif riwayat per periode untuk setiap pekerja
        foreach ($manpowers as $mp) {
            $mp->status_periode = HseDepartmentService::getEffectiveWorkerStatusForPeriod($mp, $pStart, $pEnd, $selectedPeriodKey);
            $workerCalc = HseDepartmentService::calculateWorkerHoursAndDays(
                $mp->tgl_induction ?: ($mp->tgl_masuk ?: $pStart),
                $mp->tgl_keluar,
                $pStart,
                $pEnd
            );
            $mp->man_days_periode = ($mp->status_periode === 'Non-Aktif' && $mp->tgl_keluar && $mp->tgl_keluar < $pStart) ? 0 : $workerCalc['man_days'];
            $mp->man_hours_periode = ($mp->status_periode === 'Non-Aktif' && $mp->tgl_keluar && $mp->tgl_keluar < $pStart) ? 0 : $workerCalc['man_hours'];
        }

        $groupedManpowers = $manpowers->groupBy('departemen');

        $totalCount = $manpowers->count();
        $activeCount = $manpowers->where('status_periode', 'Aktif')->count();
        $nonAktifCount = $manpowers->where('status_periode', 'Non-Aktif')->count();

        $newInductionCount = 0;
        if ($selectedPeriod) {
            $indQuery = Manpower::whereBetween('tgl_induction', [$selectedPeriod['start_date'], $selectedPeriod['end_date']]);
            if ($scope === 'proyek') {
                $indQuery->where('departemen', 'ENT');
            } else {
                $indQuery->whereNotIn('departemen', ['ENT', 'FA', 'FA/PROJECT', 'KLIEN', 'VISITOR', 'PKL']);
            }
            $newInductionCount = $indQuery->count();
        }

        // Kalkulasi Otomatis Man Days & Man Hours Sesuai Permintaan Resmi dengan cut-off harian riil
        $aggregateStats = HseDepartmentService::calculateAggregate($manpowers, $pStart, $pEnd, $selectedPeriodKey);

        // Kalkulasi Statistik Perbandingan Manpower Per Bulan (12 Siklus) untuk Tampilan Eksekutif HSE Manager
        $monthlyComparison = [];
        $scopeWorkers = $scope === 'proyek' 
            ? Manpower::where('departemen', 'ENT')->get()
            : Manpower::whereNotIn('departemen', ['ENT', 'FA', 'FA/PROJECT', 'FA PROJECT', 'KLIEN', 'VISITOR', 'PKL'])->get();

        foreach ($periods as $pkey => $pdata) {
            $activeInP = 0;
            $deptCounts = [];
            $pManDays = 0;
            $pManHours = 0;
            $aktifList = [];
            $masukList = [];
            $keluarList = [];
            $isFrozen = $pdata['is_frozen'] ?? false;

            if (!$isFrozen) {
                foreach ($scopeWorkers as $w) {
                    $effStatus = HseDepartmentService::getEffectiveWorkerStatusForPeriod($w, $pdata['start_date'], $pdata['end_date'], $pkey);
                    if ($effStatus === 'Aktif') {
                        $activeInP++;
                        $deptCounts[$w->departemen] = ($deptCounts[$w->departemen] ?? 0) + 1;
                        $wCalc = HseDepartmentService::calculateWorkerHoursAndDays(
                            $w->tgl_induction ?: ($w->tgl_masuk ?: $pdata['start_date']),
                            $w->tgl_keluar,
                            $pdata['start_date'],
                            $pdata['end_date']
                        );
                        $pManDays += $wCalc['man_days'];
                        $pManHours += $wCalc['man_hours'];
                        $aktifList[] = [
                            'nama' => $w->nama,
                            'nik' => $w->nik ?: ('EMP-' . $w->departemen . '-' . str_pad($w->id, 3, '0', STR_PAD_LEFT)),
                            'departemen' => $w->departemen,
                            'posisi' => $w->posisi ?: '-',
                        ];
                    }

                    // Pekerja Masuk Bulan Itu (tgl_induction atau tgl_masuk jatuh pada periode ini)
                    $tglMasuk = $w->tgl_induction ?: $w->tgl_masuk;
                    if ($tglMasuk && $tglMasuk >= $pdata['start_date'] && $tglMasuk <= $pdata['end_date']) {
                        $masukList[] = [
                            'nama' => $w->nama,
                            'nik' => $w->nik ?: ('EMP-' . $w->departemen . '-' . str_pad($w->id, 3, '0', STR_PAD_LEFT)),
                            'departemen' => $w->departemen,
                            'posisi' => $w->posisi ?: '-',
                            'tanggal' => date('d/m/Y', strtotime($tglMasuk)),
                        ];
                    }

                    // Pekerja Keluar Bulan Itu (tgl_keluar jatuh pada periode ini)
                    if ($w->tgl_keluar && $w->tgl_keluar >= $pdata['start_date'] && $w->tgl_keluar <= $pdata['end_date']) {
                        $keluarList[] = [
                            'nama' => $w->nama,
                            'nik' => $w->nik ?: ('EMP-' . $w->departemen . '-' . str_pad($w->id, 3, '0', STR_PAD_LEFT)),
                            'departemen' => $w->departemen,
                            'posisi' => $w->posisi ?: '-',
                            'tanggal' => date('d/m/Y', strtotime($w->tgl_keluar)),
                        ];
                    }
                }
            }

            $monthlyComparison[$pkey] = [
                'period_key' => $pkey,
                'label' => $pdata['label'],
                'month_name' => $pdata['month_name'],
                'total_active' => $activeInP,
                'total_masuk' => count($masukList),
                'total_keluar' => count($keluarList),
                'man_days' => $pManDays,
                'man_hours' => $pManHours,
                'dept_breakdown' => $deptCounts,
                'is_frozen' => $isFrozen,
                'aktif_list' => $aktifList,
                'masuk_list' => $masukList,
                'keluar_list' => $keluarList,
            ];
        }

        $masukCount = 0;
        $keluarCount = 0;
        if ($selectedPeriodKey !== 'all_year' && isset($monthlyComparison[$selectedPeriodKey])) {
            $masukCount = $monthlyComparison[$selectedPeriodKey]['total_masuk'] ?? 0;
            $keluarCount = $monthlyComparison[$selectedPeriodKey]['total_keluar'] ?? 0;
        } else {
            $masukCount = array_sum(array_column($monthlyComparison, 'total_masuk'));
            $keluarCount = array_sum(array_column($monthlyComparison, 'total_keluar'));
        }

        $subconts = array_keys(HseDepartmentService::getPlantSubconts());

        return view('manpower.index', compact(
            'manpowers',
            'groupedManpowers',
            'years',
            'selectedYear',
            'periods',
            'selectedPeriodKey',
            'selectedPeriod',
            'filterMode',
            'totalCount',
            'activeCount',
            'nonAktifCount',
            'newInductionCount',
            'masukCount',
            'keluarCount',
            'aggregateStats',
            'monthlyComparison',
            'departments',
            'subconts',
            'scope',
            'pageTitle'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_id' => 'required|string|unique:manpowers,no_id',
            'nama' => 'required|string|max:255',
            'tgl_induction' => 'required|date',
            'departemen' => 'required|string',
            'jabatan' => 'required|string',
            'status' => 'required|in:Aktif,Non-Aktif',
        ]);

        $dept = HseDepartmentService::normalizeCode($request->departemen);
        if (HseDepartmentService::isSpecialNonWorker($dept)) {
            return redirect()->back()->with('error', "Kategori {$dept} bukan status pekerja dan tidak dapat dimasukkan ke Master List Manpower (hanya dicatat pada menu Monitoring).");
        }

        $mp = Manpower::create([
            'no_id' => $request->no_id,
            'nama' => $request->nama,
            'tgl_lahir' => $request->tgl_lahir,
            'tgl_masuk' => $request->tgl_masuk ?: $request->tgl_induction,
            'tgl_induction' => $request->tgl_induction,
            'departemen' => $dept,
            'plant_location' => ($dept === 'ENT') ? 'Proyek / Konstruksi Luar' : ($request->plant_location ?: 'Plant Kalijati Subang'),
            'jabatan' => $request->jabatan,
            'status' => $request->status ?: 'Aktif',
            'apd_helmet_issued' => true
        ]);

        LogistikApd::create([
            'manpower_id' => $mp->id,
            'nama_personel' => $mp->nama,
            'departemen_subcont' => $mp->departemen,
            'tanggal_pengambilan' => $mp->tgl_induction,
            'jenis_apd' => 'Safety Helmet dasar (Paket Safety Induction)',
            'jumlah' => 1,
            'keterangan' => 'Auto-triggered dari Form Induction Pekerja Baru'
        ]);

        $targetRoute = ($dept === 'ENT') ? 'manpower.proyek' : 'manpower.index';
        return redirect()->route($targetRoute)->with('success', "Manpower {$mp->nama} berhasil didaftarkan!");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tgl_induction' => 'nullable|date',
            'tgl_induction_ulang' => 'nullable|date',
            'departemen' => 'required|string',
            'jabatan' => 'required|string',
            'status' => 'required|in:Aktif,Non-Aktif',
        ]);

        $dept = HseDepartmentService::normalizeCode($request->departemen);
        if (HseDepartmentService::isSpecialNonWorker($dept)) {
            return redirect()->back()->with('error', "Kategori {$dept} tidak dapat dimasukkan ke Master List Manpower.");
        }

        $mp = Manpower::findOrFail($id);
        $oldStatus = $mp->status;
        $newStatus = $request->status;
        
        $tglKeluar = $mp->tgl_keluar;
        $tglInductionUlang = $request->filled('tgl_induction_ulang') ? $request->tgl_induction_ulang : $mp->tgl_induction_ulang;

        if ($oldStatus === 'Aktif' && $newStatus === 'Non-Aktif') {
            $tglKeluar = $request->filled('tgl_keluar') ? $request->tgl_keluar : date('Y-m-d');
        } elseif ($oldStatus === 'Non-Aktif' && $newStatus === 'Aktif') {
            if (empty($tglInductionUlang)) {
                $tglInductionUlang = date('Y-m-d');
            }
            $tglKeluar = null;
        }

        $updateData = [
            'nama' => $request->nama,
            'tgl_lahir' => $request->tgl_lahir,
            'tgl_induction' => $request->filled('tgl_induction') ? $request->tgl_induction : $mp->tgl_induction,
            'tgl_induction_ulang' => $tglInductionUlang,
            'tgl_keluar' => $tglKeluar,
            'departemen' => $dept,
            'plant_location' => $request->plant_location ?: $mp->plant_location,
            'jabatan' => $request->jabatan,
            'status' => $newStatus,
        ];

        $mp->update($updateData);

        // Catat riwayat snapshot status periode saat ini tanpa menimpa periode sebelumnya
        $currMonth = (int) date('n');
        $currYear = (int) date('Y');
        $periodKey = $request->input('period_key', 'P' . $currMonth);

        \App\Models\ManpowerPeriodStatus::updateOrCreate(
            [
                'manpower_id' => $mp->id,
                'period_key' => $periodKey,
            ],
            [
                'period_year' => $currYear,
                'period_num' => (int) str_replace('P', '', $periodKey),
                'status' => $newStatus,
                'tgl_masuk' => $mp->tgl_induction ?: $mp->tgl_masuk,
                'tgl_keluar' => $tglKeluar,
                'tgl_induction_ulang' => $tglInductionUlang,
                'keterangan' => "Status {$newStatus} pada periode {$periodKey}",
            ]
        );

        // Sync to SubcontPersonnel if exists
        SubcontPersonnel::where('company_code', $dept)
            ->where('nama', $mp->nama)
            ->update([
                'status' => $mp->status,
                'tgl_induction_ulang' => $mp->tgl_induction_ulang,
                'tgl_keluar' => $tglKeluar,
            ]);

        $targetRoute = ($dept === 'ENT') ? 'manpower.proyek' : 'manpower.index';
        return redirect()->route($targetRoute)->with('success', "Data manpower {$mp->nama} berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $mp = Manpower::findOrFail($id);
        $dept = $mp->departemen;
        $tglKeluar = date('Y-m-d');
        
        // Data tidak dihapus permanen, melainkan dinon-aktifkan dengan pencatatan tanggal keluar
        $mp->update([
            'status' => 'Non-Aktif',
            'tgl_keluar' => $tglKeluar,
        ]);

        $currMonth = (int) date('n');
        $periodKey = 'P' . $currMonth;

        \App\Models\ManpowerPeriodStatus::updateOrCreate(
            [
                'manpower_id' => $mp->id,
                'period_key' => $periodKey,
            ],
            [
                'period_year' => (int) date('Y'),
                'period_num' => $currMonth,
                'status' => 'Non-Aktif',
                'tgl_keluar' => $tglKeluar,
                'keterangan' => "Pekerja dinonaktifkan pada periode {$periodKey}",
            ]
        );

        SubcontPersonnel::where('company_code', $dept)
            ->where('nama', $mp->nama)
            ->update([
                'status' => 'Non-Aktif',
                'tgl_keluar' => $tglKeluar,
            ]);

        $targetRoute = ($dept === 'ENT') ? 'manpower.proyek' : 'manpower.index';
        return redirect()->route($targetRoute)->with('success', "Manpower {$mp->nama} berhasil dinon-aktifkan (data historis aman).");
    }
}
