<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reservations Report</title>
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
        <h2>Reservations Report</h2>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
        @if($startDate || $endDate)
            <p>Period: {{ $startDate ?? 'Beginning' }} to {{ $endDate ?? 'Now' }}</p>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Room Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $res)
            <tr>
                <td>{{ $res->id }}</td>
                <td>{{ $res->room_name }}</td>
                <td>{{ \Carbon\Carbon::parse($res->start_time)->format('Y-m-d H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($res->end_time)->format('Y-m-d H:i') }}</td>
                <td>{{ $res->status }}</td>
                <td>{{ $res->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
