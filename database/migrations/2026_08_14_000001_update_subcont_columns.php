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
        Schema::table('subcont_personnels', function (Blueprint $table) {
            $table->string('company_code')->change();
            $table->string('posisi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcont_personnels', function (Blueprint $table) {
            $table->string('company_code')->change();
            $table->string('posisi')->change();
        });
    }
};
