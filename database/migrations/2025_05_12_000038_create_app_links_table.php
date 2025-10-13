<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppLinksTable extends Migration
{
    public function up()
    {
        Schema::create('app_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('link')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
