<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedeemCodesTable extends Migration
{
    public function up()
    {
        Schema::create('redeem_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('recharge_plan_id')->constrained('recharge_plans')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->date('valid_up_to');
            $table->enum('discount_type', ['flat', 'percent']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('use_status', ['not_used', 'used'])->default('not_used');
            $table->foreignId('used_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recharge_request_id')->nullable()->constrained('recharge_requests')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('creator_name')->nullable();
            $table->string('creator_role')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('redeem_codes');
    }
}
