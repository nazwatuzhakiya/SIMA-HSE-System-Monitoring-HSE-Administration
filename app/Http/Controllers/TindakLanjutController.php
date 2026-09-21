<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanBahaya;

class TindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanBahaya::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tingkat_risiko')) {
            $query->where('tingkat_risiko', $request->tingkat_risiko);
        }

        $reports = $query->latest()->get();

        return view('tindaklanjut.index', compact('reports'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Selesai',
            'tindak_lanjut' => 'required|string',
        ]);

        $report = LaporanBahaya::findOrFail($id);
        $report->update([
            'status' => $request->status,
            'tindak_lanjut' => $request->tindak_lanjut,
        ]);

        return redirect()->back()->with('success', "Status & Tindak Lanjut untuk Laporan Bahaya Tiket [{$report->ticket_code}] berhasil diperbarui menjadi {$report->status}!");
    }
}
