<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Name of the task
            $table->text('description')->nullable(); // Description of the task (optional)
            $table->enum('status', ['in progress', 'pending', 'complete', 'in review']); // Status of the task
            $table->enum('priority', ['high', 'normal', 'low']); // Priority of the task
            $table->json('emp'); // Array of employee names assigned to the task
            $table->text('review_details')->nullable(); // Review details (optional)
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
        Schema::dropIfExists('tasks');
    }
}
