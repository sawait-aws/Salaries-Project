<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('days_off_requests', function (Blueprint $table) {
            $table->id(); // Auto-increment ID
            $table->unsignedBigInteger('user_id'); // User ID as foreign key
            $table->string('position');
            $table->string('first_name');
            $table->string('last_name');
            $table->text('emp_notes')->nullable(); // Employee's notes
            $table->date('date'); // Date for the days off request
            $table->text('manager_notes')->nullable(); // Manager's notes
            $table->text('top_manager_notes')->nullable(); // Top manager's notes
            $table->enum('status', [
                'Requested', 
                'managerApprove', 
                'TopManagerApprove', 
                'managerReject', 
                'TopManagerReject'
            ])->default('Requested');
            $table->enum('day_off_kind', ['Not Paid', 'Yearly', 'Sick']); // Status with a default value
            $table->string('proof')->nullable(); // File path for uploaded proof
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('days_off_requests');
    }
};
