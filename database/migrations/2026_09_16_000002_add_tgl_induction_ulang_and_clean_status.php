<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subcont_personnels')) {
            Schema::table('subcont_personnels', function (Blueprint $table) {
                if (!Schema::hasColumn('subcont_personnels', 'tgl_induction_ulang')) {
                    $table->date('tgl_induction_ulang')->nullable()->after('tgl_induction');
                }
            });
        }

        if (Schema::hasTable('manpowers')) {
            Schema::table('manpowers', function (Blueprint $table) {
                if (!Schema::hasColumn('manpowers', 'tgl_induction_ulang')) {
                    $table->date('tgl_induction_ulang')->nullable()->after('tgl_induction');
                }
            });
        }

        // Convert existing On-Off records to Non-Aktif
        DB::table('subcont_personnels')->where('status', 'On-Off')->update(['status' => 'Non-Aktif']);
        DB::table('manpowers')->where('status', 'On-Off')->update(['status' => 'Non-Aktif']);
    }

    public function down(): void
    {
        if (Schema::hasTable('subcont_personnels')) {
            Schema::table('subcont_personnels', function (Blueprint $table) {
                if (Schema::hasColumn('subcont_personnels', 'tgl_induction_ulang')) {
                    $table->dropColumn('tgl_induction_ulang');
                }
            });
        }

        if (Schema::hasTable('manpowers')) {
            Schema::table('manpowers', function (Blueprint $table) {
                if (Schema::hasColumn('manpowers', 'tgl_induction_ulang')) {
                    $table->dropColumn('tgl_induction_ulang');
                }
            });
        }
    }
};
