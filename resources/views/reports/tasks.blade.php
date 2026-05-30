<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tasks Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Tasks Report</h2>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
        @if($startDate || $endDate)
            <p>Period: {{ $startDate ?? 'Beginning' }} to {{ $endDate ?? 'Now' }}</p>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Assignee</th>
                <th>Created At</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td>{{ $task->task_number }}</td>
                <td>{{ $task->title }}</td>
                <td>{{ $task->status }}</td>
                <td>{{ $task->priority }}</td>
                <td>{{ $task->assignee ? $task->assignee->first_name . ' ' . $task->assignee->last_name : 'Unassigned' }}</td>
                <td>{{ $task->created_at->format('Y-m-d') }}</td>
                <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
