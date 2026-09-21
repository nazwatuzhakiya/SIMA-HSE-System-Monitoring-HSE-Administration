<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KlinikObat;
use App\Models\Manpower;

class KlinikController extends Controller
{
    public function index()
    {
        $records = KlinikObat::latest()->get();
        $manpowers = Manpower::where('status', 'Aktif')->get();
        return view('klinik.index', compact('records', 'manpowers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'keluhan' => 'required|string',
            'nama_obat' => 'required|string',
            'jumlah_obat' => 'required|string',
            'tanggal_pengambilan' => 'required|date',
        ]);

        KlinikObat::create([
            'nama_pegawai' => $request->nama_pegawai,
            'nip' => $request->nip,
            'keluhan' => $request->keluhan,
            'nama_obat' => $request->nama_obat,
            'jumlah_obat' => $request->jumlah_obat,
            'tanggal_pengambilan' => $request->tanggal_pengambilan,
        ]);

        return redirect()->route('klinik.index')->with('success', 'Pencatatan obat klinik berhasil disimpan!');
    }
}
