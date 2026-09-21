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
        Schema::table('manpowers', function (Blueprint $table) {
            if (!Schema::hasColumn('manpowers', 'tgl_masuk')) {
                $table->date('tgl_masuk')->nullable()->after('tgl_lahir');
            }
            if (!Schema::hasColumn('manpowers', 'tgl_keluar')) {
                $table->date('tgl_keluar')->nullable()->after('tgl_induction_ulang');
            }
        });

        if (!Schema::hasTable('manpower_period_statuses')) {
            Schema::create('manpower_period_statuses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('manpower_id')->index();
                $table->string('period_key', 20)->index(); // e.g. 2026-P9
                $table->integer('period_year')->default(2026)->index();
                $table->integer('period_num')->default(1)->index();
                $table->string('status', 30)->default('Aktif'); // Aktif / Non-Aktif
                $table->date('tgl_masuk')->nullable();
                $table->date('tgl_keluar')->nullable();
                $table->date('tgl_induction_ulang')->nullable();
                $table->string('keterangan', 255)->nullable();
                $table->timestamps();

                $table->unique(['manpower_id', 'period_key'], 'uniq_mp_period');
                $table->foreign('manpower_id')->references('id')->on('manpowers')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manpower_period_statuses');

        Schema::table('manpowers', function (Blueprint $table) {
            if (Schema::hasColumn('manpowers', 'tgl_keluar')) {
                $table->dropColumn('tgl_keluar');
            }
            if (Schema::hasColumn('manpowers', 'tgl_masuk')) {
                $table->dropColumn('tgl_masuk');
            }
        });
    }
};
