<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubcontPersonnel;
use App\Models\SubcontCompany;
use App\Models\SubcontHistory;
use App\Models\Manpower;
use App\Models\LogistikApd;
use App\Services\HseDepartmentService;
use DB;

class SubcontController extends Controller
{
    /**
     * Memastikan seluruh departemen dan subcont resmi PT Murinda Iron Steel terdaftar di database
     */
    protected function syncOfficialCompanies()
    {
        $legacyMap = [
            'DKP' => 'SUB.DKP',
            'SUM' => 'SUB.SUM',
            'SUB.SUM.' => 'SUB.SUM',
            'AGS' => 'SUB.AGS',
            'PKN' => 'SUB.PKN',
            'CM' => 'SUB.CM',
            'NC' => 'SUB.NAGA CIPTA',
            'BM' => 'SUB.BM',
            'SBL' => 'SUB.SBL',
            'Built Up 9' => 'JT9',
            'SD' => 'STEEL DETAILING',
            'MNT' => 'MAINTENANCE',
            'HR/GA' => 'HRD',
            'Projek' => 'FA/PROJECT',
            'FA' => 'FA/PROJECT',
            'CUTTING & DRILLING' => 'CUTTING DRILLING',
        ];

        foreach ($legacyMap as $oldCode => $newCode) {
            SubcontPersonnel::where('company_code', $oldCode)->update(['company_code' => $newCode]);
            SubcontCompany::where('code', $oldCode)->update(['code' => $newCode]);
        }

        $allOfficial = HseDepartmentService::getAllOfficialEntities();
        foreach ($allOfficial as $code => $name) {
            SubcontCompany::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'plant_location' => 'Plant Kalijati Subang',
                    'status' => 'Aktif',
                    'description' => HseDepartmentService::isSpecialNonWorker($code) ? 'Monitoring Khusus Non-Pekerja' : 'Pekerja Subcont/Dept'
                ]
            );
        }
    }

    /**
     * MENU: Monitoring Departemen Internal Pabrik
     */
    public function departemen(Request $request)
    {
        $this->syncOfficialCompanies();

        $departments = HseDepartmentService::getPlantDepartments();
        $companies = SubcontCompany::whereIn('code', array_keys($departments))
            ->orderBy('code', 'asc')
            ->get();

        $activeCompany = $request->input('company');
        if (!$activeCompany || !array_key_exists($activeCompany, $departments)) {
            $activeCompany = 'PRODUKSI';
        }

        $period = HseDepartmentService::getCurrentPeriodDates();
        $pStart = $period['start'];
        $pEnd = $period['end'];

        $query = SubcontPersonnel::query();
        $query->where('company_code', $activeCompany);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_masuk', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tgl_masuk')) {
            $query->whereDate('tgl_masuk', $request->tgl_masuk);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('posisi', 'LIKE', "%$search%")
                  ->orWhere('domisili_kota', 'LIKE', "%$search%")
                  ->orWhere('keterangan', 'LIKE', "%$search%");
            });
        }

        $personnels = $query->orderByRaw("CASE WHEN status = 'Aktif' THEN 0 ELSE 1 END ASC")
                            ->orderBy('tgl_masuk', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();

        $companyCounts = SubcontPersonnel::whereIn('company_code', array_keys($departments))
            ->select('company_code', DB::raw('count(*) as total'))
            ->groupBy('company_code')
            ->pluck('total', 'company_code');

        $deptBaseQuery = SubcontPersonnel::whereIn('company_code', array_keys($departments));
        $totalActiveWorkers = (clone $deptBaseQuery)->where('status', 'Aktif')->count();
        $totalNonActiveWorkers = (clone $deptBaseQuery)->where('status', 'Non-Aktif')->count();
        $totalInductionPeriod = (clone $deptBaseQuery)
            ->where(function($q) use ($pStart, $pEnd) {
                $q->whereBetween('tgl_induction', [$pStart, $pEnd])
                  ->orWhere(function($sub) use ($pStart, $pEnd) {
                      $sub->whereNull('tgl_induction')->whereBetween('tgl_masuk', [$pStart, $pEnd]);
                  });
            })->count();
        $totalExitPeriod = (clone $deptBaseQuery)
            ->where('status', 'Non-Aktif')
            ->whereBetween('tgl_keluar', [$pStart, $pEnd])
            ->count();

        $positions = [
            'Sr. Manager', 'Manager', 'Supervisor / Officer', 'Engineer',
            'HSE Inspector', 'Foreman', 'Admin', 'Operator Plant',
            'Painter', 'Blaster', 'Helper', 'Fitter', 'Welder'
        ];

        return view('subcont.index', compact(
            'personnels',
            'companies',
            'activeCompany',
            'companyCounts',
            'positions',
            'period',
            'totalActiveWorkers',
            'totalNonActiveWorkers',
            'totalInductionPeriod',
            'totalExitPeriod',
            'departments'
        ));
    }

    /**
     * MENU: Monitoring Subkontraktor Resmi
     */
    public function subcont(Request $request)
    {
        $this->syncOfficialCompanies();

        $subconts = HseDepartmentService::getPlantSubconts();
        $companies = SubcontCompany::whereIn('code', array_keys($subconts))
            ->orderBy('code', 'asc')
            ->get();

        $activeCompany = $request->input('company');
        if (!$activeCompany || !array_key_exists($activeCompany, $subconts)) {
            $activeCompany = 'SUB.SBL';
        }

        $period = HseDepartmentService::getCurrentPeriodDates();
        $pStart = $period['start'];
        $pEnd = $period['end'];

        $query = SubcontPersonnel::query();
        $query->where('company_code', $activeCompany);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_masuk', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tgl_masuk')) {
            $query->whereDate('tgl_masuk', $request->tgl_masuk);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('posisi', 'LIKE', "%$search%")
                  ->orWhere('domisili_kota', 'LIKE', "%$search%")
                  ->orWhere('keterangan', 'LIKE', "%$search%");
            });
        }

        $personnels = $query->orderByRaw("CASE WHEN status = 'Aktif' THEN 0 ELSE 1 END ASC")
                            ->orderBy('tgl_masuk', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();

        $companyCounts = SubcontPersonnel::whereIn('company_code', array_keys($subconts))
            ->select('company_code', DB::raw('count(*) as total'))
            ->groupBy('company_code')
            ->pluck('total', 'company_code');

        $subcontBaseQuery = SubcontPersonnel::whereIn('company_code', array_keys($subconts));
        $totalActiveWorkers = (clone $subcontBaseQuery)->where('status', 'Aktif')->count();
        $totalNonActiveWorkers = (clone $subcontBaseQuery)->where('status', 'Non-Aktif')->count();
        $totalInductionPeriod = (clone $subcontBaseQuery)
            ->where(function($q) use ($pStart, $pEnd) {
                $q->whereBetween('tgl_induction', [$pStart, $pEnd])
                  ->orWhere(function($sub) use ($pStart, $pEnd) {
                      $sub->whereNull('tgl_induction')->whereBetween('tgl_masuk', [$pStart, $pEnd]);
                  });
            })->count();
        $totalExitPeriod = (clone $subcontBaseQuery)
            ->where('status', 'Non-Aktif')
            ->whereBetween('tgl_keluar', [$pStart, $pEnd])
            ->count();

        $positions = [
            'Supervisor Subcont', 'Foreman Subcont', 'Fitter Subcont',
            'Welder 3G/4G/6G', 'Blaster Painter', 'Scaffolder', 'Helper'
        ];

        return view('subcont.subcont', compact(
            'personnels',
            'companies',
            'activeCompany',
            'companyCounts',
            'positions',
            'period',
            'totalActiveWorkers',
            'totalNonActiveWorkers',
            'totalInductionPeriod',
            'totalExitPeriod',
            'subconts'
        ));
    }

    /**
     * Alias method for backward compatibility
     */
    public function index(Request $request)
    {
        return $this->departemen($request);
    }

    /**
     * MENU 2: Monitoring Proyek (FA/PROJECT & ENT)
     * Dipisahkan tersendiri sesuai instruksi: "ent ga bareng di monitoring pekrja ya, ent pisah sendiri juga kaya proyek"
     */
    public function proyek(Request $request)
    {
        $this->syncOfficialCompanies();

        $projectEntities = HseDepartmentService::getProjectEntities();
        $companies = SubcontCompany::whereIn('code', array_keys($projectEntities))->orderBy('code', 'asc')->get();

        $activeCompany = $request->input('company');
        if (!$activeCompany || !array_key_exists($activeCompany, $projectEntities)) {
            $activeCompany = 'ENT';
        }

        $period = HseDepartmentService::getCurrentPeriodDates();
        $pStart = $period['start'];
        $pEnd = $period['end'];

        $query = SubcontPersonnel::query();
        $query->where('company_code', $activeCompany);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_masuk', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tgl_masuk')) {
            $query->whereDate('tgl_masuk', $request->tgl_masuk);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('posisi', 'LIKE', "%$search%")
                  ->orWhere('domisili_kota', 'LIKE', "%$search%")
                  ->orWhere('keterangan', 'LIKE', "%$search%");
            });
        }

        $personnels = $query->orderByRaw("CASE WHEN status = 'Aktif' THEN 0 ELSE 1 END ASC")
                            ->orderBy('tgl_masuk', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();

        $companyCounts = SubcontPersonnel::whereIn('company_code', array_keys($projectEntities))
            ->select('company_code', DB::raw('count(*) as total'))
            ->groupBy('company_code')
            ->pluck('total', 'company_code');

        // Ringkasan pekerja proyek dan ENT dipisah secara spesifik
        $proyekBaseQuery = SubcontPersonnel::where('company_code', $activeCompany);
        $totalActiveWorkers = (clone $proyekBaseQuery)->where('status', 'Aktif')->count();
        $totalNonActiveWorkers = (clone $proyekBaseQuery)->where('status', 'Non-Aktif')->count();
        $totalInductionPeriod = (clone $proyekBaseQuery)
            ->where(function($q) use ($pStart, $pEnd) {
                $q->whereBetween('tgl_induction', [$pStart, $pEnd])
                  ->orWhere(function($sub) use ($pStart, $pEnd) {
                      $sub->whereNull('tgl_induction')->whereBetween('tgl_masuk', [$pStart, $pEnd]);
                  });
            })->count();
        $totalExitPeriod = (clone $proyekBaseQuery)
            ->where('status', 'Non-Aktif')
            ->whereBetween('tgl_keluar', [$pStart, $pEnd])
            ->count();

        $entStats = [
            'total' => SubcontPersonnel::where('company_code', 'ENT')->count(),
            'aktif' => SubcontPersonnel::where('company_code', 'ENT')->where('status', 'Aktif')->count(),
            'non_aktif' => SubcontPersonnel::where('company_code', 'ENT')->where('status', 'Non-Aktif')->count(),
        ];
        $faStats = [
            'total' => SubcontPersonnel::where('company_code', 'FA/PROJECT')->count(),
            'aktif' => SubcontPersonnel::where('company_code', 'FA/PROJECT')->where('status', 'Aktif')->count(),
            'non_aktif' => SubcontPersonnel::where('company_code', 'FA/PROJECT')->where('status', 'Non-Aktif')->count(),
        ];

        $positions = ['Site Manager', 'Site Engineer', 'HSE Officer Proyek', 'Supervisor Konstruksi', 'Foreman Proyek', 'Fitter Proyek', 'Welder Proyek', 'Rigger', 'Erector'];
        $plants = ['Proyek Lapangan Subang', 'Proyek Luar Kota', 'Plant Kalijati Subang'];

        return view('subcont.proyek', compact(
            'personnels',
            'companies',
            'activeCompany',
            'companyCounts',
            'positions',
            'plants',
            'period',
            'totalActiveWorkers',
            'totalNonActiveWorkers',
            'totalInductionPeriod',
            'totalExitPeriod',
            'entStats',
            'faStats'
        ));
    }

    /**
     * MENU 3: Monitoring Tamu & Khusus (PKL, VISITOR, KLIEN)
     * Form dan tabel terpisah karena bukan status pekerja.
     * TIDAK PERNAH dimasukkan ke Master List Manpower!
     */
    public function visitor(Request $request)
    {
        $this->syncOfficialCompanies();

        // Normalisasi status legacy jika ada (Kunjungan Selesai / Selesai Magang -> Non-Aktif)
        SubcontPersonnel::whereIn('status', ['Kunjungan Selesai', 'Selesai Magang'])->update(['status' => 'Non-Aktif']);

        // Auto backfill data lama jika asal_instansi kosong
        SubcontPersonnel::whereIn('company_code', ['PKL', 'VISITOR', 'KLIEN'])
            ->whereNull('asal_instansi')
            ->get()
            ->each(function($p) {
                $p->asal_instansi = $p->posisi ?: 'Instansi / Lembaga';
                if ($p->company_code === 'PKL' && empty($p->departemen_penempatan)) {
                    $p->departemen_penempatan = 'HSE';
                    $p->posisi = 'Staff Magang';
                }
                $p->save();
            });

        $visitorEntities = HseDepartmentService::getSpecialVisitors();
        $companies = SubcontCompany::whereIn('code', array_keys($visitorEntities))->orderBy('code', 'asc')->get();

        $activeCompany = $request->input('company');
        if (!$activeCompany || !array_key_exists($activeCompany, $visitorEntities)) {
            $activeCompany = 'PKL';
        }

        $period = HseDepartmentService::getCurrentPeriodDates();

        $query = SubcontPersonnel::query();
        $query->where('company_code', $activeCompany);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_masuk', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('tgl_masuk')) {
            $query->whereDate('tgl_masuk', $request->tgl_masuk);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('asal_instansi', 'LIKE', "%$search%")
                  ->orWhere('departemen_penempatan', 'LIKE', "%$search%")
                  ->orWhere('posisi', 'LIKE', "%$search%")
                  ->orWhere('keterangan', 'LIKE', "%$search%");
            });
        }

        $personnels = $query->orderByRaw("CASE WHEN status = 'Aktif' THEN 0 ELSE 1 END ASC")
                            ->orderBy('tgl_masuk', 'desc')
                            ->orderBy('id', 'desc')
                            ->get();

        $companyCounts = SubcontPersonnel::whereIn('company_code', array_keys($visitorEntities))
            ->select('company_code', DB::raw('count(*) as total'))
            ->groupBy('company_code')
            ->pluck('total', 'company_code');

        $totalPkl = SubcontPersonnel::where('company_code', 'PKL')->where('status', 'Aktif')->count();
        $totalVisitor = SubcontPersonnel::where('company_code', 'VISITOR')->count(); // Keseluruhan kunjungan
        $totalKlien = SubcontPersonnel::where('company_code', 'KLIEN')->count(); // Keseluruhan kunjungan klien

        $plantDepartments = HseDepartmentService::getPlantDepartments();

        return view('subcont.visitor', compact(
            'personnels',
            'companies',
            'activeCompany',
            'companyCounts',
            'period',
            'totalPkl',
            'totalVisitor',
            'totalKlien',
            'plantDepartments'
        ));
    }

    /**
     * Tambah Personel Pekerja Plant
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'company_code' => 'required|string|max:50',
            'tgl_masuk' => 'required|date',
            'tgl_induction' => 'nullable|date',
            'posisi' => 'required|string|max:100',
            'status' => 'nullable|in:Aktif,Non-Aktif',
            'catatan_induction' => 'nullable|string|max:255',
            'signature' => 'nullable|string',
        ]);

        $companyCode = HseDepartmentService::normalizeCode($request->company_code);
        $isNonWorker = HseDepartmentService::isSpecialNonWorker($companyCode);

        $notes = $request->catatan_induction ?: $request->keterangan;
        if ($request->filled('signature')) {
            $notes = ($notes ? $notes . " | " : "") . "[Digital Signature Verified]";
        }

        $sc = SubcontPersonnel::create([
            'nama' => $request->nama,
            'tgl_lahir' => $request->tgl_lahir,
            'company_code' => $companyCode,
            'plant_location' => 'Plant Kalijati Subang',
            'tgl_masuk' => $request->tgl_masuk,
            'tgl_keluar' => $request->tgl_keluar ?: null,
            'tgl_induction' => $request->tgl_induction ?: $request->tgl_masuk,
            'posisi' => $request->posisi,
            'status' => $request->status ?: 'Aktif',
            'domisili_alamat' => $request->domisili_alamat,
            'domisili_desa' => $request->domisili_desa,
            'domisili_kec' => $request->domisili_kec,
            'domisili_kota' => $request->domisili_kota ?: 'Subang',
            'keterangan' => $notes,
        ]);

        if (!$isNonWorker) {
            $autoNoId = 'EMP-' . $sc->company_code . '-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT);
            $mp = Manpower::updateOrCreate(
                ['no_id' => $autoNoId],
                [
                    'nama' => $sc->nama,
                    'tgl_lahir' => $sc->tgl_lahir,
                    'tgl_induction' => $sc->tgl_induction ?: $sc->tgl_masuk,
                    'departemen' => $sc->company_code,
                    'plant_location' => 'Plant Kalijati Subang',
                    'jabatan' => $sc->posisi,
                    'status' => $sc->status,
                    'apd_helmet_issued' => true,
                ]
            );

            LogistikApd::create([
                'manpower_id' => $mp->id,
                'nama_personel' => $sc->nama,
                'departemen_subcont' => $sc->company_code,
                'tanggal_pengambilan' => $sc->tgl_induction ?: $sc->tgl_masuk,
                'jenis_apd' => 'Safety Helmet',
                'jumlah' => 1,
                'keterangan' => 'Induction Pertama: ' . ($request->catatan_induction ?: 'Penyerahan Safety Helmet Pekerja Baru')
            ]);
        }

        $targetRoute = str_starts_with($sc->company_code, 'SUB.') ? 'subcont.subcont' : 'subcont.departemen';
        return redirect()->route($targetRoute, ['company' => $sc->company_code])
            ->with('success', "Data personel {$sc->nama} berhasil didaftarkan ke {$sc->company_code}.");
    }

    /**
     * Tambah Personel Proyek (FA/PROJECT atau ENT)
     * Jika ENT: Memiliki manpower tersendiri di Master Manpower Proyek (ENT).
     * Jika FA/PROJECT: Sesuai instruksi tidak dimasukkan ke manpower.
     */
    public function storeProyek(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'company_code' => 'required|in:FA/PROJECT,ENT,FA',
            'posisi' => 'required|string|max:100',
            'tgl_masuk' => 'required|date',
            'tgl_induction' => 'nullable|date',
            'status' => 'required|in:Aktif,Non-Aktif',
            'keterangan' => 'nullable|string',
        ]);

        $companyCode = HseDepartmentService::normalizeCode($request->company_code);

        $sc = SubcontPersonnel::create([
            'nama' => $request->nama,
            'tgl_lahir' => $request->tgl_lahir ?: null,
            'company_code' => $companyCode,
            'plant_location' => 'Proyek / Konstruksi Luar',
            'tgl_masuk' => $request->tgl_masuk,
            'tgl_keluar' => $request->tgl_keluar ?: null,
            'tgl_induction' => $request->tgl_induction ?: $request->tgl_masuk,
            'posisi' => $request->posisi,
            'status' => $request->status,
            'domisili_alamat' => $request->domisili_alamat,
            'domisili_kota' => $request->domisili_kota ?: 'Subang',
            'keterangan' => $request->keterangan,
        ]);

        // "nah ent juga punya man power tersendiri yang terpisah sama manpower pekerja"
        if ($companyCode === 'ENT') {
            $autoNoId = 'EMP-ENT-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT);
            $mp = Manpower::updateOrCreate(
                ['no_id' => $autoNoId],
                [
                    'nama' => $sc->nama,
                    'tgl_lahir' => $sc->tgl_lahir,
                    'tgl_induction' => $sc->tgl_induction ?: $sc->tgl_masuk,
                    'departemen' => 'ENT',
                    'plant_location' => 'Proyek / Konstruksi Luar',
                    'jabatan' => $sc->posisi,
                    'status' => $sc->status,
                    'apd_helmet_issued' => true,
                ]
            );

            LogistikApd::create([
                'manpower_id' => $mp->id,
                'nama_personel' => $sc->nama,
                'departemen_subcont' => 'ENT',
                'tanggal_pengambilan' => $sc->tgl_induction ?: $sc->tgl_masuk,
                'jenis_apd' => 'Safety Helmet',
                'jumlah' => 1,
                'keterangan' => 'Induction Proyek: Penyerahan Safety Helmet Pekerja Baru'
            ]);
        }

        return redirect()->route('subcont.proyek', ['company' => $companyCode])
            ->with('success', "Data pekerja proyek {$sc->nama} ({$companyCode}) berhasil ditambahkan dan APD Safety Helmet telah didata.");
    }

    /**
     * Tambah Tamu Khusus (PKL, VISITOR, KLIEN)
     * Form monitoring terpisah, TIDAK dimasukkan ke Manpower
     */
    public function storeVisitor(Request $request)
    {
        $request->validate([
            'nama'                  => 'required|string|max:255',
            'company_code'          => 'required|in:PKL,VISITOR,KLIEN',
            'asal_instansi'         => 'nullable|string|max:255',
            'departemen_penempatan' => 'nullable|string|max:100',
            'posisi'                => 'required|string|max:255',
            'tgl_masuk'             => 'required|date',
            'tgl_keluar'            => 'nullable|date',
            'status'                => 'required|in:Aktif,Non-Aktif',
            'keperluan'             => 'required|string|max:255',
            'keterangan'            => 'nullable|string',
        ]);

        $category = HseDepartmentService::normalizeCode($request->company_code);

        $notes = "Keperluan: " . $request->keperluan;
        if ($request->keterangan) {
            $notes .= " | " . $request->keterangan;
        }

        $asalInstansi = $request->asal_instansi ?: ($request->posisi ?: 'Instansi / Lembaga');
        $deptPenempatan = ($category === 'PKL')
            ? ($request->departemen_penempatan ?: 'HSE')
            : $request->departemen_penempatan;

        $sc = SubcontPersonnel::create([
            'nama'                  => $request->nama,
            'tgl_lahir'             => $request->tgl_lahir ?: null,
            'company_code'          => $category,
            'asal_instansi'         => $asalInstansi,
            'departemen_penempatan' => $deptPenempatan,
            'plant_location'        => 'Plant Kalijati Subang',
            'tgl_masuk'             => $request->tgl_masuk,
            'tgl_keluar'            => ($request->status === 'Aktif') ? null : ($request->tgl_keluar ?: date('Y-m-d')),
            'tgl_induction'         => $request->tgl_masuk,
            'posisi'                => $request->posisi,
            'status'                => $request->status,
            'domisili_alamat'       => $request->domisili_alamat,
            'domisili_kota'         => $request->domisili_kota ?: 'Subang',
            'keterangan'            => $notes,
        ]);

        return redirect()->route('subcont.visitor', ['company' => $category])
            ->with('success', "Data {$category} ({$sc->nama}) berhasil dicatat pada Monitoring Tamu & Khusus.");
    }

    /**
     * Selesaikan Kunjungan / Masa Magang dengan input tanggal selesai
     */
    public function finishVisitor(Request $request, $id)
    {
        $request->validate([
            'tgl_keluar'      => 'required|date',
            'catatan_selesai' => 'nullable|string|max:500',
        ]);

        $sc = SubcontPersonnel::findOrFail($id);

        $catatan = $sc->keterangan ?: '';
        if ($request->filled('catatan_selesai')) {
            $catatan .= ($catatan ? ' | ' : '') . 'Selesai: ' . $request->catatan_selesai;
        }

        $sc->update([
            'status'     => 'Non-Aktif',
            'tgl_keluar' => $request->tgl_keluar,
            'keterangan' => $catatan,
        ]);

        $autoNoId = 'EMP-' . $sc->company_code . '-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT);
        Manpower::where('no_id', $autoNoId)->update(['status' => 'Non-Aktif']);

        return redirect()->route('subcont.visitor', ['company' => $sc->company_code])
            ->with('success', "Kunjungan / Masa Magang {$sc->nama} berhasil diselesaikan pada tanggal " . date('d/m/Y', strtotime($request->tgl_keluar)) . " dan status menjadi Non-Aktif.");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'                  => 'required|string|max:255',
            'tgl_lahir'             => 'nullable|date',
            'company_code'          => 'required|string|max:50',
            'tgl_masuk'             => 'required|date',
            'tgl_induction'         => 'nullable|date',
            'tgl_induction_ulang'   => 'nullable|date',
            'posisi'                => 'required|string|max:100',
            'status'                => 'required|in:Aktif,Non-Aktif',
            'asal_instansi'         => 'nullable|string|max:255',
            'departemen_penempatan' => 'nullable|string|max:100',
        ]);

        $personnel = SubcontPersonnel::findOrFail($id);
        $oldStatus = $personnel->status;

        $updateData = [
            'nama'                => $request->nama,
            'tgl_lahir'           => $request->tgl_lahir,
            'company_code'        => HseDepartmentService::normalizeCode($request->company_code),
            'tgl_masuk'           => $request->tgl_masuk,
            'tgl_keluar'          => $request->tgl_keluar,
            'tgl_induction'       => $request->tgl_induction,
            'tgl_induction_ulang' => $request->tgl_induction_ulang,
            'posisi'              => $request->posisi,
            'status'              => $request->status,
            'domisili_alamat'     => $request->domisili_alamat,
            'domisili_desa'       => $request->domisili_desa,
            'domisili_kec'        => $request->domisili_kec,
            'domisili_kota'       => $request->domisili_kota,
        ];

        if ($request->has('asal_instansi')) {
            $updateData['asal_instansi'] = $request->asal_instansi;
        }
        if ($request->has('departemen_penempatan')) {
            $updateData['departemen_penempatan'] = $request->departemen_penempatan;
        }

        if ($request->filled('catatan_induction')) {
            $updateData['keterangan'] = $request->catatan_induction;
        }

        if ($oldStatus === 'Aktif' && $request->status === 'Non-Aktif') {
            if (empty($request->tgl_keluar)) {
                $updateData['tgl_keluar'] = date('Y-m-d');
            }
            $updateData['tgl_induction_ulang'] = $personnel->tgl_induction_ulang;
        } elseif ($oldStatus === 'Non-Aktif' && $request->status === 'Aktif') {
            if (empty($request->tgl_induction_ulang)) {
                return back()->withErrors(['tgl_induction_ulang' => 'Tanggal Induction Ulang wajib diisi saat mengaktifkan kembali pekerja.'])->withInput();
            }
            $updateData['tgl_keluar'] = null;
            $updateData['tgl_induction_ulang'] = $request->tgl_induction_ulang;
        }

        $personnel->update($updateData);

        $isNonWorker = HseDepartmentService::isSpecialNonWorker($personnel->company_code);
        $autoNoId = 'EMP-' . $personnel->company_code . '-' . str_pad($personnel->id, 3, '0', STR_PAD_LEFT);

        if (!$isNonWorker) {
            $mp = Manpower::updateOrCreate(
                ['no_id' => $autoNoId],
                [
                    'nama' => $personnel->nama,
                    'tgl_lahir' => $personnel->tgl_lahir,
                    'tgl_masuk' => $personnel->tgl_masuk,
                    'tgl_induction' => $personnel->tgl_induction ?: $personnel->tgl_masuk,
                    'tgl_induction_ulang' => $personnel->tgl_induction_ulang,
                    'tgl_keluar' => $personnel->tgl_keluar,
                    'departemen' => $personnel->company_code,
                    'plant_location' => 'Plant Kalijati Subang',
                    'jabatan' => $personnel->posisi,
                    'status' => $personnel->status,
                ]
            );

            $currMonth = (int) date('n');
            $currYear = (int) date('Y');
            $periodKey = 'P' . $currMonth;

            \App\Models\ManpowerPeriodStatus::updateOrCreate(
                [
                    'manpower_id' => $mp->id,
                    'period_key' => $periodKey,
                ],
                [
                    'period_year' => $currYear,
                    'period_num' => $currMonth,
                    'status' => $personnel->status,
                    'tgl_masuk' => $personnel->tgl_masuk,
                    'tgl_keluar' => $personnel->tgl_keluar,
                    'tgl_induction_ulang' => $personnel->tgl_induction_ulang,
                    'keterangan' => "Pembaruan via monitoring pada periode {$periodKey}",
                ]
            );
        } else {
            Manpower::where('no_id', $autoNoId)->delete();
        }

        if (HseDepartmentService::isVisitorEntity($personnel->company_code)) {
            return redirect()->route('subcont.visitor', ['company' => $personnel->company_code])
                ->with('success', "Data {$personnel->nama} berhasil diperbarui!");
        } elseif (HseDepartmentService::isProjectEntity($personnel->company_code)) {
            return redirect()->route('subcont.proyek', ['company' => $personnel->company_code])
                ->with('success', "Data {$personnel->nama} berhasil diperbarui!");
        }

        $targetRoute = str_starts_with($personnel->company_code, 'SUB.') ? 'subcont.subcont' : 'subcont.departemen';
        return redirect()->route($targetRoute, ['company' => $personnel->company_code])
            ->with('success', "Data {$personnel->nama} berhasil diperbarui!");
    }

    public function updateCompany(Request $request)
    {
        $request->validate([
            'company_code' => 'required|string',
            'new_name' => 'required|string|max:255',
        ]);

        $company = SubcontCompany::where('code', $request->company_code)->first();
        if ($company) {
            $oldName = $company->name;
            $company->update(['name' => $request->new_name]);

            SubcontHistory::create([
                'nama_pekerja' => 'Sistem Subcont',
                'change_type' => 'Ganti Nama Kontraktor/Dept',
                'old_value' => $oldName,
                'new_value' => $request->new_name,
                'notes' => "Entitas {$request->company_code} diperbarui namanya menjadi '{$request->new_name}'",
            ]);
        }

        return redirect()->back()->with('success', "Nama {$request->company_code} berhasil diperbarui!");
    }

    /**
     * Non-aktifkan data pekerja (DATA TIDAK BOLEH DIHAPUS DARI SISTEM)
     */
    public function destroy($id)
    {
        $sc = SubcontPersonnel::findOrFail($id);
        $tglKeluar = $sc->tgl_keluar ?: date('Y-m-d');
        $sc->update([
            'status' => 'Non-Aktif',
            'tgl_keluar' => $tglKeluar,
        ]);

        $autoNoId = 'EMP-' . $sc->company_code . '-' . str_pad($sc->id, 3, '0', STR_PAD_LEFT);
        $mp = Manpower::where('no_id', $autoNoId)->first();
        if ($mp) {
            $mp->update([
                'status' => 'Non-Aktif',
                'tgl_keluar' => $tglKeluar,
            ]);

            $currMonth = (int) date('n');
            \App\Models\ManpowerPeriodStatus::updateOrCreate(
                [
                    'manpower_id' => $mp->id,
                    'period_key' => 'P' . $currMonth,
                ],
                [
                    'period_year' => (int) date('Y'),
                    'period_num' => $currMonth,
                    'status' => 'Non-Aktif',
                    'tgl_keluar' => $tglKeluar,
                    'keterangan' => 'Dinonaktifkan via monitoring',
                ]
            );
        }

        if (HseDepartmentService::isVisitorEntity($sc->company_code)) {
            return redirect()->route('subcont.visitor', ['company' => $sc->company_code])
                ->with('success', "Kunjungan / Praktik Magang {$sc->nama} berhasil dinonaktifkan (status: Non-Aktif).");
        } elseif (HseDepartmentService::isProjectEntity($sc->company_code)) {
            return redirect()->route('subcont.proyek', ['company' => $sc->company_code])
                ->with('success', "Data {$sc->nama} berhasil dinonaktifkan.");
        }

        $targetRoute = str_starts_with($sc->company_code, 'SUB.') ? 'subcont.subcont' : 'subcont.departemen';
        return redirect()->route($targetRoute, ['company' => $sc->company_code])
            ->with('success', "Data personel {$sc->nama} berhasil dinonaktifkan (data historis tersimpan aman).");
    }
}
