<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditSchedule;
use Carbon\Carbon;

class AuditScheduleController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $schedules = AuditSchedule::where('tahun', $tahun)->orderBy('departemen', 'asc')->get();
        
        $latestUpdate = AuditSchedule::latest('updated_at')->first();
        $updateDate = $latestUpdate ? Carbon::parse($latestUpdate->updated_at)->translatedFormat('d F Y - H:i') : date('d F Y - H:i');

        $totalDeptCount = $schedules->count();
        $totalScheduledAudits = 0;
        $totalCompletedAudits = 0;

        foreach ($schedules as $s) {
            foreach (['jan_date', 'feb_date', 'mar_date', 'apr_date', 'may_date', 'jun_date', 'jul_date', 'aug_date', 'sep_date', 'oct_date', 'nov_date', 'dec_date'] as $m) {
                if (!empty($s->$m) && $s->$m != '-') {
                    $totalScheduledAudits++;
                    if (str_contains(strtolower($s->$m), 'v') || str_contains(strtolower($s->$m), 'ok') || str_contains(strtolower($s->$m), 'selesai')) {
                        $totalCompletedAudits++;
                    }
                }
            }
        }

        return view('audit_schedule.index', compact(
            'schedules',
            'tahun',
            'updateDate',
            'totalDeptCount',
            'totalScheduledAudits',
            'totalCompletedAudits'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'departemen' => 'required|string|max:100',
            'lead_auditor' => 'required|string|max:150',
            'auditor' => 'required|string|max:150',
        ]);

        AuditSchedule::create([
            'tahun' => $request->tahun ?: date('Y'),
            'update_date' => now(),
            'departemen' => $request->departemen,
            'lead_auditor' => $request->lead_auditor,
            'auditor' => $request->auditor,
            'wakil_auditor' => $request->wakil_auditor,
            'jan_date' => $request->jan_date,
            'feb_date' => $request->feb_date,
            'mar_date' => $request->mar_date,
            'apr_date' => $request->apr_date,
            'may_date' => $request->may_date,
            'jun_date' => $request->jun_date,
            'jul_date' => $request->jul_date,
            'aug_date' => $request->aug_date,
            'sep_date' => $request->sep_date,
            'oct_date' => $request->oct_date,
            'nov_date' => $request->nov_date,
            'dec_date' => $request->dec_date,
            'status' => $request->status ?: 'Terjadwal',
        ]);

        return redirect()->route('audit-schedule.index')->with('success', 'Jadwal pelaksanaan audit berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $schedule = AuditSchedule::findOrFail($id);
        $schedule->update([
            'tahun' => $request->tahun ?: $schedule->tahun,
            'update_date' => now(),
            'departemen' => $request->departemen,
            'lead_auditor' => $request->lead_auditor,
            'auditor' => $request->auditor,
            'wakil_auditor' => $request->wakil_auditor,
            'jan_date' => $request->jan_date,
            'feb_date' => $request->feb_date,
            'mar_date' => $request->mar_date,
            'apr_date' => $request->apr_date,
            'may_date' => $request->may_date,
            'jun_date' => $request->jun_date,
            'jul_date' => $request->jul_date,
            'aug_date' => $request->aug_date,
            'sep_date' => $request->sep_date,
            'oct_date' => $request->oct_date,
            'nov_date' => $request->nov_date,
            'dec_date' => $request->dec_date,
            'status' => $request->status ?: $schedule->status,
        ]);

        return redirect()->route('audit-schedule.index')->with('success', 'Jadwal audit berhasil diperbarui! Tanggal update diperbarui otomatis.');
    }

    public function destroy($id)
    {
        $schedule = AuditSchedule::findOrFail($id);
        $schedule->delete();
        return redirect()->route('audit-schedule.index')->with('success', 'Jadwal audit berhasil dihapus.');
    }
}
