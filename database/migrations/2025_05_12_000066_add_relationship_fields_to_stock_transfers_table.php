<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToStockTransfersTable extends Migration
{
    public function up()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('select_user_id')->nullable();
            $table->foreign('select_user_id', 'select_user_fk_10564074')->references('id')->on('roles');
            $table->unsignedBigInteger('reseller_id')->nullable();
            $table->foreign('reseller_id', 'reseller_fk_10570176')->references('id')->on('users');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_10564079')->references('id')->on('teams');
        });
    }
}
