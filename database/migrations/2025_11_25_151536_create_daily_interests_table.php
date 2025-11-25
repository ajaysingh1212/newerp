<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('daily_interests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('investment_id');
            $table->unsignedBigInteger('investor_id');
            $table->unsignedBigInteger('plan_id');

            $table->decimal('principal_amount', 15, 2);
            $table->decimal('daily_interest_amount', 15, 2);

            $table->date('interest_date');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('investment_id')->references('id')->on('investments')->onDelete('cascade');
            $table->foreign('investor_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_interests');
    }
};
