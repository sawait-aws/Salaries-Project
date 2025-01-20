<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seeding some tasks with user_id (emp) as 1, 8, 85, and 34
        Task::create([
            'name' => 'Fix critical bug',
            'description' => 'Fix the bug that causes the app to crash when opening the dashboard.',
            'status' => 'pending',
            'priority' => 'high',
            'emp' => [1, 8], // Assigning to users with IDs 1 and 8
            'review_details' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Task::create([
            'name' => 'Design new landing page',
            'description' => 'Create a new landing page for the website.',
            'status' => 'in progress',
            'priority' => 'normal',
            'emp' => [8, 85], // Assigning to users with IDs 8 and 85
            'review_details' => null,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        Task::create([
            'name' => 'Update employee records',
            'description' => 'Update employee records in the system.',
            'status' => 'complete',
            'priority' => 'low',
            'emp' => [34], // Assigning to user with ID 34
            'review_details' => 'Task completed successfully.',
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        Task::create([
            'name' => 'Implement new feature',
            'description' => 'Work on implementing a new feature in the app.',
            'status' => 'in progress',
            'priority' => 'high',
            'emp' => [1, 85], // Assigning to users with IDs 1 and 85
            'review_details' => null,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        Task::create([
            'name' => 'Test bug fixes',
            'description' => 'Test all the bug fixes before release.',
            'status' => 'pending',
            'priority' => 'normal',
            'emp' => [8, 34], // Assigning to users with IDs 8 and 34
            'review_details' => null,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ]);
    }
}
