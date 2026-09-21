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

        return view('apd.index', compact('logs', 'allApdLogs', 'allManpower', 'selectedMp', 'workerHistory', 'apdStats', 'departments', 'selectedDept'));
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
