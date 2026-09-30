<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_activations', function (Blueprint $table) {
            $table->unsignedBigInteger('manual_app_id')->nullable()->after('manual_product_id');
            $table->string('vts_number', 100)->nullable()->after('vehicle_color');
            $table->string('sim_number', 25)->nullable()->after('vts_number');
            $table->enum('sim_company', ['airtel', 'jio', 'vi'])->nullable()->after('sim_number');

            $table->foreign('manual_app_id', 'fk_ma_app')->references('id')->on('manual_apps')->nullOnDelete();
            $table->index(['manual_app_id', 'sim_company'], 'idx_ma_app_sim_company');
        });
    }

    public function down(): void
    {
        Schema::table('manual_activations', function (Blueprint $table) {
            $table->dropForeign('fk_ma_app');
            $table->dropIndex('idx_ma_app_sim_company');
            $table->dropColumn(['manual_app_id', 'vts_number', 'sim_number', 'sim_company']);
        });
    }
};
