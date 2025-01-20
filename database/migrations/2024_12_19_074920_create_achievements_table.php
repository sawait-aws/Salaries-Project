<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->integer('year'); // Year of achievement
            $table->integer('month'); // Month of achievement
            $table->unsignedInteger('employee_of_the_month')->nullable(); // Employee of the Month ID
            $table->unsignedInteger('top_atv')->nullable(); // Top ATV ID
            $table->unsignedInteger('top_performer')->nullable(); // Top Performer ID
            $table->unsignedInteger('top_quality')->nullable(); // Top Quality ID
            $table->unsignedInteger('top_upselling')->nullable(); // Top Upselling ID
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('achievements');
    }
}
