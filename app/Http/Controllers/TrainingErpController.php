<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingErp;

class TrainingErpController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingErp::query();

        if ($request->filled('jenis_team')) {
            $query->where('jenis_team', $request->jenis_team);
        }

        if ($request->filled('struktur')) {
            $query->where('struktur', $request->struktur);
        }

        if ($request->filled('subcont')) {
            $query->where('subcont_dept', 'LIKE', '%' . $request->subcont . '%');
        }

        $trainings = $query->latest()->get();
        $teams = ['Kebakaran', 'P3K', 'Evakuasi', 'Umum'];

        return view('training_erp.index', compact('trainings', 'teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pekerja' => 'required|string|max:255',
            'subcont_dept' => 'required|string|max:100',
            'jenis_team' => 'required|in:Kebakaran,P3K,Evakuasi,Umum',
            'struktur' => 'required|in:Ya,Tidak',
        ]);

        TrainingErp::create([
            'nama_pekerja' => $request->nama_pekerja,
            'subcont_dept' => $request->subcont_dept,
            'jenis_team' => $request->jenis_team,
            'struktur' => $request->struktur,
            'pernyataan' => $request->pernyataan,
            'tgl_training' => $request->tgl_training ?: now(),
        ]);

        return redirect()->route('training-erp.index')->with('success', 'Data monitoring training ERP berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $training = TrainingErp::findOrFail($id);
        $training->update([
            'nama_pekerja' => $request->nama_pekerja,
            'subcont_dept' => $request->subcont_dept,
            'jenis_team' => $request->jenis_team,
            'struktur' => $request->struktur,
            'pernyataan' => $request->pernyataan,
            'tgl_training' => $request->tgl_training ?: $training->tgl_training,
        ]);

        return redirect()->route('training-erp.index')->with('success', 'Data monitoring training ERP berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $training = TrainingErp::findOrFail($id);
        $training->delete();
        return redirect()->route('training-erp.index')->with('success', 'Data training ERP berhasil dihapus.');
    }
}
