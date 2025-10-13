<?php

// database/migrations/xxxx_xx_xx_create_complain_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('complain_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamps();

            // (Optional) Foreign key constraint if teams table exists
            // $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complain_categories');
    }
}
