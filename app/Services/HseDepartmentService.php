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
            'SUB.SBL' => 'SUB.SBL (SURYA BAROKAH LESTARI)',
            'SUB.DKP' => 'SUB.DKP',
            'SUB.SUM' => 'SUB.SUM',
            'SUB.CM' => 'SUB.CM (CITRA MANDIRI)',
            'SUB.PKN' => 'SUB.PKN',
            'SUB.NAGA CIPTA' => 'SUB.NAGA CIPTA',
            'SUB.BM' => 'SUB.BM',
            'SUB.AGS' => 'SUB.AGS',
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
            'SUM' => 'SUB.SUM',
            'SUB.SUM.' => 'SUB.SUM',
            'AGS' => 'SUB.AGS',
            'PKN' => 'SUB.PKN',
            'CM' => 'SUB.CM',
            'CITRA MANDIRI' => 'SUB.CM',
            'SUB.CITRA MANDIRI' => 'SUB.CM',
            'NC' => 'SUB.NAGA CIPTA',
            'NAGA CIPTA' => 'SUB.NAGA CIPTA',
            'BM' => 'SUB.BM',
            'SBL' => 'SUB.SBL',
            'SURYA BAROKAH LESTARI' => 'SUB.SBL',
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
}
