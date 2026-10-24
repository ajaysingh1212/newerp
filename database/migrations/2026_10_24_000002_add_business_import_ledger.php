<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_imports', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('sha256', 64)->unique();
            $table->string('archive_path');
            $table->json('summary');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('business_import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_import_id')->constrained('business_imports')->restrictOnDelete();
            $table->string('sheet', 100);
            $table->unsignedInteger('row_number');
            $table->foreignId('manual_party_id')->nullable()->constrained('manual_parties')->restrictOnDelete();
            $table->foreignId('business_device_id')->nullable()->constrained('business_devices')->nullOnDelete();
            $table->string('kind', 30)->index();
            $table->string('vts_number', 100)->nullable()->index();
            $table->json('source_cells');
            $table->json('normalized');
            $table->json('issues');
            $table->boolean('needs_review')->default(false)->index();
            $table->unique(['business_import_id', 'sheet', 'row_number'], 'business_import_source_row_unique');
        });
        Schema::table('business_devices', function (Blueprint $table) {
            $table->foreignId('manual_product_id')->nullable()->change();
            $table->string('vts_number', 100)->nullable()->change();
            $table->string('sim_number', 100)->nullable()->change();
            $table->decimal('sales_price', 12, 2)->nullable()->change();
            $table->date('assigned_at')->nullable()->change();
            $table->string('imei_number', 100)->nullable()->index();
            $table->foreignId('business_import_id')->nullable()->constrained('business_imports')->restrictOnDelete();
        });
        Schema::table('manual_parties', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('business_devices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_import_id');
            $table->dropColumn('imei_number');
        });
        Schema::dropIfExists('business_import_rows');
        Schema::dropIfExists('business_imports');
        // Imported unknown fields intentionally remain nullable; no fabricated backfill.
    }
};
