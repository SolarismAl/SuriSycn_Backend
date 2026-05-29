<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Announcement;
use App\Models\Reservation;
use App\Models\Task;
use App\Models\User;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends BaseApiController
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $now   = Carbon::now();

        // --- Stats ---
        $totalEmployees   = User::count();
        $pendingReservations = Reservation::where('status', 'pending')->count();

        // Tasks progress: average progress across all tasks (0-100)
        $tasksAvgProgress = Task::avg('progress') ?? 0;

        // Today's reservations (approved ones starting today)
        $todayReservationsCount = Reservation::whereDate('start_time', $today)
            ->where('status', 'approved')
            ->count();

        // --- Today's Schedule: upcoming/ongoing approved reservations today ---
        $todaySchedule = Reservation::with('requester')
            ->whereDate('start_time', $today)
            ->where('status', 'approved')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // --- Pending Approvals (pending reservations) ---
        $pendingApprovals = Reservation::with('requester')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // --- Announcements ---
        $announcements = Announcement::orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // --- Recent Tasks ---
        $recentTasks = Task::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return $this->successResponse([
            'stats' => [
                'total_employees'       => $totalEmployees,
                'pending_approvals'     => $pendingReservations,
                'tasks_progress'        => round($tasksAvgProgress),
                'today_reservations'    => $todayReservationsCount,
            ],
            'today_schedule'   => ReservationResource::collection($todaySchedule),
            'pending_approvals' => ReservationResource::collection($pendingApprovals),
            'announcements'    => AnnouncementResource::collection($announcements),
            'recent_tasks'     => TaskResource::collection($recentTasks),
        ], 'Dashboard data retrieved successfully');
    }
}
