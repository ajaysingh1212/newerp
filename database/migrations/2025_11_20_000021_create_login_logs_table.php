<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginLogsTable extends Migration
{
    public function up()
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip_address')->nullable();
            $table->string('device')->nullable();
            $table->string('location')->nullable();
            $table->string('logged_in_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
