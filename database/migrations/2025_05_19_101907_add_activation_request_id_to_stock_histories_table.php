<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivationRequestIdToStockHistoriesTable extends Migration
{
    public function up()
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('activation_request_id')->nullable()->after('id');
            $table->foreign('activation_request_id')->references('id')->on('activation_requests')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropForeign(['activation_request_id']);
            $table->dropColumn('activation_request_id');
        });
    }
}
