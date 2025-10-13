<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_recharges', function (Blueprint $table) {
            $table->id();

            // Relation fields
            $table->unsignedBigInteger('user_id'); 
            $table->unsignedBigInteger('vehicle_id')->nullable(); // if vehicle table used
            $table->string('vehicle_number')->nullable(); // for direct vehicle number reference

            // Main fields
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Payment fields
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->decimal('payment_amount', 10, 2)->default(0.00);
            $table->dateTime('payment_date')->nullable();

            // Created by (Admin or User)
            $table->unsignedBigInteger('created_by_id')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_recharges');
    }
};
