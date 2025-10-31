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
    Schema::create('delete_data', function (Blueprint $table) {
        $table->id();
        $table->string('user_name');
        $table->string('number')->nullable();
        $table->string('email')->nullable();
        $table->string('product')->nullable();
        $table->string('counter_name')->nullable();
        $table->string('vehicle_no')->nullable();
        $table->string('imei_no')->nullable();
        $table->string('vts_no')->nullable();
        $table->dateTime('delete_date')->nullable();
        $table->timestamps(); // includes created_at and updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delete_data');
    }
};
