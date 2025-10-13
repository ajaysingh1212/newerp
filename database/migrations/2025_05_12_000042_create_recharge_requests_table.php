<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargeRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('recharge_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vehicle_number');
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
