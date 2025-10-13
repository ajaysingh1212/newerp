<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImeiMastersTable extends Migration
{
    public function up()
    {
        Schema::create('imei_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('imei_number')->unique();
            $table->string('status');
            $table->string('product_status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
