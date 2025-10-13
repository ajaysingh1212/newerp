<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToAttachVeichlesTable extends Migration
{
    public function up()
    {
        Schema::table('attach_veichles', function (Blueprint $table) {
            $table->unsignedBigInteger('select_user_id')->nullable();
            $table->foreign('select_user_id', 'select_user_fk_10564376')->references('id')->on('users');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_10564381')->references('id')->on('teams');
        });
    }
}
