<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivationRequestAttachVeichlePivotTable extends Migration
{
    public function up()
    {
        Schema::create('activation_request_attach_veichle', function (Blueprint $table) {
            $table->unsignedBigInteger('attach_veichle_id');
            $table->foreign('attach_veichle_id', 'attach_veichle_id_fk_10564377')->references('id')->on('attach_veichles')->onDelete('cascade');
            $table->unsignedBigInteger('activation_request_id');
            $table->foreign('activation_request_id', 'activation_request_id_fk_10564377')->references('id')->on('activation_requests')->onDelete('cascade');
        });
    }
}
