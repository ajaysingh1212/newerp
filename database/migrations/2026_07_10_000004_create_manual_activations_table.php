<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_activations', function (Blueprint $table) {
            $table->id();

            // Step 1 - Party & Product
            $table->unsignedBigInteger('manual_party_id');
            $table->unsignedBigInteger('manual_product_id');
            $table->date('fitting_date');

            // Step 2 - Customer Details
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();

            // Step 3 - Vehicle Details
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_chassis_number')->nullable();
            $table->string('vehicle_engine_number')->nullable();
            $table->string('vehicle_color')->nullable();

            // Step 4 - Documents
            $table->string('aadhar_front_path')->nullable();
            $table->string('aadhar_back_path')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys (Custom Names)
            $table->foreign('manual_party_id', 'fk_ma_party')
                ->references('id')
                ->on('manual_parties')
                ->onDelete('cascade');

            $table->foreign('manual_product_id', 'fk_ma_product')
                ->references('id')
                ->on('manual_products')
                ->onDelete('cascade');

            // Custom Short Index Name
            $table->index(
                ['manual_party_id', 'manual_product_id', 'fitting_date'],
                'idx_ma_party_product_date'
            );
        });
    }

    public function down(): void
    {
        Schema::table('manual_activations', function (Blueprint $table) {
            $table->dropForeign('fk_ma_party');
            $table->dropForeign('fk_ma_product');
            $table->dropIndex('idx_ma_party_product_date');
        });

        Schema::dropIfExists('manual_activations');
    }
};
