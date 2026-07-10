<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('manual_fitters', function (Blueprint $table) {
            $table->id();

            // Basic Details
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('photo_path')->nullable();

            // Contact Details
            $table->string('alternate_phone', 20)->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('aadhar_number', 20)->nullable();
            $table->string('id_proof_path')->nullable();
            $table->text('address')->nullable();
            $table->string('landmark')->nullable();

            // Address Details (required)
            $table->string('state');
            $table->string('district');
            $table->string('city');
            $table->string('pincode', 10);

            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['state', 'district', 'city']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('manual_fitters');
    }
};
