<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_activation_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manual_activation_id');
            $table->string('document_name');
            $table->string('file_path');
            $table->timestamps();

            $table->foreign('manual_activation_id')
                ->references('id')->on('manual_activations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_activation_documents');
    }
};
