<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckComplainsTable extends Migration
{
    public function up()
    {
        Schema::create('check_complains', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_number')->unique();
            $table->string('poduct_name');
            $table->string('vehicle_no');
            $table->string('customer_name');
            $table->string('phone_number')->unique();
            $table->longText('reason');
            $table->string('status');
            $table->longText('notes')->nullable();
            $table->longText('admin_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
