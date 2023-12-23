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
        Schema::create('admin_employee', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('english_name', 40);
            $table->string('short_english', 40)->nullable();
            $table->tinyInteger('is_default');
            $table->string('arabic_name', 40)->nullable();
            $table->string('short_arabic', 40);
            $table->string('bangla_name', 40)->nullable();
            $table->string('short_bangla', 40);
            $table->tinyInteger('rating');
            $table->timestamp('created_on')->useCurrent();
            $table->tinyInteger('is_active');
            $table->tinyInteger('is_draft');
            $table->timestamp('last_updated')->nullable();
            $table->tinyInteger('is_delete');
            $table->tinyInteger('is_approved');
            $table->tinyInteger('is_pending');
            $table->tinyInteger('is_in_progress');
            $table->tinyInteger('is_rejected');
            $table->string('approved_by', 20);
            $table->timestamp('approved_date')->useCurrent();
            $table->string('rejected_by', 20);
            $table->timestamp('rejected_date')->useCurrent();
            $table->integer('action_user_id');
            $table->timestamp('action_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_employee');
    }
};
