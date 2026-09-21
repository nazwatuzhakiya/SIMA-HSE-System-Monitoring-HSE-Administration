<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsidenK3;

class InsidenController extends Controller
{
    public function index()
    {
        $insidens = InsidenK3::latest()->get();
        return view('insiden.index', compact('insidens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'deskripsi_kejadian' => 'required|string',
            'kategori' => 'required|in:Near-Miss,First Aid,LTI',
        ]);

        $kode = 'INC-' . date('Y') . '-' . str_pad(InsidenK3::count() + 1, 3, '0', STR_PAD_LEFT);

        InsidenK3::create([
            'kode_insiden' => $kode,
            'tanggal' => $request->tanggal,
            'lokasi' => $request->lokasi,
            'deskripsi_kejadian' => $request->deskripsi_kejadian,
            'kategori' => $request->kategori,
            'nama_korban' => $request->nama_korban ?: null, // Opsional
            'nama_saksi' => $request->nama_saksi ?: null,
            'pj_departemen' => $request->pj_departemen ?: null,
            'tindak_lanjut' => $request->tindak_lanjut,
            'status' => $request->status ?: 'Open'
        ]);

        return redirect()->route('insiden.index')->with('success', "Laporan Insiden K3 ({$kode}) berhasil disimpan!");
    }
}
