<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('stock_histories', function (Blueprint $table) {
        $table->string('product_name')->nullable();
        $table->string('product_brand')->nullable();
        $table->string('product_model')->nullable();
        $table->string('product_serial')->nullable();
        $table->integer('product_qty')->nullable();
    });
}

public function down()
{
    Schema::table('stock_histories', function (Blueprint $table) {
        $table->dropColumn([
            'product_name',
            'product_brand',
            'product_model',
            'product_serial',
            'product_qty',
        ]);
    });
}

};
