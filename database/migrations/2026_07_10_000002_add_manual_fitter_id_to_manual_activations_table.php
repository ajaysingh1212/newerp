<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('manual_activations', function (Blueprint $table) {
            // Nullable at DB level (so existing rows do not break); enforced as
            // required at the form-validation layer in ManualActivationController.
            $table->unsignedBigInteger('manual_fitter_id')->nullable()->after('manual_party_id');

            $table->foreign('manual_fitter_id')
                ->references('id')->on('manual_fitters')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('manual_activations', function (Blueprint $table) {
            $table->dropForeign(['manual_fitter_id']);
            $table->dropColumn('manual_fitter_id');
        });
    }
};
