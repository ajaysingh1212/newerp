<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reg')->nullable();
            $table->string('referral_code')->nullable();
            $table->string('aadhaar_number')->unique();
            $table->string('pan_number')->unique();
            $table->date('dob')->nullable();
            $table->string('gender');
            $table->string('father_name')->nullable();
            $table->longText('address_line_1');
            $table->longText('address_line_2')->nullable();
            $table->string('pincode');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('bank_account_holder_name');
            $table->string('bank_account_number')->unique();
            $table->string('ifsc_code');
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('income_range');
            $table->string('occupation');
            $table->string('risk_profile')->nullable();
            $table->string('investment_experience')->nullable();
            $table->string('kyc_status')->nullable();
            $table->string('account_status')->nullable();
            $table->string('is_email_verified')->nullable();
            $table->string('is_phone_verified')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
