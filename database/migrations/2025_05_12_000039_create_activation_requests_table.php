<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivationRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('activation_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_name');
            $table->string('mobile_number')->unique();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->longText('address')->nullable();
            $table->date('request_date')->nullable();
            $table->string('vehicle_model');
            $table->string('vehicle_reg_no');
            $table->string('chassis_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
