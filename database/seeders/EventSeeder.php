<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $departments = \App\Models\Department::all();

        if ($users->count() === 0 || $departments->count() === 0) return;

        $admin = $users->firstWhere('role', 'admin') ?? $users->first();

        $event = \App\Models\Event::create([
            'title' => 'Weekly Sync',
            'description' => 'Weekly team synchronization meeting.',
            'start_date' => now()->addDays(1)->setHour(10)->setMinute(0),
            'end_date' => now()->addDays(1)->setHour(11)->setMinute(0),
            'recurrence' => 'weekly',
            'color' => '#ff0000',
            'created_by' => $admin->id,
            'department_id' => $departments->first()->id,
        ]);

        $syncData = [];
        foreach ($users->pluck('id')->take(3) as $userId) {
            $syncData[$userId] = ['id' => \Illuminate\Support\Str::uuid()->toString()];
        }
        $event->taggedUsers()->sync($syncData);
    }
}
