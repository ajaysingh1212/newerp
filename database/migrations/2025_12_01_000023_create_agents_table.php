<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name');
            $table->string('phone_number')->unique();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->integer('pin_code');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->longText('present_address')->nullable();
            $table->longText('parmanent_address')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}