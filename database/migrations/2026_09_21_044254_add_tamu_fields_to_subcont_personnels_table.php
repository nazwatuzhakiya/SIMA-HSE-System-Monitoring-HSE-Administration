 sahboard <?php

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
            $table->string('asal_instansi')->nullable()->after('company_code');
            $table->string('departemen_penempatan')->nullable()->after('asal_instansi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcont_personnels', function (Blueprint $table) {
            $table->dropColumn(['asal_instansi', 'departemen_penempatan']);
        });
    }
};
