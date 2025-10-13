<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_sharing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('sharing_user_id'); // User jisko share kiya
            $table->unsignedBigInteger('created_by')->nullable(); // original owner
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('add_customer_vehicles')->onDelete('cascade');
            $table->foreign('sharing_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_sharing');
    }
};
