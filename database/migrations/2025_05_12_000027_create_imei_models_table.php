<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImeiModelsTable extends Migration
{
    public function up()
    {
        Schema::create('imei_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('imei_model_number')->unique();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
