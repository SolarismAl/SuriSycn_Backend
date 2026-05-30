<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Ensure the user is an admin or manager before allowing report generation.
     */
    private function checkAccess(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized access to reports.');
        }
    }

    public function exportTasks(Request $request)
    {
        $this->checkAccess($request);

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $format = $request->query('format', 'csv');

        $query = Task::with(['assignee', 'creator']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.tasks', ['tasks' => $tasks, 'startDate' => $startDate, 'endDate' => $endDate]);
            return $pdf->download('tasks_report.pdf');
        }

        // CSV Export fallback
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=tasks_report.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Task Number', 'Title', 'Status', 'Priority', 'Assigned To', 'Created By', 'Created At', 'Due Date']);

            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->task_number,
                    $task->title,
                    $task->status,
                    $task->priority,
                    $task->assignee ? $task->assignee->first_name . ' ' . $task->assignee->last_name : 'Unassigned',
                    $task->creator ? $task->creator->first_name . ' ' . $task->creator->last_name : 'System',
                    $task->created_at->format('Y-m-d H:i'),
                    $task->due_date ? Carbon::parse($task->due_date)->format('Y-m-d') : 'N/A'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportReservations(Request $request)
    {
        $this->checkAccess($request);

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $format = $request->query('format', 'csv');

        $query = Reservation::query();

        if ($startDate) {
            $query->whereDate('start_time', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('start_time', '<=', $endDate);
        }

        $reservations = $query->orderBy('start_time', 'desc')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.reservations', ['reservations' => $reservations, 'startDate' => $startDate, 'endDate' => $endDate]);
            return $pdf->download('reservations_report.pdf');
        }

        // CSV Export fallback
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=reservations_report.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($reservations) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Room Name', 'Start Time', 'End Time', 'Status', 'Created At']);

            foreach ($reservations as $res) {
                fputcsv($file, [
                    $res->id,
                    $res->room_name,
                    Carbon::parse($res->start_time)->format('Y-m-d H:i'),
                    Carbon::parse($res->end_time)->format('Y-m-d H:i'),
                    $res->status,
                    $res->created_at->format('Y-m-d H:i')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
