<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('u_properties', function (Blueprint $table) {
            $table->id()->autoIncrement()->comment('識別子');
            $table->unsignedBigInteger('user_id')->default(1)->comment('内部用ユーザ識別子');
            $table->unsignedSmallInteger('energy')->default(1)->comment('エナジー');
            $table->unsignedSmallInteger('energy_max_regen')->default(1)->comment('エナジー最大値（自動回復）');
            $table->unsignedSmallInteger('energy_max_stock')->default(1)->comment('エナジー最大値（所持）');
            $table->unsignedInteger('credit_free')->default(0)->comment('クレジット（無償）');
            $table->unsignedInteger('credit_paid')->default(0)->comment('クレジット（有償）');
            $table->unsignedInteger('credit_max_stock')->default(999999)->comment('クレジット最大値（所持）');
            $table->timestamps();

            $table->unique(['user_id'], 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_properties');
    }
};
