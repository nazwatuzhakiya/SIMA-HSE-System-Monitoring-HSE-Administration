<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Laporan Bahaya Publik
        Schema::create('laporan_bahayas', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->string('nama_pelapor')->default('Anonim');
            $table->string('area_lokasi');
            $table->string('foto_url')->nullable();
            $table->text('deskripsi');
            $table->enum('tingkat_risiko', ['Rendah', 'Sedang', 'Tinggi'])->default('Sedang');
            $table->enum('status', ['Menunggu', 'Diproses', 'Selesai'])->default('Menunggu');
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();
        });

        // 2. Subcont Monitoring
        Schema::create('subcont_personnels', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('company_code', ['DKP', 'SUM', 'AGS', 'PKN', 'CM', 'NC', 'BM', 'SBL'])->default('DKP');
            $table->date('tgl_masuk');
            $table->date('tgl_keluar')->nullable();
            $table->date('tgl_induction')->nullable();
            $table->enum('posisi', ['Foreman', 'Admin', 'Painter', 'Blaster', 'Helper'])->default('Helper');
            $table->enum('status', ['Aktif', 'Non-Aktif'])->default('Aktif');
            $table->text('domisili_alamat')->nullable();
            $table->string('domisili_desa')->nullable();
            $table->string('domisili_kec')->nullable();
            $table->string('domisili_kota')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 3. Master List of Manpower
        Schema::create('manpowers', function (Blueprint $table) {
            $table->id();
            $table->string('no_id')->unique();
            $table->string('nama');
            $table->date('tgl_induction');
            $table->string('departemen'); // Produksi, MSB, Built Up 9, Cutting Drilling, PPC, SD, QC, MNT, Warehouse, HR/GA, PJC, EXP, DKP, BM, SB, SUM, AGS, PKN, CM, NC, ENT, Projek
            $table->string('jabatan');
            $table->enum('status', ['Aktif', 'Non-Aktif'])->default('Aktif');
            $table->boolean('apd_helmet_issued')->default(true);
            $table->timestamps();
        });

        // 4. Absensi SMT & TBM
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_kegiatan', ['SMT', 'TBM'])->default('SMT');
            $table->date('tanggal');
            $table->string('departemen_subcont');
            $table->foreignId('manpower_id')->nullable()->constrained('manpowers')->onDelete('cascade');
            $table->string('nama_personel');
            $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Telat', 'Alpha'])->default('Hadir');
            $table->text('keterangan')->nullable();
            $table->text('alasan_perubahan')->nullable();
            $table->boolean('is_fixed')->default(false);
            $table->timestamps();
        });

        // 5. Logistik APD
        Schema::create('logistik_apds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manpower_id')->nullable()->constrained('manpowers')->onDelete('cascade');
            $table->string('nama_personel');
            $table->string('departemen_subcont');
            $table->date('tanggal_pengambilan');
            $table->text('jenis_apd'); // Comma-separated or JSON string
            $table->integer('jumlah')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 6. Klinik & Obat
        Schema::create('klinik_obats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');
            $table->string('nip')->nullable();
            $table->text('keluhan');
            $table->string('nama_obat');
            $table->string('jumlah_obat');
            $table->date('tanggal_pengambilan');
            $table->timestamps();
        });

        // 7. Mobile Audit 5R
        Schema::create('audit_5rs', function (Blueprint $table) {
            $table->id();
            $table->string('auditor_name');
            $table->string('departemen');
            $table->date('tanggal');
            $table->integer('score_ringkas')->default(8); // 1-10
            $table->integer('score_rapi')->default(8);    // 1-10
            $table->integer('score_resik')->default(8);   // 1-10
            $table->integer('score_rawat')->default(8);   // 1-10
            $table->integer('score_rajin')->default(8);   // 1-10
            $table->decimal('total_percentage', 5, 2)->default(80.00);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 8. Laporan Insiden K3
        Schema::create('insiden_k3s', function (Blueprint $table) {
            $table->id();
            $table->string('kode_insiden')->unique();
            $table->date('tanggal');
            $table->string('lokasi');
            $table->text('deskripsi_kejadian');
            $table->enum('kategori', ['Near-Miss', 'First Aid', 'LTI'])->default('Near-Miss');
            $table->string('nama_korban')->nullable();
            $table->string('nama_saksi')->nullable();
            $table->string('pj_departemen')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->enum('status', ['Open', 'Investigasi', 'Closed'])->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insiden_k3s');
        Schema::dropIfExists('audit_5rs');
        Schema::dropIfExists('klinik_obats');
        Schema::dropIfExists('logistik_apds');
        Schema::dropIfExists('absensis');
        Schema::dropIfExists('manpowers');
        Schema::dropIfExists('subcont_personnels');
        Schema::dropIfExists('laporan_bahayas');
    }
};
