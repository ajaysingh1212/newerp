<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckPartyStockUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('check_party_stock_user', function (Blueprint $table) {
            $table->unsignedBigInteger('check_party_stock_id');
            $table->foreign('check_party_stock_id', 'check_party_stock_id_fk_10564082')->references('id')->on('check_party_stocks')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_10564082')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
