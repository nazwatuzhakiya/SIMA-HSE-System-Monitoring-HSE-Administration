<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE absensis MODIFY COLUMN status_kehadiran VARCHAR(50) DEFAULT 'Hadir'");
        }
    }

    public function down()
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE absensis MODIFY COLUMN status_kehadiran ENUM('Hadir', 'Izin', 'Sakit', 'Telat', 'Alpha') DEFAULT 'Hadir'");
        }
    }
};
