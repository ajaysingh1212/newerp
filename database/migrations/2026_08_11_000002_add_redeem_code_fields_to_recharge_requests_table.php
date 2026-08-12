<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRedeemCodeFieldsToRechargeRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('recharge_requests', function (Blueprint $table) {
            $table->foreignId('redeem_code_id')->nullable()->after('redeem_amount')->constrained('redeem_codes')->nullOnDelete();
            $table->string('redeem_code')->nullable()->after('redeem_code_id');
            $table->decimal('redeem_code_discount', 10, 2)->default(0)->after('redeem_code');
        });
    }

    public function down()
    {
        Schema::table('recharge_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('redeem_code_id');
            $table->dropColumn(['redeem_code', 'redeem_code_discount']);
        });
    }
}
