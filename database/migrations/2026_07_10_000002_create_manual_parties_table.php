<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_parties', function (Blueprint $table) {
            $table->id();

            // Step 1 - Basic Info
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');

            // Step 2 - Location (required)
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('city_id');
            $table->string('pincode')->nullable();
            $table->text('address')->nullable();

            // Step 3 - GST / PAN
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();

            // Step 4 - Account Details
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('branch_name')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_parties');
    }
};
