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
        Schema::create('admin_leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('english_name');
            $table->string('short_english');
            $table->tinyInteger('is_default');
            $table->string('arabic_name')->nullable();
            $table->string('short_arabic');
            $table->string('bangla_name')->nullable();
            $table->string('short_bangla');
            $table->tinyInteger('rating');
            $table->tinyInteger('is_active');
            $table->tinyInteger('is_draft');
            $table->timestamp('last_updated')->nullable();
            $table->tinyInteger('is_delete');
            $table->integer('action_user_id');
            $table->timestamp('action_date')->useCurrent();
            $table->timestamp('created_on')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_leave_types');
    }
};
