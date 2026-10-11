<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manpower;
use App\Models\SubcontPersonnel;
use App\Models\LogistikApd;
use App\Models\ActivitySchedule;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Core Pillar KPIs (Hanya dept, subcont, dan ent yang masuk ke ringkasan)
        $excludedCodes = ['KLIEN', 'VISITOR', 'PKL', 'FA', 'FA/PROJECT', 'FA PROJECT'];
        $totalManpowerAktif = Manpower::whereNotIn('departemen', $excludedCodes)->where('status', 'Aktif')->count();
        $totalManpowerNonAktif = Manpower::whereNotIn('departemen', $excludedCodes)->where('status', 'Non-Aktif')->count();
        $totalSeluruhPersonel = SubcontPersonnel::whereNotIn('company_code', $excludedCodes)->count();
        $totalDistribusiApd = LogistikApd::sum('jumlah');

        // 2. Current Period Calculation (26 s/d 25) - Tanpa kata Cut-off
        $today = date('Y-m-d');
        $m = (int) date('m');
        $y = (int) date('Y');
        $d = (int) date('d');
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($d >= 26) {
            $nextM = $m == 12 ? 1 : $m + 1;
            $nextY = $m == 12 ? $y + 1 : $y;
            $currentPeriodLabel = "26 " . substr($monthNames[$m], 0, 3) . " - 25 " . substr($monthNames[$nextM], 0, 3) . " $y";
            $pStart = "$y-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-26";
            $pEnd = "$nextY-" . str_pad($nextM, 2, '0', STR_PAD_LEFT) . "-25";
        } else {
            $prevM = $m == 1 ? 12 : $m - 1;
            $prevY = $m == 1 ? $y - 1 : $y;
            $currentPeriodLabel = "26 " . substr($monthNames[$prevM], 0, 3) . " - 25 " . substr($monthNames[$m], 0, 3) . " $y";
            $pStart = "$prevY-" . str_pad($prevM, 2, '0', STR_PAD_LEFT) . "-26";
            $pEnd = "$y-" . str_pad($m, 2, '0', STR_PAD_LEFT) . "-25";
        }

        // 3. Seluruh Sebaran Resmi (Hanya Dept, Subcont, & ENT - Tamu & FA dikecualikan)
        $officialEntities = array_merge(
            \App\Services\HseDepartmentService::getInternalDepartments(),
            \App\Services\HseDepartmentService::getSubconts(),
            ['ENT' => 'ENT (Enterprise)']
        );

        $existingCounts = SubcontPersonnel::whereNotIn('company_code', $excludedCodes)
            ->select('company_code', DB::raw('count(*) as total'))
            ->groupBy('company_code')
            ->pluck('total', 'company_code')
            ->toArray();

        $subcontBreakdown = collect();
        foreach ($officialEntities as $code => $fullName) {
            $count = $existingCounts[$code] ?? ($existingCounts[\App\Services\HseDepartmentService::normalizeCode($code)] ?? 0);
            $subcontBreakdown->push((object)[
                'company_code' => $code,
                'name' => $fullName,
                'total' => $count,
                'is_special' => \App\Services\HseDepartmentService::isSpecialNonWorker($code)
            ]);
        }

        // Urutkan yang memiliki personil terbanyak di awal, namun semua tetap terlihat
        $subcontBreakdown = $subcontBreakdown->sortByDesc('total')->values();

        // 4. Kalkulasi Man Hours & Man Days Ringkasan (Pekerja FA strictly excluded)
        $allWorkers = SubcontPersonnel::whereNotIn('company_code', ['KLIEN', 'VISITOR', 'PKL', 'FA', 'FA/PROJECT', 'FA PROJECT'])->get();
        $aggregateStats = \App\Services\HseDepartmentService::calculateAggregate($allWorkers, $pStart, $pEnd);

        // 5. Kalkulasi Persentase Kehadiran SMT Perminggu & Persentase Terendah
        $smtWeeklyStats = \App\Services\HseDepartmentService::calculateWeeklySmtLowest($today);

        // 6. Data Operasional Hari Ini untuk Admin HSE
        $todayAbsensiCount = \App\Models\Absensi::where('tanggal', $today)->where('jenis_kegiatan', 'SMT')->count();
        $todayAbsensiHadir = \App\Models\Absensi::where('tanggal', $today)->where('jenis_kegiatan', 'SMT')->where('status_kehadiran', 'Hadir')->count();
        $todayAbsensiTelat = \App\Models\Absensi::where('tanggal', $today)->where('jenis_kegiatan', 'SMT')->where('status_kehadiran', 'Telat')->count();
        $todayAbsensiPct = $todayAbsensiCount > 0 ? round((($todayAbsensiHadir + $todayAbsensiTelat) / $todayAbsensiCount) * 100, 1) : 0;

        // 7. Ringkasan Eksekutif untuk HSE Manager
        $recordedWeeks = collect($smtWeeklyStats['weeks'])->filter(fn($w) => $w['persentase_total'] !== null);
        $avgSmtPercentage = $recordedWeeks->count() > 0 ? round($recordedWeeks->avg('persentase_total'), 1) : 0;
        $totalMitraSubcont = count(\App\Services\HseDepartmentService::getSubconts());
        $apdSummaryBreakdown = [
            'Helmet' => LogistikApd::where('jenis_apd', 'like', '%Helmet%')->sum('jumlah'),
            'Rompi' => LogistikApd::where('jenis_apd', 'like', '%Rompi%')->sum('jumlah'),
            'Sepatu' => LogistikApd::where('jenis_apd', 'like', '%Sepatu%')->sum('jumlah'),
            'Harness' => LogistikApd::where('jenis_apd', 'like', '%Harness%')->sum('jumlah'),
        ];

        return view('dashboard', compact(
            'totalManpowerAktif',
            'totalManpowerNonAktif',
            'totalSeluruhPersonel',
            'totalDistribusiApd',
            'currentPeriodLabel',
            'subcontBreakdown',
            'aggregateStats',
            'smtWeeklyStats',
            'todayAbsensiCount',
            'todayAbsensiHadir',
            'todayAbsensiTelat',
            'todayAbsensiPct',
            'avgSmtPercentage',
            'totalMitraSubcont',
            'apdSummaryBreakdown'
        ));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|string|max:100',
            'category' => 'required|string|max:255',
            'status' => 'required|in:Planned,In Progress,Completed,Delayed,Cancelled',
        ]);

        ActivitySchedule::create([
            'title' => $request->title,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location ?: 'Plant Kalijati Subang',
            'category' => $request->category,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return redirect()->route('dashboard')->with('success', 'Kegiatan baru berhasil ditambahkan ke kalender!');
    }

    public function updateSchedule(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|string|max:100',
            'category' => 'required|string|max:255',
            'status' => 'required|in:Planned,In Progress,Completed,Delayed,Cancelled',
        ]);

        $schedule = ActivitySchedule::findOrFail($id);
        $schedule->update([
            'title' => $request->title,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location ?: $schedule->location,
            'category' => $request->category,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return redirect()->route('dashboard')->with('success', 'Status & detail kegiatan berhasil diperbarui!');
    }

    public function destroySchedule($id)
    {
        $schedule = ActivitySchedule::findOrFail($id);
        $schedule->delete();
        return redirect()->route('dashboard')->with('success', 'Jadwal kegiatan berhasil dihapus.');
    }
}
