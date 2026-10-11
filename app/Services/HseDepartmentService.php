<?php

namespace App\Services;

class HseDepartmentService
{
    /**
     * Departemen Internal Pabrik (Plant)
     */
    public static function getPlantDepartments(): array
    {
        return [
            'HRD' => 'HRD',
            'GA' => 'GA',
            'HSE' => 'HSE',
            'PRODUKSI' => 'PRODUKSI',
            'JT9' => 'JT9',
            'EXP' => 'EXP',
            'MSB' => 'MSB',
            'PPC' => 'PPC',
            'PJC' => 'PJC',
            'MAINTENANCE' => 'MAINTENANCE',
            'STEEL DETAILING' => 'STEEL DETAILING',
            'WAREHOUSE' => 'WAREHOUSE',
            'QC' => 'QC',
            'CUTTING DRILLING' => 'CUTTING DRILLING',
        ];
    }

    /**
     * Subkontraktor Resmi Pabrik (Plant)
     */
    public static function getPlantSubconts(): array
    {
        return [
            'SUB.DKP' => 'SUB.DKP (DIRA KARYA PRATAMA)',
            'SUB.SUM' => 'SUB.SUM (SHAKA USAHA MANDIRI)',
            'SUB.AGS' => 'SUB.AGS (ARTHA GUNA SEJAHTERA)',
            'SUB.PKN' => 'SUB.PKN (PARWATA KANDAGA NONOMAN)',
            'SUB.CM' => 'SUB.CM (CITRA MANDIRI)',
            'SUB.NAGA CIPTA' => 'SUB.NC (NAGA CIPTA)',
            'SUB.BM' => 'SUB.BM (BANGKIT MANDIRI)',
            'SUB.SBL' => 'SUB.SBL (SURYA BAROKAH LESTARI)',
        ];
    }

    /**
     * Seluruh entitas Monitoring Pekerja Plant & Subcont
     */
    public static function getPlantAll(): array
    {
        return array_merge(self::getPlantDepartments(), self::getPlantSubconts());
    }

    /**
     * Entitas Pekerja Proyek Luar (FA/PROJECT & ENT)
     * Dipisahkan di Menu Monitoring Proyek tersendiri
     */
    public static function getProjectEntities(): array
    {
        return [
            'FA/PROJECT' => 'FA/PROJECT',
            'ENT' => 'ENT',
        ];
    }

    /**
     * Alias untuk backward compatibility
     */
    public static function getInternalDepartments(): array
    {
        return self::getPlantDepartments();
    }

    public static function getSubconts(): array
    {
        return self::getPlantSubconts();
    }

    public static function getSpecialNonWorkers(): array
    {
        return [
            'PKL' => 'PKL',
            'VISITOR' => 'VISITOR',
            'KLIEN' => 'KLIEN',
            'FA/PROJECT' => 'FA/PROJECT',
        ];
    }

    public static function getSpecialVisitors(): array
    {
        return [
            'PKL' => 'PKL',
            'VISITOR' => 'VISITOR',
            'KLIEN' => 'KLIEN',
        ];
    }

    /**
     * Seluruh daftar resmi 27 bagian di PT Murinda Iron Steel
     */
    public static function getAllOfficialEntities(): array
    {
        return array_merge(
            self::getPlantDepartments(),
            self::getPlantSubconts(),
            self::getProjectEntities(),
            self::getSpecialVisitors()
        );
    }

    /**
     * Mendapatkan nama resmi entitas berdasarkan kode
     */
    public static function getOfficialName(?string $code): string
    {
        if (!$code) return '-';
        $normalized = self::normalizeCode($code);
        $all = self::getAllOfficialEntities();
        return $all[$normalized] ?? ($all[$code] ?? $code);
    }

    /**
     * Periksa apakah entitas ini adalah Tamu Khusus / FA non-manpower
     * Sesuai instruksi: "FA, KLIEN, VISITOR, dan PKL itu hanya dicatat di monitoring saja, ga dimasukin ke manpower"
     */
    public static function isSpecialNonWorker(?string $code): bool
    {
        if (!$code) return false;
        $c = strtoupper(trim($code));
        return in_array($c, ['KLIEN', 'VISITOR', 'PKL', 'FA', 'FA/PROJECT', 'FA PROJECT']);
    }

    public static function isPlantEntity(?string $code): bool
    {
        if (!$code) return false;
        $c = self::normalizeCode($code);
        return array_key_exists($c, self::getPlantAll());
    }

    public static function isProjectEntity(?string $code): bool
    {
        if (!$code) return false;
        $c = self::normalizeCode($code);
        return in_array($c, ['FA/PROJECT', 'ENT', 'FA', 'FA PROJECT']);
    }

    public static function isVisitorEntity(?string $code): bool
    {
        if (!$code) return false;
        $c = self::normalizeCode($code);
        return in_array($c, ['PKL', 'VISITOR', 'KLIEN']);
    }

    /**
     * Normalisasi kode ke nama resmi yang tepat
     */
    public static function normalizeCode(?string $code): string
    {
        if (!$code) return 'PRODUKSI';
        $trimmed = strtoupper(trim($code));

        $map = [
            'DKP' => 'SUB.DKP',
            'DIRA KARYA PRATAMA' => 'SUB.DKP',
            'PT DIRA KARYA PRATAMA' => 'SUB.DKP',
            'SUM' => 'SUB.SUM',
            'SUB.SUM.' => 'SUB.SUM',
            'SHAKA USAHA MANDIRI' => 'SUB.SUM',
            'PT SHAKA USAHA MANDIRI' => 'SUB.SUM',
            'AGS' => 'SUB.AGS',
            'ARTHA GUNA SEJAHTERA' => 'SUB.AGS',
            'PT ARTHA GUNA SEJAHTERA' => 'SUB.AGS',
            'PKN' => 'SUB.PKN',
            'PARWATA KANDAGA NONOMAN' => 'SUB.PKN',
            'PT PARWATA KANDAGA NONOMAN' => 'SUB.PKN',
            'CM' => 'SUB.CM',
            'CITRA MANDIRI' => 'SUB.CM',
            'PT CITRA MANDIRI' => 'SUB.CM',
            'SUB.CITRA MANDIRI' => 'SUB.CM',
            'NC' => 'SUB.NAGA CIPTA',
            'SUB.NC' => 'SUB.NAGA CIPTA',
            'NAGA CIPTA' => 'SUB.NAGA CIPTA',
            'PT NAGA CIPTA' => 'SUB.NAGA CIPTA',
            'BM' => 'SUB.BM',
            'BANGKIT MANDIRI' => 'SUB.BM',
            'PT BANGKIT MANDIRI' => 'SUB.BM',
            'SBL' => 'SUB.SBL',
            'SURYA BAROKAH LESTARI' => 'SUB.SBL',
            'PT SURYA BAROKAH LESTARI' => 'SUB.SBL',
            'BUILT UP 9' => 'JT9',
            'BUILT UP' => 'JT9',
            'SD' => 'STEEL DETAILING',
            'MNT' => 'MAINTENANCE',
            'HR/GA' => 'HRD',
            'PROJEK' => 'FA/PROJECT',
            'FA' => 'FA/PROJECT',
            'FA PROJECT' => 'FA/PROJECT',
            'PRODUKSI' => 'PRODUKSI',
            'CUTTING & DRILLING' => 'CUTTING DRILLING',
        ];

        return $map[$trimmed] ?? $trimmed;
    }

    /**
     * Dapatkan rentang tanggal periode resmi (26 bulan lalu s/d 25 bulan ini)
     */
    public static function getCurrentPeriodDates(?string $referenceDate = null): array
    {
        $ref = $referenceDate ? strtotime($referenceDate) : time();
        $day = (int) date('j', $ref);
        $month = (int) date('n', $ref);
        $year = (int) date('Y', $ref);

        if ($day >= 26) {
            $startMonth = $month;
            $startYear = $year;
            $endMonth = $month === 12 ? 1 : $month + 1;
            $endYear = $month === 12 ? $year + 1 : $year;
        } else {
            $startMonth = $month === 1 ? 12 : $month - 1;
            $startYear = $month === 1 ? $year - 1 : $year;
            $endMonth = $month;
            $endYear = $year;
        }

        $startDate = sprintf('%04d-%02d-26', $startYear, $startMonth);
        $endDate = sprintf('%04d-%02d-25', $endYear, $endMonth);

        $indonesianMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $label = "26 " . $indonesianMonths[$startMonth] . " " . $startYear . " s/d 25 " . $indonesianMonths[$endMonth] . " " . $endYear;

        return [
            'start' => $startDate,
            'end' => $endDate,
            'label' => $label,
        ];
    }

    /**
     * Hitung Man Days & Man Hours seorang pekerja
     * Ketentuan jam kerja:
     * - Senin s/d Rabu: 7 jam
     * - Kamis: 8 jam
     * - Jumat: 7 jam
     * - Sabtu: 4 jam
     * - Minggu: 0 jam (Libur)
     */
    /**
     * Tentukan status pekerja yang efektif pada periode tertentu
     */
    public static function getEffectiveWorkerStatusForPeriod($worker, string $periodStart, string $periodEnd, ?string $periodKey = null): string
    {
        // 1. Cek jika ada snapshot manual tersimpan di database
        if (!empty($worker->id) && $periodKey) {
            $snap = \App\Models\ManpowerPeriodStatus::where('manpower_id', $worker->id)
                ->where('period_key', $periodKey)
                ->first();
            if ($snap) {
                return $snap->status;
            }
        }

        $tglInduction = $worker->tgl_induction ?: ($worker->tgl_masuk ?? null);
        $tglInductionUlang = $worker->tgl_induction_ulang ?? null;
        $tglKeluar = $worker->tgl_keluar ?? null;

        // Belum terdaftar / belum masuk pada periode ini
        if ($tglInduction && $tglInduction > $periodEnd) {
            return 'Non-Aktif';
        }

        // Riwayat Keluar (Off)
        if ($tglKeluar) {
            // Sudah keluar sebelum periode ini dimulai
            if ($tglKeluar < $periodStart) {
                // Kecuali ada induction ulang di periode ini atau sebelumnya yang lebih baru dari tgl_keluar
                if ($tglInductionUlang && $tglInductionUlang >= $periodStart && $tglInductionUlang <= $periodEnd) {
                    return 'Aktif';
                }
                return 'Non-Aktif';
            }
            // Keluar pada saat periode ini berjalan
            if ($tglKeluar >= $periodStart && $tglKeluar <= $periodEnd) {
                // Di periode ini status akhirnya Non-Aktif (namun jam kerja di cut-off s/d tgl_keluar)
                return 'Non-Aktif';
            }
            // Keluar di masa depan (setelah periodEnd) -> pada periode ini statusnya Aktif
            if ($tglKeluar > $periodEnd) {
                return 'Aktif';
            }
        }

        // Riwayat Re-Induction / Pengaktifan Kembali
        if ($tglInductionUlang) {
            // Baru diaktifkan di masa depan (setelah periodEnd) -> di periode lampau ini statusnya Non-Aktif
            if ($tglInductionUlang > $periodEnd) {
                return 'Non-Aktif';
            }
            // Diaktifkan pada atau sebelum periodEnd
            return 'Aktif';
        }

        return $worker->status ?: 'Aktif';
    }

    /**
     * Hitung Man Days & Man Hours seorang pekerja
     * Ketentuan jam kerja:
     * - Senin s/d Rabu: 7 jam
     * - Kamis: 8 jam
     * - Jumat: 7 jam
     * - Sabtu: 4 jam
     * - Minggu: 0 jam (Libur)
     */
    public static function calculateWorkerHoursAndDays(
        string $startDate,
        ?string $endDate = null,
        ?string $periodStart = null,
        ?string $periodEnd = null
    ): array {
        $today = date('Y-m-d');

        // Batas awal efektif: tidak boleh mendahului tanggal periode dimulai
        $start = $startDate;
        if ($periodStart && $periodStart > $start) {
            $start = $periodStart;
        }

        // Batas akhir efektif:
        // Jika periode telah selesai di masa lampau: batas maksimal adalah periodEnd atau tgl_keluar
        // Jika periode sedang berjalan (ongoing): batas maksimal adalah hari ini (today), periodEnd, atau tgl_keluar
        // Hari di masa depan TIDAK PERNAH dihitung otomatis!
        if ($endDate) {
            $end = $endDate;
            if ($periodEnd && $periodEnd < $end) {
                $end = $periodEnd;
            }
            if ($end > $today) {
                $end = $today;
            }
        } else {
            $end = $periodEnd ?: $today;
            if ($end > $today) {
                $end = $today;
            }
        }

        if ($start > $end) {
            return [
                'man_days' => 0,
                'man_hours' => 0,
                'start_used' => $start,
                'end_used' => $end,
            ];
        }

        $current = strtotime($start);
        $last = strtotime($end);

        $manDays = 0;
        $manHours = 0;

        while ($current <= $last) {
            $dayOfWeek = (int) date('w', $current); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

            switch ($dayOfWeek) {
                case 1: // Senin
                case 2: // Selasa
                case 3: // Rabu
                    $manDays++;
                    $manHours += 7;
                    break;
                case 4: // Kamis
                    $manDays++;
                    $manHours += 8;
                    break;
                case 5: // Jumat
                    $manDays++;
                    $manHours += 7;
                    break;
                case 6: // Sabtu
                    $manDays++;
                    $manHours += 4;
                    break;
                case 0: // Minggu
                default:
                    // Hari libur / off
                    break;
            }

            $current = strtotime('+1 day', $current);
        }

        return [
            'man_days' => $manDays,
            'man_hours' => $manHours,
            'start_used' => $start,
            'end_used' => $end,
        ];
    }

    /**
     * Hitung akumulasi Man Hours & Man Days untuk sekelompok pekerja
     */
    public static function calculateAggregate($personnels, ?string $periodStart = null, ?string $periodEnd = null, ?string $periodKey = null): array
    {
        $totalManDays = 0;
        $totalManHours = 0;
        $breakdown = [];

        foreach ($personnels as $p) {
            $pStart = $periodStart ?: date('Y-m-01');
            $pEnd = $periodEnd ?: date('Y-m-d');
            $effStatus = self::getEffectiveWorkerStatusForPeriod($p, $pStart, $pEnd, $periodKey);

            $startDate = $p->tgl_induction ?: ($p->tgl_masuk ?: $pStart);
            if (!empty($p->tgl_induction_ulang) && $p->tgl_induction_ulang > $startDate) {
                if ($p->tgl_induction_ulang >= $pStart) {
                    $startDate = $p->tgl_induction_ulang;
                }
            }

            $endDate = null;
            if (!empty($p->tgl_keluar)) {
                $endDate = $p->tgl_keluar;
            } elseif ($p->status === 'Non-Aktif' && empty($p->tgl_keluar)) {
                $endDate = ($effStatus === 'Non-Aktif') ? $pStart : null;
            }

            // Jika pekerja sudah keluar sebelum periode ini dimulai, 0 days 0 hours
            if ($endDate && $endDate < $pStart) {
                $res = [
                    'man_days' => 0,
                    'man_hours' => 0,
                    'start_used' => $pStart,
                    'end_used' => $endDate,
                ];
            } else {
                $res = self::calculateWorkerHoursAndDays($startDate, $endDate, $pStart, $pEnd);
            }

            $totalManDays += $res['man_days'];
            $totalManHours += $res['man_hours'];

            $dept = self::normalizeCode($p->company_code ?? ($p->departemen ?? 'LAINNYA'));
            if (!isset($breakdown[$dept])) {
                $breakdown[$dept] = [
                    'entity' => $dept,
                    'total_personel' => 0,
                    'active_personel' => 0,
                    'non_active_personel' => 0,
                    'man_days' => 0,
                    'man_hours' => 0,
                ];
            }

            $breakdown[$dept]['total_personel']++;
            if ($effStatus === 'Aktif') {
                $breakdown[$dept]['active_personel']++;
            } else {
                $breakdown[$dept]['non_active_personel']++;
            }
            $breakdown[$dept]['man_days'] += $res['man_days'];
            $breakdown[$dept]['man_hours'] += $res['man_hours'];
        }

        return [
            'total_man_days' => $totalManDays,
            'total_man_hours' => $totalManHours,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Menghitung persentase total kehadiran SMT per minggu dan mendeteksi persentase terendah
     *
     * @param string|null $referenceDate (default: hari ini)
     * @param int $weeksCount (jumlah minggu dianalisis, default: 4 minggu)
     * @return array
     */
    public static function calculateWeeklySmtLowest(?string $referenceDate = null, int $weeksCount = 4): array
    {
        $ref = $referenceDate ? \Carbon\Carbon::parse($referenceDate) : \Carbon\Carbon::now();
        $weeks = [];
        $lowestWeek = null;
        $lowestDeptOverall = null;

        // Ambil data per minggu ke belakang (termasuk minggu saat ini)
        for ($i = $weeksCount - 1; $i >= 0; $i--) {
            $wStart = $ref->copy()->subWeeks($i)->startOfWeek()->format('Y-m-d');
            $wEnd = $ref->copy()->subWeeks($i)->endOfWeek()->format('Y-m-d');
            $wLabel = \Carbon\Carbon::parse($wStart)->format('d M') . ' - ' . \Carbon\Carbon::parse($wEnd)->format('d M Y');

            $records = \App\Models\Absensi::whereBetween('tanggal', [$wStart, $wEnd])
                ->where(function ($q) {
                    $q->where('jenis_kegiatan', 'SMT')
                      ->orWhere('jenis_kegiatan', 'like', '%SMT%');
                })
                ->get();

            $hadir = $records->where('status_kehadiran', 'Hadir')->count();
            $telat = $records->where('status_kehadiran', 'Telat')->count();
            $tidakHadir = $records->whereNotIn('status_kehadiran', ['Hadir', 'Telat'])->count();
            $totalRecorded = $records->count();
            $harusnyaHadir = $hadir + $telat;

            $persentaseTotal = ($harusnyaHadir > 0) ? round(($hadir / $harusnyaHadir) * 100, 1) : null;

            // Hitung breakdown per departemen di minggu ini
            $deptStats = [];
            $grouped = $records->groupBy(function ($item) {
                return self::normalizeCode($item->departemen_subcont);
            });

            $lowestDeptInWeek = null;
            foreach ($grouped as $deptCode => $dRecords) {
                $dHadir = $dRecords->where('status_kehadiran', 'Hadir')->count();
                $dTelat = $dRecords->where('status_kehadiran', 'Telat')->count();
                $dHarus = $dHadir + $dTelat;
                if ($dHarus > 0) {
                    $dPct = round(($dHadir / $dHarus) * 100, 1);
                    $item = [
                        'code' => $deptCode,
                        'name' => self::getOfficialName($deptCode),
                        'hadir' => $dHadir,
                        'telat' => $dTelat,
                        'harusnya_hadir' => $dHarus,
                        'persentase' => $dPct,
                    ];
                    $deptStats[] = $item;

                    if ($lowestDeptInWeek === null || $dPct < $lowestDeptInWeek['persentase']) {
                        $lowestDeptInWeek = $item;
                    }
                    if ($lowestDeptOverall === null || $dPct < $lowestDeptOverall['persentase']) {
                        $lowestDeptOverall = array_merge($item, ['week_label' => $wLabel]);
                    }
                }
            }

            $weekItem = [
                'week_index' => $i,
                'is_current_week' => ($i === 0),
                'start_date' => $wStart,
                'end_date' => $wEnd,
                'label' => $wLabel,
                'total_recorded' => $totalRecorded,
                'hadir' => $hadir,
                'telat' => $telat,
                'tidak_hadir' => $tidakHadir,
                'harusnya_hadir' => $harusnyaHadir,
                'persentase_total' => $persentaseTotal,
                'lowest_dept' => $lowestDeptInWeek,
            ];

            $weeks[] = $weekItem;

            if ($persentaseTotal !== null) {
                if ($lowestWeek === null || $persentaseTotal < $lowestWeek['persentase_total']) {
                    $lowestWeek = $weekItem;
                }
            }
        }

        // Tentukan persentase total terendah mingguan
        $lowestPercentage = $lowestWeek ? $lowestWeek['persentase_total'] : 0.0;
        $lowestWeekLabel = $lowestWeek ? $lowestWeek['label'] : 'Belum ada data SMT';
        $lowestWeekHadir = $lowestWeek ? $lowestWeek['hadir'] : 0;
        $lowestWeekHarus = $lowestWeek ? $lowestWeek['harusnya_hadir'] : 0;
        $lowestDeptName = ($lowestWeek && $lowestWeek['lowest_dept']) ? $lowestWeek['lowest_dept']['name'] : '-';
        $lowestDeptPct = ($lowestWeek && $lowestWeek['lowest_dept']) ? $lowestWeek['lowest_dept']['persentase'] : null;

        return [
            'lowest_percentage' => $lowestPercentage,
            'lowest_week_label' => $lowestWeekLabel,
            'lowest_week_hadir' => $lowestWeekHadir,
            'lowest_week_harus' => $lowestWeekHarus,
            'lowest_dept_name' => $lowestDeptName,
            'lowest_dept_percentage' => $lowestDeptPct,
            'lowest_dept_overall' => $lowestDeptOverall,
            'lowest_week' => $lowestWeek,
            'weeks' => $weeks,
            'has_data' => ($lowestWeek !== null),
        ];
    }
}
