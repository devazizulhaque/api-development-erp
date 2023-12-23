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
        Schema::create('admin_currency_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('currency_id');
            $table->tinyInteger('status_type_id');
            $table->tinyInteger('approved_type_id');
            $table->smallInteger('action_user_id');
            $table->timestamp('action_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_currency_history');
    }
};
