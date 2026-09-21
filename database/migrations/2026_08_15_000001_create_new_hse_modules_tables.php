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
        // 1. Master Subcont Companies
        if (!Schema::hasTable('subcont_companies')) {
            Schema::create('subcont_companies', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('plant_location')->default('Plant Kalijati Subang');
                $table->enum('status', ['Aktif', 'Non-Aktif'])->default('Aktif');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Subcont History Logs (Rename, Transfer, Promotion)
        if (!Schema::hasTable('subcont_histories')) {
            Schema::create('subcont_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subcont_personnel_id')->nullable()->constrained('subcont_personnels')->onDelete('cascade');
                $table->string('nama_pekerja');
                $table->string('change_type'); // 'Transfer Subcont', 'Kenaikan Jabatan', 'Ganti Nama Kontraktor'
                $table->string('old_value')->nullable();
                $table->string('new_value')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Schedule Pelaksanaan Audit
        if (!Schema::hasTable('audit_schedules')) {
            Schema::create('audit_schedules', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun')->default(2026);
                $table->date('update_date')->nullable();
                $table->string('departemen');
                $table->string('lead_auditor');
                $table->string('auditor');
                $table->string('wakil_auditor')->nullable();
                $table->string('jan_date')->nullable();
                $table->string('feb_date')->nullable();
                $table->string('mar_date')->nullable();
                $table->string('apr_date')->nullable();
                $table->string('may_date')->nullable();
                $table->string('jun_date')->nullable();
                $table->string('jul_date')->nullable();
                $table->string('aug_date')->nullable();
                $table->string('sep_date')->nullable();
                $table->string('oct_date')->nullable();
                $table->string('nov_date')->nullable();
                $table->string('dec_date')->nullable();
                $table->string('status')->default('Terjadwal');
                $table->timestamps();
            });
        }

        // 4. Activity Schedules (Kalender Beranda)
        if (!Schema::hasTable('activity_schedules')) {
            Schema::create('activity_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->date('date');
                $table->string('time')->nullable();
                $table->string('location')->nullable();
                $table->string('category')->default('Kegiatan HSE'); // SMT, TBM, Audit, Training, Inspection, Meeting
                $table->enum('status', ['Planned', 'In Progress', 'Completed', 'Delayed', 'Cancelled'])->default('Planned');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 5. Monitoring Training ERP
        if (!Schema::hasTable('training_erps')) {
            Schema::create('training_erps', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pekerja');
                $table->string('subcont_dept');
                $table->enum('jenis_team', ['Kebakaran', 'P3K', 'Evakuasi', 'Umum'])->default('Umum');
                $table->enum('struktur', ['Ya', 'Tidak'])->default('Ya');
                $table->text('pernyataan')->nullable();
                $table->date('tgl_training')->nullable();
                $table->timestamps();
            });
        }

        // 6. Join Safety Patrol (JSP)
        if (!Schema::hasTable('join_safety_patrols')) {
            Schema::create('join_safety_patrols', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->string('area_patrol');
                $table->string('tim_patroli');
                // 6 Kategori Penilaian OK / Tidak OK
                $table->enum('kat1_kondisi_umum', ['OK', 'Tidak OK'])->default('OK');
                $table->text('catatan1')->nullable();
                $table->enum('kat2_peralatan_mesin', ['OK', 'Tidak OK'])->default('OK');
                $table->text('catatan2')->nullable();
                $table->enum('kat3_penataan_material', ['OK', 'Tidak OK'])->default('OK');
                $table->text('catatan3')->nullable();
                $table->enum('kat4_instalasi_listrik', ['OK', 'Tidak OK'])->default('OK');
                $table->text('catatan4')->nullable();
                $table->enum('kat5_sarana_darurat', ['OK', 'Tidak OK'])->default('OK');
                $table->text('catatan5')->nullable();
                $table->enum('kat6_alat_angkat', ['OK', 'Tidak OK'])->default('OK');
                $table->text('catatan6')->nullable();
                $table->enum('status', ['Selesai', 'Perlu Tindak Lanjut'])->default('Selesai');
                $table->timestamps();
            });
        }

        // 7. Dynamic columns on laporan_bahayas for Whistleblowing
        if (Schema::hasTable('laporan_bahayas')) {
            Schema::table('laporan_bahayas', function (Blueprint $table) {
                if (!Schema::hasColumn('laporan_bahayas', 'kategori_lapor')) {
                    $table->string('kategori_lapor')->default('Bahaya K3')->after('ticket_code');
                }
            });
        }

        // 8. Add plant_location column to manpower & subcont if missing
        if (Schema::hasTable('subcont_personnels')) {
            Schema::table('subcont_personnels', function (Blueprint $table) {
                if (!Schema::hasColumn('subcont_personnels', 'plant_location')) {
                    $table->string('plant_location')->default('Plant Kalijati')->after('company_code');
                }
            });
        }

        if (Schema::hasTable('manpowers')) {
            Schema::table('manpowers', function (Blueprint $table) {
                if (!Schema::hasColumn('manpowers', 'plant_location')) {
                    $table->string('plant_location')->default('Plant Kalijati')->after('departemen');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_safety_patrols');
        Schema::dropIfExists('training_erps');
        Schema::dropIfExists('activity_schedules');
        Schema::dropIfExists('audit_schedules');
        Schema::dropIfExists('subcont_histories');
        Schema::dropIfExists('subcont_companies');
    }
};
