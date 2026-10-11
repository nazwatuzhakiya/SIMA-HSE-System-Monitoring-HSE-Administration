<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SubcontPersonnel;
use App\Models\Manpower;
use App\Services\HseDepartmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HseRequirementsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_man_hours_calculation_accuracy()
    {
        // Uji rumus jam kerja resmi:
        // Senin s/d Rabu: 7 jam
        // Kamis: 8 jam
        // Jumat: 7 jam
        // Sabtu: 4 jam
        // Minggu: 0 jam
        // Contoh: Senin 2026-09-07 sampai Minggu 2026-09-13
        // Total: 7 + 7 + 7 + 8 + 7 + 4 = 40 jam, 6 man days
        $res = HseDepartmentService::calculateWorkerHoursAndDays('2026-09-07', '2026-09-13', '2026-09-01', '2026-09-30');
        $this->assertEquals(6, $res['man_days']);
        $this->assertEquals(40, $res['man_hours']);
    }

    public function test_special_non_worker_categorization()
    {
        $this->assertTrue(HseDepartmentService::isSpecialNonWorker('KLIEN'));
        $this->assertTrue(HseDepartmentService::isSpecialNonWorker('VISITOR'));
        $this->assertTrue(HseDepartmentService::isSpecialNonWorker('PKL'));
        $this->assertTrue(HseDepartmentService::isSpecialNonWorker('FA'));
        $this->assertTrue(HseDepartmentService::isSpecialNonWorker('FA/PROJECT'));

        $this->assertFalse(HseDepartmentService::isSpecialNonWorker('HRD'));
        $this->assertFalse(HseDepartmentService::isSpecialNonWorker('PRODUKSI'));
        $this->assertFalse(HseDepartmentService::isSpecialNonWorker('SUB.SBL'));
        $this->assertFalse(HseDepartmentService::isSpecialNonWorker('SUB.DKP'));
    }

    public function test_monitoring_departemen_page_renders()
    {
        $response = $this->get('/monitoring/departemen');
        $response->assertStatus(200);
        $response->assertSee('PRODUKSI');
        $response->assertSee('HRD');
        $response->assertSee('HSE');
        $response->assertSee('Pekerja Aktif');
        $response->assertSee('Pekerja Non-Aktif');
        $response->assertSee('Periode:');
        $response->assertSee('Monitoring Departemen');
    }

    public function test_monitoring_subcont_page_renders()
    {
        $response = $this->get('/monitoring/subcont');
        $response->assertStatus(200);
        $response->assertSee('SUB.SBL');
        $response->assertSee('SUB.DKP');
        $response->assertSee('SUB.SUM');
        $response->assertSee('Pekerja Aktif');
        $response->assertSee('Pekerja Non-Aktif');
        $response->assertSee('Periode:');
        $response->assertSee('Monitoring Subkontraktor');
    }

    public function test_subcont_alias_redirects_or_renders_departemen()
    {
        $response = $this->get('/subcont');
        $response->assertStatus(200);
        $response->assertSee('Monitoring Departemen');
    }

    public function test_monitoring_proyek_page_renders()
    {
        $response = $this->get('/monitoring/proyek');
        $response->assertStatus(200);
        $response->assertSee('ENT');
        $response->assertSee('FA/PROJECT');
        $response->assertSee('Total Personel');
    }

    public function test_monitoring_visitor_page_renders()
    {
        $response = $this->get('/monitoring/visitor');
        $response->assertStatus(200);
        $response->assertSee('PKL');
        $response->assertSee('VISITOR');
        $response->assertSee('KLIEN');
    }

    public function test_manpower_page_renders_with_new_title_and_man_hours_days()
    {
        $response = $this->get('/manpower');
        $response->assertStatus(200);
        $response->assertSee('Data Manpower Subcont per Periode (26 s/d 25)');
        $response->assertSee('Total Man Days');
        $response->assertSee('Akumulasi Man Hours');
        $response->assertDontSee('Cut-off');
        $response->assertDontSee('Cut-Off');
    }

    public function test_manpower_proyek_renders_ent_separately()
    {
        $response = $this->get('/manpower/proyek');
        $response->assertStatus(200);
        $response->assertSee('Data Manpower Proyek (ENT)');
        $response->assertDontSee('Cut-off');
    }

    public function test_landing_page_renders_5r_form_link()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Penilaian 5R');
        $response->assertSee('https://docs.google.com/forms/d/e/1FAIpQLSeC-hDDXPRaCg0b4adFef0gSCRPoMXRoodD-Izm7tkWdy67ag/viewform');
    }

    public function test_dashboard_does_not_render_external_5r_link()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Utama HSE');
        $response->assertDontSee('Sebaran Tenaga Kerja per Subcont', false);
        $response->assertDontSee('https://docs.google.com/forms/d/e/1FAIpQLSeC-hDDXPRaCg0b4adFef0gSCRPoMXRoodD-Izm7tkWdy67ag/viewform');
        $response->assertDontSee('https://inductionhse.vercel.app');
        $response->assertDontSee('Cut-off');
        $response->assertDontSee('Cut-Off');
    }

    public function test_special_visitor_is_not_synced_to_master_manpower()
    {
        $initialManpowerCount = Manpower::count();

        $response = $this->post('/subcont/visitor', [
            'nama' => 'Tamu Klien Test',
            'company_code' => 'KLIEN',
            'posisi' => 'PT Konstruksi Mega',
            'keperluan' => 'Inspeksi Lapangan',
            'tgl_masuk' => '2026-09-17',
            'tgl_keluar' => '2026-09-17',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect('/monitoring/visitor?company=KLIEN');
        
        $this->assertDatabaseHas('subcont_personnels', [
            'nama' => 'Tamu Klien Test',
            'company_code' => 'KLIEN',
        ]);

        $this->assertDatabaseMissing('manpowers', [
            'nama' => 'Tamu Klien Test',
        ]);
        $this->assertEquals($initialManpowerCount, Manpower::count());
    }

    public function test_ent_worker_creates_ent_manpower_separately()
    {
        $response = $this->post('/subcont/proyek', [
            'nama' => 'Pekerja ENT Lapangan',
            'company_code' => 'ENT',
            'posisi' => 'Erector Leader',
            'tgl_masuk' => '2026-09-01',
            'tgl_induction' => '2026-09-01',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect('/monitoring/proyek?company=ENT');

        $this->assertDatabaseHas('subcont_personnels', [
            'nama' => 'Pekerja ENT Lapangan',
            'company_code' => 'ENT',
        ]);

        $this->assertDatabaseHas('manpowers', [
            'nama' => 'Pekerja ENT Lapangan',
            'departemen' => 'ENT',
        ]);
    }

    public function test_fa_project_worker_is_not_synced_to_manpower()
    {
        $response = $this->post('/subcont/proyek', [
            'nama' => 'Pekerja FA Project Luar',
            'company_code' => 'FA/PROJECT',
            'posisi' => 'Site Coordinator',
            'tgl_masuk' => '2026-09-01',
            'tgl_induction' => '2026-09-01',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect('/monitoring/proyek?company=FA%2FPROJECT');

        $this->assertDatabaseHas('subcont_personnels', [
            'nama' => 'Pekerja FA Project Luar',
            'company_code' => 'FA/PROJECT',
        ]);

        $this->assertDatabaseMissing('manpowers', [
            'nama' => 'Pekerja FA Project Luar',
        ]);
    }

    public function test_deleting_worker_archives_instead_of_hard_deleting()
    {
        $worker = SubcontPersonnel::create([
            'nama' => 'Pekerja Uji NonAktif',
            'company_code' => 'SUB.SBL',
            'posisi' => 'Welder',
            'tgl_masuk' => '2026-09-01',
            'status' => 'Aktif',
            'tgl_induction' => '2026-09-01',
        ]);

        $response = $this->delete('/subcont/' . $worker->id);
        $response->assertRedirect('/monitoring/subcont?company=SUB.SBL');

        $this->assertDatabaseHas('subcont_personnels', [
            'id' => $worker->id,
            'status' => 'Non-Aktif',
        ]);
    }

    public function test_worker_creation_automatically_logs_safety_helmet_apd()
    {
        $response = $this->post('/subcont', [
            'nama' => 'Pekerja Baru Otomatis APD',
            'company_code' => 'SUB.DKP',
            'posisi' => 'Fitter',
            'tgl_masuk' => '2026-09-01',
            'tgl_induction' => '2026-09-01',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect('/monitoring/subcont?company=SUB.DKP');

        $this->assertDatabaseHas('logistik_apds', [
            'nama_personel' => 'Pekerja Baru Otomatis APD',
            'departemen_subcont' => 'SUB.DKP',
            'jenis_apd' => 'Safety Helmet',
        ]);
    }

    public function test_hse_admin_credentials_authenticate_successfully()
    {
        $response = $this->post('/login', [
            'email' => 'HSEadmin',
            'password' => 'hsemurinda123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('HSEadmin', session('user_name'));
        $this->assertEquals('admin', session('user_role'));
        $this->assertFalse(session('is_read_only'));
        $this->assertTrue(session('is_logged_in'));
    }

    public function test_hse_manager_credentials_authenticate_with_read_only_role()
    {
        $response = $this->post('/login', [
            'email' => 'HSEManager',
            'password' => 'hsemurinda123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('HSEManager', session('user_name'));
        $this->assertEquals('manager', session('user_role'));
        $this->assertTrue(session('is_read_only'));
        $this->assertTrue(session('is_logged_in'));
    }

    public function test_hse_manager_read_only_blocks_mutations()
    {
        // Login as HSEManager
        $this->post('/login', [
            'email' => 'HSEManager',
            'password' => 'hsemurinda123',
        ]);

        // Attempt mutating subcont creation
        $response = $this->post('/subcont', [
            'nama' => 'Pekerja Tidak Boleh Dibuat',
            'company_code' => 'HRD',
            'posisi' => 'Staff',
            'tgl_masuk' => '2026-01-01',
            'status' => 'Aktif',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('subcont_personnels', [
            'nama' => 'Pekerja Tidak Boleh Dibuat',
        ]);
    }

    public function test_hse_manager_can_view_dashboard_and_monitoring_with_read_only_badge()
    {
        // Login as HSEManager
        $this->post('/login', [
            'email' => 'HSEManager',
            'password' => 'hsemurinda123',
        ]);

        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertDontSee('HSE MANAGER (READ ONLY)');
        $dashboardResponse->assertSee('HSEManager');

        $deptResponse = $this->get('/monitoring/departemen');
        $deptResponse->assertStatus(200);
        $deptResponse->assertDontSee('HSE MANAGER (READ ONLY)');

        $apdResponse = $this->get('/apd');
        $apdResponse->assertStatus(200);
        $apdResponse->assertDontSee('HSE MANAGER (READ ONLY)');

        $absensiResponse = $this->get('/absensi');
        $absensiResponse->assertStatus(200);
        $absensiResponse->assertDontSee('HSE MANAGER (READ ONLY)');
    }

    public function test_calculate_weekly_smt_lowest_returns_correct_stats()
    {
        $stats = \App\Services\HseDepartmentService::calculateWeeklySmtLowest();
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('lowest_percentage', $stats);
        $this->assertArrayHasKey('lowest_week_label', $stats);
        $this->assertArrayHasKey('weeks', $stats);
        $this->assertCount(4, $stats['weeks']);
    }

    public function test_both_roles_can_view_lowest_weekly_smt_percentage_on_dashboard_and_absensi()
    {
        // 1. Role HSE Admin
        $this->post('/login', [
            'email' => 'HSEadmin',
            'password' => 'hsemurinda123',
        ]);

        $adminDashboard = $this->get('/dashboard');
        $adminDashboard->assertStatus(200);
        $adminDashboard->assertSee('SMT Terendah (Perminggu)');
        $adminDashboard->assertSee('Rekap Kehadiran SMT Mingguan dan Persentase Terendah');

        $adminAbsensi = $this->get('/absensi');
        $adminAbsensi->assertStatus(200);
        $adminAbsensi->assertSee('% Total SMT Terendah (Perminggu)');
        $adminAbsensi->assertSee('Rekapitulasi Kehadiran SMT Perminggu');

        // Logout
        $this->post('/logout');

        // 2. Role HSE Manager
        $this->post('/login', [
            'email' => 'HSEManager',
            'password' => 'hsemurinda123',
        ]);

        $managerDashboard = $this->get('/dashboard');
        $managerDashboard->assertStatus(200);
        $managerDashboard->assertSee('SMT Terendah (Perminggu)');
        $managerDashboard->assertSee('Rekap Kehadiran SMT Mingguan dan Persentase Terendah');

        $managerAbsensi = $this->get('/absensi');
        $managerAbsensi->assertStatus(200);
        $managerAbsensi->assertSee('% Total SMT Terendah (Perminggu)');
        $managerAbsensi->assertSee('Rekapitulasi Kehadiran SMT Perminggu');
    }

    public function test_worker_reactivation_requires_induction_ulang_and_resets_tgl_keluar()
    {
        $worker = SubcontPersonnel::create([
            'nama' => 'Pekerja Reaktivasi Test',
            'company_code' => 'HRD',
            'posisi' => 'Staff',
            'tgl_masuk' => '2026-01-01',
            'status' => 'Non-Aktif',
            'tgl_keluar' => '2026-06-01',
            'tgl_induction' => '2026-01-01',
        ]);

        $response = $this->put('/subcont/' . $worker->id, [
            'nama' => 'Pekerja Reaktivasi Test',
            'company_code' => 'HRD',
            'posisi' => 'Staff',
            'tgl_masuk' => '2026-01-01',
            'status' => 'Aktif',
            'tgl_induction_ulang' => '2026-09-17',
        ]);

        $response->assertRedirect('/monitoring/departemen?company=HRD');

        $worker->refresh();
        $this->assertEquals('Aktif', $worker->status);
        $this->assertNull($worker->tgl_keluar);
        $this->assertStringStartsWith('2026-09-17', (string)$worker->tgl_induction_ulang);
    }

    public function test_visitor_completion_updates_status_without_purging_record()
    {
        $visitor = SubcontPersonnel::create([
            'nama' => 'Bpk. Hendra Tamu',
            'company_code' => 'VISITOR',
            'asal_instansi' => 'PT Petrosea',
            'posisi' => 'Tamu Audit',
            'tgl_masuk' => '2026-09-17',
            'status' => 'Aktif',
        ]);

        // Uji route finish dengan tanggal selesai kustom
        $response = $this->post('/subcont/visitor/' . $visitor->id . '/finish', [
            'tgl_keluar' => '2026-09-20',
            'catatan_selesai' => 'Audit ISO selesai dengan sukses',
        ]);
        $response->assertRedirect('/monitoring/visitor?company=VISITOR');

        $visitor->refresh();
        $this->assertEquals('Non-Aktif', $visitor->status);
        $this->assertEquals('2026-09-20', $visitor->tgl_keluar);
        $this->assertDatabaseHas('subcont_personnels', [
            'id' => $visitor->id,
            'status' => 'Non-Aktif',
            'tgl_keluar' => '2026-09-20',
        ]);
    }

    public function test_pkl_intern_creation_with_placement_dept_and_null_tgl_keluar()
    {
        $response = $this->post('/subcont/visitor', [
            'nama' => 'Ahmad Siswa Magang',
            'company_code' => 'PKL',
            'asal_instansi' => 'SMKN 1 Subang',
            'departemen_penempatan' => 'QC',
            'posisi' => 'Staff Magang QC',
            'tgl_masuk' => '2026-09-01',
            'status' => 'Aktif',
            'keperluan' => 'Praktik Kerja Lapangan 3 Bulan',
        ]);

        $response->assertRedirect('/monitoring/visitor?company=PKL');

        $this->assertDatabaseHas('subcont_personnels', [
            'nama' => 'Ahmad Siswa Magang',
            'company_code' => 'PKL',
            'asal_instansi' => 'SMKN 1 Subang',
            'departemen_penempatan' => 'QC',
            'posisi' => 'Staff Magang QC',
            'status' => 'Aktif',
            'tgl_masuk' => '2026-09-01',
            'tgl_keluar' => null,
        ]);
    }

    public function test_intern_can_be_placed_in_any_department()
    {
        $response = $this->post('/subcont/visitor', [
            'nama' => 'Rina Magang Produksi',
            'company_code' => 'PKL',
            'asal_instansi' => 'Politeknik Negeri Subang',
            'departemen_penempatan' => 'PRODUKSI',
            'posisi' => 'Drafter Magang Produksi',
            'tgl_masuk' => '2026-09-10',
            'status' => 'Aktif',
            'keperluan' => 'Magang MBKM',
        ]);

        $response->assertRedirect('/monitoring/visitor?company=PKL');
        $this->assertDatabaseHas('subcont_personnels', [
            'nama' => 'Rina Magang Produksi',
            'departemen_penempatan' => 'PRODUKSI',
            'posisi' => 'Drafter Magang Produksi',
            'status' => 'Aktif',
        ]);
    }

    public function test_daily_man_hours_and_days_calculation_with_real_date_cutoff()
    {
        // Pekerja keluar tanggal 2026-09-07 di periode 2026-08-26 s/d 2026-09-25
        $calc = \App\Services\HseDepartmentService::calculateWorkerHoursAndDays(
            '2025-12-25',
            '2026-09-07',
            '2026-08-26',
            '2026-09-25'
        );

        // Dari 26 Ags s/d 7 Sep: 11 hari kerja riil, 73 jam
        $this->assertEquals(11, $calc['man_days']);
        $this->assertEquals(73, $calc['man_hours']);

        // Periode berikutnya (2026-09-26 s/d 2026-10-25) pekerja sudah keluar sebelumnya
        $nextPeriodCalc = \App\Services\HseDepartmentService::calculateWorkerHoursAndDays(
            '2025-12-25',
            '2026-09-07',
            '2026-09-26',
            '2026-10-25'
        );
        $this->assertEquals(0, $nextPeriodCalc['man_days']);
        $this->assertEquals(0, $nextPeriodCalc['man_hours']);
    }

    public function test_worker_periodic_status_history_is_independent_per_period()
    {
        $worker = \App\Models\Manpower::create([
            'no_id' => 'EMP-TEST-999',
            'nama' => 'Test Worker Period History',
            'departemen' => 'HRD',
            'jabatan' => 'Staff',
            'tgl_masuk' => '2025-01-01',
            'tgl_induction' => '2025-01-01',
            'tgl_keluar' => '2026-09-05',
            'status' => 'Non-Aktif',
        ]);

        // Di periode lampau (2026-07-26 s/d 2026-08-25) pekerja belum keluar -> Aktif
        $pastStatus = \App\Services\HseDepartmentService::getEffectiveWorkerStatusForPeriod(
            $worker,
            '2026-07-26',
            '2026-08-25',
            'P8'
        );
        $this->assertEquals('Aktif', $pastStatus);

        // Di periode masa depan (2026-09-26 s/d 2026-10-25) pekerja sudah keluar -> Non-Aktif
        $futureStatus = \App\Services\HseDepartmentService::getEffectiveWorkerStatusForPeriod(
            $worker,
            '2026-09-26',
            '2026-10-25',
            'P10'
        );
        $this->assertEquals('Non-Aktif', $futureStatus);
    }
}

