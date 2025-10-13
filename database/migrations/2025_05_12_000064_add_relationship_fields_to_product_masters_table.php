<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToProductMastersTable extends Migration
{
    public function up()
    {
        Schema::table('product_masters', function (Blueprint $table) {
            $table->unsignedBigInteger('product_model_id')->nullable();
            $table->foreign('product_model_id', 'product_model_fk_10562921')->references('id')->on('product_models');
            $table->unsignedBigInteger('imei_id')->nullable();
            $table->foreign('imei_id', 'imei_fk_10562922')->references('id')->on('imei_masters');
            $table->unsignedBigInteger('vts_id')->nullable();
            $table->foreign('vts_id', 'vts_fk_10562923')->references('id')->on('vts');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_10562931')->references('id')->on('teams');
        });
    }
}
