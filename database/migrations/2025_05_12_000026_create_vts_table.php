<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVtsTable extends Migration
{
    public function up()
    {
        Schema::create('vts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vts_number')->unique();
            $table->string('sim_number')->unique();
            $table->string('operator');
            $table->string('product_status');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
