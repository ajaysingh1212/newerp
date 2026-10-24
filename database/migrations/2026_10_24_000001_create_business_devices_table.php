<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_party_id')->constrained('manual_parties')->restrictOnDelete();
            $table->foreignId('manual_product_id')->constrained('manual_products')->restrictOnDelete();
            $table->string('vts_number', 100)->unique();
            $table->string('sim_number', 100);
            $table->decimal('sales_price', 12, 2);
            $table->date('assigned_at')->index();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
            $table->index(['manual_party_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_devices');
    }
};
