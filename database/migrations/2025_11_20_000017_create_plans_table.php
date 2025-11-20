<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('plan_name');
            $table->string('secure_interest_percent');
            $table->string('market_interest_percent');
            $table->string('total_interest_percent');
            $table->string('payout_frequency');
            $table->decimal('min_invest_amount', 15, 2);
            $table->decimal('max_invest_amount', 15, 2);
            $table->string('lockin_days');
            $table->string('withdraw_processing_hours');
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
