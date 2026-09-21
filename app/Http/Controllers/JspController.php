<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JoinSafetyPatrol;

class JspController extends Controller
{
    public function index(Request $request)
    {
        $query = JoinSafetyPatrol::query();

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('area')) {
            $query->where('area_patrol', 'LIKE', '%' . $request->area . '%');
        }

        $patrols = $query->latest()->get();

        return view('jsp.index', compact('patrols'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'area_patrol' => 'required|string|max:255',
            'tim_patroli' => 'required|string|max:255',
        ]);

        JoinSafetyPatrol::create([
            'tanggal' => $request->tanggal,
            'area_patrol' => $request->area_patrol,
            'tim_patroli' => $request->tim_patroli,
            'kat1_kondisi_umum' => $request->kat1_kondisi_umum ?: 'OK',
            'catatan1' => $request->catatan1,
            'kat2_peralatan_mesin' => $request->kat2_peralatan_mesin ?: 'OK',
            'catatan2' => $request->catatan2,
            'kat3_penataan_material' => $request->kat3_penataan_material ?: 'OK',
            'catatan3' => $request->catatan3,
            'kat4_instalasi_listrik' => $request->kat4_instalasi_listrik ?: 'OK',
            'catatan4' => $request->catatan4,
            'kat5_sarana_darurat' => $request->kat5_sarana_darurat ?: 'OK',
            'catatan5' => $request->catatan5,
            'kat6_alat_angkat' => $request->kat6_alat_angkat ?: 'OK',
            'catatan6' => $request->catatan6,
            'status' => $request->status ?: 'Selesai',
        ]);

        return redirect()->route('jsp.index')->with('success', 'Form Join Safety Patrol (JSP) berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $jsp = JoinSafetyPatrol::findOrFail($id);
        $jsp->update([
            'tanggal' => $request->tanggal,
            'area_patrol' => $request->area_patrol,
            'tim_patroli' => $request->tim_patroli,
            'kat1_kondisi_umum' => $request->kat1_kondisi_umum,
            'catatan1' => $request->catatan1,
            'kat2_peralatan_mesin' => $request->kat2_peralatan_mesin,
            'catatan2' => $request->catatan2,
            'kat3_penataan_material' => $request->kat3_penataan_material,
            'catatan3' => $request->catatan3,
            'kat4_instalasi_listrik' => $request->kat4_instalasi_listrik,
            'catatan4' => $request->catatan4,
            'kat5_sarana_darurat' => $request->kat5_sarana_darurat,
            'catatan5' => $request->catatan5,
            'kat6_alat_angkat' => $request->kat6_alat_angkat,
            'catatan6' => $request->catatan6,
            'status' => $request->status,
        ]);

        return redirect()->route('jsp.index')->with('success', 'Data Join Safety Patrol berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jsp = JoinSafetyPatrol::findOrFail($id);
        $jsp->delete();
        return redirect()->route('jsp.index')->with('success', 'Data Join Safety Patrol berhasil dihapus.');
    }
}
