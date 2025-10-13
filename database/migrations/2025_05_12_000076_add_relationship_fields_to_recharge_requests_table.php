<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToRechargeRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('recharge_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_10564701')->references('id')->on('users');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_10564703')->references('id')->on('current_stocks');
            $table->unsignedBigInteger('select_recharge_id')->nullable();
            $table->foreign('select_recharge_id', 'select_recharge_fk_10564704')->references('id')->on('recharge_plans');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_10564710')->references('id')->on('teams');
        });
    }
}
