<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit5R;

class Audit5rController extends Controller
{
    public function index()
    {
        $audits = Audit5R::latest()->get();
        $departments = [
            'Produksi', 'MSB', 'Built Up 9', 'Cutting Drilling', 'PPC', 'SD', 'QC',
            'MNT', 'Warehouse', 'HR/GA', 'PJC', 'EXP', 'DKP', 'BM', 'SB', 'SUM',
            'AGS', 'PKN', 'CM', 'NC', 'ENT', 'Projek'
        ];

        return view('audit5r.index', compact('audits', 'departments'));
    }

    public function mobileInspector()
    {
        $departments = [
            'Produksi', 'MSB', 'Built Up 9', 'Cutting Drilling', 'PPC', 'SD', 'QC',
            'MNT', 'Warehouse', 'HR/GA', 'PJC', 'EXP', 'DKP', 'BM', 'SB', 'SUM',
            'AGS', 'PKN', 'CM', 'NC', 'ENT', 'Projek'
        ];
        return view('audit5r.mobile', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'departemen' => 'required|string',
            'tanggal' => 'required|date',
            'score_ringkas' => 'required|integer|min:1|max:10',
            'score_rapi' => 'required|integer|min:1|max:10',
            'score_resik' => 'required|integer|min:1|max:10',
            'score_rawat' => 'required|integer|min:1|max:10',
            'score_rajin' => 'required|integer|min:1|max:10',
        ]);

        $sum = $request->score_ringkas + $request->score_rapi + $request->score_resik + $request->score_rawat + $request->score_rajin;
        $totalPercentage = ($sum / 50) * 100;

        Audit5R::create([
            'auditor_name' => $request->auditor_name ?: 'HSE Inspector Subang',
            'departemen' => $request->departemen,
            'tanggal' => $request->tanggal,
            'score_ringkas' => $request->score_ringkas,
            'score_rapi' => $request->score_rapi,
            'score_resik' => $request->score_resik,
            'score_rawat' => $request->score_rawat,
            'score_rajin' => $request->score_rajin,
            'total_percentage' => $totalPercentage,
            'catatan' => $request->catatan
        ]);

        return redirect()->route('audit5r.index')->with('success', "Audit 5R untuk Dept {$request->departemen} berhasil disimpan! Nilai: " . number_format($totalPercentage, 1) . "%");
    }
}
