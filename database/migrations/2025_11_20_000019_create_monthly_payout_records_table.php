<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyPayoutRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('monthly_payout_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('secure_interest_amount');
            $table->string('market_interest_amount');
            $table->decimal('total_payout_amount', 15, 2);
            $table->date('month_for');
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
