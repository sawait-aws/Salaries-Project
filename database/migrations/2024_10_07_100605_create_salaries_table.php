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
            $table->unsignedInteger('user_id');  // Foreign key should match the users table (unsigned)
            $table->integer('year');  // Year of the salary
            $table->integer('month');  // Month of the salary
            $table->decimal('gross_salary', 20, 2);  // Gross salary
            $table->decimal('commission', 20, 2)->default(0);  // Commission
            $table->decimal('salaf', 20, 2)->default(0);  // Advance payment
            $table->decimal('salaf_deducted', 20, 2)->default(0);  // Salaf deducted
            $table->integer('working_days')->default(0);
            $table->integer('unpaid_days')->default(0);
            $table->integer('sick_leave')->default(0);
            $table->decimal('deduction',20,2)->default(0);
            $table->decimal('bonus',20,2)->default(0);
            $table->decimal('salary_to_be_paid', 20, 2);  // Final salary to be paid
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            $table->unique(['user_id', 'year', 'month']);  // Ensure unique entry per user, year, and month
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
