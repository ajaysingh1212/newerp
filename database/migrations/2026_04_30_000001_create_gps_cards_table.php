<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gps_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('batch_code', 30)->index();
            $table->unsignedBigInteger('product_model_id');
            $table->string('card_number', 16)->unique();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('status', 20)->default('active')->index();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_model_id', 'gps_cards_product_model_fk')
                ->references('id')
                ->on('product_models');
            $table->foreign('created_by_id', 'gps_cards_created_by_fk')
                ->references('id')
                ->on('users');
            $table->foreign('team_id', 'gps_cards_team_fk')
                ->references('id')
                ->on('teams');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps_cards');
    }
};
