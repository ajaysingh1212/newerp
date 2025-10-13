<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToRechargePlansTable extends Migration
{
    public function up()
    {
        Schema::table('recharge_plans', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_10564476')->references('id')->on('teams');
        });
    }
}
