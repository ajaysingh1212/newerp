<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToActivationRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('activation_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('party_type_id')->nullable();
            $table->foreign('party_type_id', 'party_type_fk_10564298')->references('id')->on('roles');
            $table->unsignedBigInteger('select_party_id')->nullable();
            $table->foreign('select_party_id', 'select_party_fk_10564299')->references('id')->on('users');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_10564300')->references('id')->on('current_stocks');
            $table->unsignedBigInteger('state_id')->nullable();
            $table->foreign('state_id', 'state_fk_10564305')->references('id')->on('states');
            $table->unsignedBigInteger('disrict_id')->nullable();
            $table->foreign('disrict_id', 'disrict_fk_10564306')->references('id')->on('districts');
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->foreign('vehicle_type_id', 'vehicle_type_fk_10564310')->references('id')->on('vehicle_types');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id', 'team_fk_10564322')->references('id')->on('teams');
        });
    }
}
