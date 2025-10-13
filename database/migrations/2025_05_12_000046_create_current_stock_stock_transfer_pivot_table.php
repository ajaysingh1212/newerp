<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentStockStockTransferPivotTable extends Migration
{
    public function up()
    {
        Schema::create('current_stock_stock_transfer', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_transfer_id');
            $table->foreign('stock_transfer_id', 'stock_transfer_id_fk_10564075')->references('id')->on('stock_transfers')->onDelete('cascade');
            $table->unsignedBigInteger('current_stock_id');
            $table->foreign('current_stock_id', 'current_stock_id_fk_10564075')->references('id')->on('current_stocks')->onDelete('cascade');
        });
    }
}
