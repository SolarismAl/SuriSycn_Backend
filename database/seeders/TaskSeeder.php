<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        if ($users->count() === 0) return;

        $admin = $users->firstWhere('role', 'admin') ?? $users->first();
        $staff = $users->last();

        \App\Models\Task::create([
            'title' => 'Prepare Monthly Report',
            'description' => 'Gather data and prepare the monthly government operations report.',
            'priority' => 'high',
            'progress' => 50,
            'due_date' => now()->addDays(5),
            'assigned_to' => $staff->id,
            'created_by' => $admin->id,
            'status' => 'in_progress',
        ]);

        \App\Models\Task::create([
            'title' => 'Update Department Directory',
            'description' => 'Review and update the contact list for all departments.',
            'priority' => 'medium',
            'progress' => 0,
            'due_date' => now()->addDays(10),
            'assigned_to' => $staff->id,
            'created_by' => $admin->id,
            'status' => 'pending',
        ]);
    }
}
