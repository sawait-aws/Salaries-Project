<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('user_id')->constrained('users');  // Reference to users table
            $table->integer('year');  // Year of the salary
            $table->integer('month');  // Month of the salary
            $table->decimal('gross_salary', 20, 2);  // Gross salary
            $table->decimal('commission', 20, 2)->default(0);  // Commission
            $table->decimal('salaf', 20, 2)->default(0);  // Advance payment
            $table->decimal('salaf_deducted', 20, 2)->default(0);  // Salaf deducted
            $table->decimal('salary_to_be_paid', 20, 2);  // Final salary to be paid
            $table->timestamps();

            $table->unique(['user_id', 'year', 'month']);  // Ensure unique entry per user, year, and month
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
