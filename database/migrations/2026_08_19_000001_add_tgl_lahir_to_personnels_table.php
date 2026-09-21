<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subcont_personnels')) {
            Schema::table('subcont_personnels', function (Blueprint $table) {
                if (!Schema::hasColumn('subcont_personnels', 'tgl_lahir')) {
                    $table->date('tgl_lahir')->nullable()->after('nama');
                }
            });
        }

        if (Schema::hasTable('manpowers')) {
            Schema::table('manpowers', function (Blueprint $table) {
                if (!Schema::hasColumn('manpowers', 'tgl_lahir')) {
                    $table->date('tgl_lahir')->nullable()->after('nama');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subcont_personnels')) {
            Schema::table('subcont_personnels', function (Blueprint $table) {
                if (Schema::hasColumn('subcont_personnels', 'tgl_lahir')) {
                    $table->dropColumn('tgl_lahir');
                }
            });
        }

        if (Schema::hasTable('manpowers')) {
            Schema::table('manpowers', function (Blueprint $table) {
                if (Schema::hasColumn('manpowers', 'tgl_lahir')) {
                    $table->dropColumn('tgl_lahir');
                }
            });
        }
    }
};
