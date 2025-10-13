<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckComplainComplainCategoryPivotTable extends Migration
{
    public function up()
    {
        Schema::create('check_complain_complain_category', function (Blueprint $table) {
            $table->unsignedBigInteger('check_complain_id');
            $table->unsignedBigInteger('complain_category_id');

            $table->foreign('check_complain_id', 'check_complain_id_fk_10576954')
                ->references('id')->on('check_complains')->onDelete('cascade');

            $table->foreign('complain_category_id', 'complain_category_id_fk_10576954')
                ->references('id')->on('complain_categories')->onDelete('cascade');

            $table->primary(['check_complain_id', 'complain_category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('check_complain_complain_category');
    }
}
