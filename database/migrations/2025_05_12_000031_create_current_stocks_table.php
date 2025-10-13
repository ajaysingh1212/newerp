<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentStocksTable extends Migration
{
    public function up()
    {
        Schema::create('current_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sku')->unique();
            $table->string('product_name');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
