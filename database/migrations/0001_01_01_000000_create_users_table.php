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
        Schema::create('users', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->string('first_name')->nullable();  // Optional first name
            $table->string('last_name')->nullable();   // Optional last name
            $table->integer('user_id')->unique();  // Unique user ID for login
            $table->string('password');  // Password
            $table->string('role');  // Role: manager or employee
            $table->rememberToken();  // To remember the user session
            $table->timestamps();  // Created at and updated at timestamps
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();  // Email for password reset
            $table->string('token');  // Reset token
            $table->timestamp('created_at')->nullable();  // Timestamp for token creation
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();  // Primary key for session ID
            $table->foreignId('user_id')->nullable()->index();  // Foreign key to users table
            $table->string('ip_address', 45)->nullable();  // IP address of the session
            $table->text('user_agent')->nullable();  // User agent of the session
            $table->longText('payload');  // Session data
            $table->integer('last_activity')->index();  // Last activity timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');  // Drop the users table
        Schema::dropIfExists('password_reset_tokens');  // Drop the password reset tokens table
        Schema::dropIfExists('sessions');  // Drop the sessions table
    }
};
