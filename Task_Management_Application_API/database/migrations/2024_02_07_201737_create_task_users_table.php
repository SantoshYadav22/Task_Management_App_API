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
        Schema::create('task_users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('task_id');

            // Define foreign key for user
            $table->unsignedBigInteger('user_id');

            // Add timestamps (created_at and updated_at)
            $table->timestamps();

            // Define foreign key constraints
            // $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Define composite primary key
            // $table->primary(['task_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_users');
    }
};
