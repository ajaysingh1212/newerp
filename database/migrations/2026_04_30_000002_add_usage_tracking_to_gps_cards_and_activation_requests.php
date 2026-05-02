<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gps_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('used_by_id')->nullable()->after('created_by_id');
            $table->unsignedBigInteger('used_by_activation_request_id')->nullable()->after('used_by_id');
            $table->string('card_holder_name')->nullable()->after('used_by_activation_request_id');
            $table->timestamp('used_at')->nullable()->after('card_holder_name');
            $table->timestamp('printed_at')->nullable()->after('used_at');
            $table->unsignedBigInteger('printed_by_id')->nullable()->after('printed_at');

            $table->foreign('used_by_id', 'gps_cards_used_by_fk')->references('id')->on('users');
            $table->foreign('used_by_activation_request_id', 'gps_cards_used_by_activation_fk')->references('id')->on('activation_requests');
            $table->foreign('printed_by_id', 'gps_cards_printed_by_fk')->references('id')->on('users');
        });

        Schema::table('activation_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('gps_card_id')->nullable()->after('product_id');
            $table->foreign('gps_card_id', 'activation_requests_gps_card_fk')->references('id')->on('gps_cards');
        });
    }

    public function down(): void
    {
        Schema::table('activation_requests', function (Blueprint $table) {
            $table->dropForeign('activation_requests_gps_card_fk');
            $table->dropColumn('gps_card_id');
        });

        Schema::table('gps_cards', function (Blueprint $table) {
            $table->dropForeign('gps_cards_used_by_fk');
            $table->dropForeign('gps_cards_used_by_activation_fk');
            $table->dropForeign('gps_cards_printed_by_fk');
            $table->dropColumn([
                'used_by_id',
                'used_by_activation_request_id',
                'card_holder_name',
                'used_at',
                'printed_at',
                'printed_by_id',
            ]);
        });
    }
};
