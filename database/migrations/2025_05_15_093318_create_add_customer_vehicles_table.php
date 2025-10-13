<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddCustomerVehiclesTable extends Migration
{
    public function up()
    {
        Schema::create('add_customer_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vehicle_number')->unique();
            $table->string('owners_name');
            $table->date('insurance_expiry_date');
            $table->string('chassis_number')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('owner_image')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
