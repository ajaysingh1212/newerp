<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestmentsTable extends Migration
{
    public function up()
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('principal_amount', 15, 2);
            $table->string('secure_interest_percent')->nullable();
            $table->string('market_interest_percent')->nullable();
            $table->string('total_interest_percent')->nullable();
            $table->date('start_date');
            $table->date('lockin_end_date')->nullable();
            $table->date('next_payout_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
