<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToImeiMastersTable extends Migration
{
    public function up()
    {
        Schema::table('imei_masters', function (Blueprint $table) {
            $table->unsignedBigInteger('imei_model_id')->nullable();
            $table->foreign('imei_model_id', 'imei_model_fk_10562896')->references('id')->on('imei_models');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_10562903')->references('id')->on('teams');
        });
    }
}
