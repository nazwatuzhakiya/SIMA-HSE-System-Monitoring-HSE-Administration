<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogistikApd;
use App\Models\Manpower;

class ApdController extends Controller
{
    public function index(Request $request)
    {
        $selectedMp = null;
        $workerHistory = collect();
        
        if ($request->filled('manpower_id')) {
            $selectedMp = Manpower::find($request->manpower_id);
            if ($selectedMp) {
                $workerHistory = LogistikApd::where('manpower_id', $selectedMp->id)
                    ->orWhere('nama_personel', 'LIKE', '%' . $selectedMp->nama . '%')
                    ->latest()
                    ->get();
            }
        }

        $selectedDept = $request->input('departemen');

        // All active workers sorted alphabetically A-Z (non-aktif at bottom)
        $allManpower = Manpower::orderByRaw("CASE WHEN status = 'Aktif' THEN 0 ELSE 1 END ASC")
            ->when($selectedDept, function($q) use ($selectedDept) {
                return $q->where('departemen', $selectedDept);
            })
            ->orderBy('nama', 'asc')
            ->get();

        $departments = Manpower::distinct()->pluck('departemen')->filter()->values();

        // Query Logs with optional date range filter
        $logsQuery = LogistikApd::query();
        if ($selectedDept) {
            $logsQuery->where('departemen_subcont', $selectedDept);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $logsQuery->whereBetween('tanggal_pengambilan', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $logsQuery->where(function($q) use ($search) {
                $q->where('nama_personel', 'LIKE', "%$search%")
                  ->orWhere('jenis_apd', 'LIKE', "%$search%")
                  ->orWhere('departemen_subcont', 'LIKE', "%$search%");
            });
        }

        $logs = $logsQuery->latest()->get();
        $allApdLogs = LogistikApd::latest()->get();

        // APD Inventory & Distribution Stats
        $apdStats = [
            'Total' => LogistikApd::sum('jumlah'),
            'Helmet' => LogistikApd::where('jenis_apd', 'LIKE', '%Helmet%')->sum('jumlah'),
            'Sepatu Safety' => LogistikApd::where('jenis_apd', 'LIKE', '%Sepatu%')->orWhere('jenis_apd', 'LIKE', '%Shoes%')->sum('jumlah'),
            'Kacamata K3' => LogistikApd::where('jenis_apd', 'LIKE', '%Kacamata%')->orWhere('jenis_apd', 'LIKE', '%Glasses%')->sum('jumlah'),
            'Rompi Reflective' => LogistikApd::where('jenis_apd', 'LIKE', '%Vest%')->orWhere('jenis_apd', 'LIKE', '%Rompi%')->sum('jumlah'),
            'Body Harness' => LogistikApd::where('jenis_apd', 'LIKE', '%Harness%')->sum('jumlah'),
            'Sarung Tangan' => LogistikApd::where('jenis_apd', 'LIKE', '%Tangan%')->sum('jumlah'),
            'Masker' => LogistikApd::where('jenis_apd', 'LIKE', '%Masker%')->sum('jumlah'),
        ];

        // Periode berjalan dan deteksi pengambilan APD (Indikasi Penimbunan vs Distribusi Wajar)
        $period = \App\Services\HseDepartmentService::getCurrentPeriodDates();
        $pStart = $request->input('start_date', $period['start']);
        $pEnd = $request->input('end_date', $period['end']);

        $periodLogs = LogistikApd::whereBetween('tanggal_pengambilan', [$pStart, $pEnd])->get();
        if ($periodLogs->isEmpty()) {
            $periodLogs = LogistikApd::all();
        }

        $workerApdMap = [];
        foreach ($periodLogs as $log) {
            $workerKey = $log->nama_personel . '|' . $log->departemen_subcont;
            if (!isset($workerApdMap[$workerKey])) {
                $workerApdMap[$workerKey] = [
                    'nama_personel' => $log->nama_personel,
                    'departemen_subcont' => $log->departemen_subcont,
                    'total_apd' => 0,
                    'total_transaksi' => 0,
                    'apd_types' => [],
                    'has_duplicate_type' => false,
                    'duplicate_details' => []
                ];
            }
            $workerApdMap[$workerKey]['total_apd'] += $log->jumlah;
            $workerApdMap[$workerKey]['total_transaksi'] += 1;

            $items = array_map('trim', explode(',', $log->jenis_apd));
            foreach ($items as $item) {
                if (!empty($item)) {
                    $workerApdMap[$workerKey]['apd_types'][$item] = ($workerApdMap[$workerKey]['apd_types'][$item] ?? 0) + 1;
                }
            }
        }

        foreach ($workerApdMap as &$w) {
            foreach ($w['apd_types'] as $itemType => $count) {
                // Sesuai aturan: hanya jika dalam 1 periode ganti APD yang SAMA lebih dari 5 kali baru dikasih warning
                if ($count > 5) {
                    $w['has_duplicate_type'] = true;
                    $w['duplicate_details'][$itemType] = $count;
                }
            }
        }
        unset($w);

        $suspiciousWorkers = array_filter($workerApdMap, fn($w) => $w['has_duplicate_type']);
        usort($suspiciousWorkers, fn($a, $b) => $b['total_apd'] <=> $a['total_apd']);

        if (!empty($suspiciousWorkers)) {
            $topApdTaker = (object) reset($suspiciousWorkers);
            $topApdTaker->is_danger = true;
            $topApdTaker->status_label = 'Waspada Penimbunan APD';
            $topApdTaker->status_note = 'Terindikasi mengambil APD yang sama > 5 kali (' . implode(', ', array_map(fn($k, $v) => "$k: {$v}x", array_keys($topApdTaker->duplicate_details), $topApdTaker->duplicate_details)) . ')';
        } else {
            usort($workerApdMap, fn($a, $b) => $b['total_apd'] <=> $a['total_apd']);
            if (!empty($workerApdMap)) {
                $topApdTaker = (object) reset($workerApdMap);
                $topApdTaker->is_danger = false;
                $topApdTaker->status_label = 'Distribusi APD Aman';
                $topApdTaker->status_note = 'Aman dan tidak terindikasi adanya penimbunan';
            } else {
                $topApdTaker = null;
            }
        }

        // Data Grafik 1: Bagian / Departemen / Subcont mana yang paling banyak meminta APD
        $deptApdChart = LogistikApd::select('departemen_subcont', \Illuminate\Support\Facades\DB::raw('sum(jumlah) as total_apd'))
            ->groupBy('departemen_subcont')
            ->orderByDesc('total_apd')
            ->get();

        // Data Grafik 2: Jenis APD apa yang paling banyak diminta
        $apdTypeChart = [];
        foreach (LogistikApd::all() as $itemLog) {
            $splits = array_map('trim', explode(',', $itemLog->jenis_apd));
            foreach ($splits as $sp) {
                if (!empty($sp)) {
                    $apdTypeChart[$sp] = ($apdTypeChart[$sp] ?? 0) + $itemLog->jumlah;
                }
            }
        }
        arsort($apdTypeChart);

        // Data 10 Nama tersering mengajukan / mengambil APD
        $top10Requesters = LogistikApd::select('nama_personel', 'departemen_subcont', \Illuminate\Support\Facades\DB::raw('count(*) as total_transaksi'), \Illuminate\Support\Facades\DB::raw('sum(jumlah) as total_apd'))
            ->groupBy('nama_personel', 'departemen_subcont')
            ->orderByDesc('total_transaksi')
            ->orderByDesc('total_apd')
            ->limit(10)
            ->get();

        // Ringkasan Distribusi & Permintaan APD per Departemen/Subcont untuk Tampilan Eksekutif HSE Manager
        $deptApdSummary = LogistikApd::select('departemen_subcont', \Illuminate\Support\Facades\DB::raw('count(*) as total_transaksi'), \Illuminate\Support\Facades\DB::raw('sum(jumlah) as total_apd'))
            ->groupBy('departemen_subcont')
            ->orderByDesc('total_apd')
            ->get();

        return view('apd.index', compact(
            'logs',
            'allApdLogs',
            'allManpower',
            'selectedMp',
            'workerHistory',
            'apdStats',
            'departments',
            'selectedDept',
            'topApdTaker',
            'period',
            'deptApdSummary',
            'deptApdChart',
            'apdTypeChart',
            'top10Requesters'
        ));
    }

    public function create(Request $request)
    {
        $allManpower = Manpower::where('status', 'Aktif')
            ->orderBy('nama', 'asc')
            ->get();
        $departments = Manpower::distinct()->pluck('departemen')->filter()->values();

        return view('apd.create', compact('allManpower', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'manpower_id' => 'required|exists:manpowers,id',
            'tanggal_pengambilan' => 'required|date',
            'jenis_apd' => 'required|array|min:1',
        ]);

        $mp = Manpower::findOrFail($request->manpower_id);
        $jenisApdString = implode(', ', $request->jenis_apd);

        LogistikApd::create([
            'manpower_id' => $mp->id,
            'nama_personel' => $mp->nama,
            'departemen_subcont' => $mp->departemen,
            'tanggal_pengambilan' => $request->tanggal_pengambilan,
            'jenis_apd' => $jenisApdString,
            'jumlah' => count($request->jenis_apd),
            'keterangan' => $request->keterangan ?: 'Pengambilan APD Rutin Lapangan'
        ]);

        return redirect()->route('apd.index')
            ->with('success', "Distribusi APD untuk {$mp->nama} ({$mp->departemen}) berhasil dicatat ke sistem!");
    }
}
