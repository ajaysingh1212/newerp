<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddCustomerVehicleCheckComplainPivotTable extends Migration
{
    public function up()
    {
        Schema::create('add_customer_vehicle_check_complain', function (Blueprint $table) {
            $table->unsignedBigInteger('check_complain_id');
            $table->foreign('check_complain_id', 'check_complain_id_fk_10576956')->references('id')->on('check_complains')->onDelete('cascade');
            $table->unsignedBigInteger('add_customer_vehicle_id');
            $table->foreign('add_customer_vehicle_id', 'add_customer_vehicle_id_fk_10576956')->references('id')->on('add_customer_vehicles')->onDelete('cascade');
        });
    }
}
