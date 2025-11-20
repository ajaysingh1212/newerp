<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestorTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('investor_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_type');
            $table->decimal('amount', 15, 2);
            $table->longText('narration')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
