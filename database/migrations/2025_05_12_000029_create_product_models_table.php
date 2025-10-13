<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductModelsTable extends Migration
{
    public function up()
    {
        Schema::create('product_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_model');
            $table->string('warranty');
            $table->string('subscription');
            $table->string('amc');
            $table->string('mrp');
            $table->string('cnf_price');
            $table->string('distributor_price');
            $table->string('dealer_price');
            $table->string('customer_price');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
