<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargePlansTable extends Migration
{
    public function up()
    {
        Schema::create('recharge_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->string('plan_name');
            $table->string('duration_months');
            $table->string('duration_days');
            $table->string('price');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
