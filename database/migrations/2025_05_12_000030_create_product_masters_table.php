<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMastersTable extends Migration
{
    public function up()
    {
        Schema::create('product_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('warranty');
            $table->string('subscription');
            $table->string('amc');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
