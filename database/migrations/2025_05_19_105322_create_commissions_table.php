<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recharge_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('dealer_id')->nullable()->constrained('users');
            $table->foreignId('distributor_id')->nullable()->constrained('users');
            
            // Add vehicle_id foreign key to add_customer_vehicles table
            $table->foreignId('vehicle_id')->nullable()->constrained('add_customer_vehicles');

            $table->decimal('dealer_commission', 10, 2)->default(0);
            $table->decimal('distributor_commission', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
