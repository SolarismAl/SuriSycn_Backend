<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        if ($users->count() === 0) return;

        $requester = $users->last();
        $admin = $users->firstWhere('role', 'admin');

        \App\Models\Reservation::create([
            'room_name' => 'Conference Room A',
            'requested_by' => $requester->id,
            'start_time' => now()->addDays(2)->setHour(13)->setMinute(0),
            'end_time' => now()->addDays(2)->setHour(14)->setMinute(30),
            'status' => 'approved',
            'approved_by' => $admin->id ?? $users->first()->id,
        ]);

        \App\Models\Reservation::create([
            'room_name' => 'Meeting Room B',
            'requested_by' => $requester->id,
            'start_time' => now()->addDays(3)->setHour(9)->setMinute(0),
            'end_time' => now()->addDays(3)->setHour(10)->setMinute(0),
            'status' => 'pending',
            'approved_by' => null,
        ]);
    }
}
