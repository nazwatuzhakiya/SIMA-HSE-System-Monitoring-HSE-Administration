<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter status column to VARCHAR(50) so it supports 'Aktif', 'On-Off', 'Non-Aktif'
        try {
            DB::statement("ALTER TABLE `manpowers` MODIFY `status` VARCHAR(50) DEFAULT 'Aktif'");
            DB::statement("ALTER TABLE `subcont_personnels` MODIFY `status` VARCHAR(50) DEFAULT 'Aktif'");
        } catch (\Exception $e) {
            // If SQLite or other driver, fallback
        }
    }

    public function down(): void
    {
    }
};
