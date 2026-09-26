<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Task Assigned</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            padding: 32px 40px;
            color: white;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.8;
            font-size: 14px;
        }
        .content {
            padding: 32px 40px;
        }
        .content h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 8px;
            color: #0f172a;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #475569;
            margin: 0 0 16px;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white !important;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .button:hover {
            background: #1d4ed8;
            text-decoration: none;
        }
        .footer {
            padding: 24px 40px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
        .task-details {
            background: #f8fafc;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
        }
        .task-details p {
            margin: 4px 0;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            margin-bottom: 8px;
        }
        @media (max-width: 600px) {
            .header { padding: 24px; }
            .content { padding: 24px; }
            .footer { padding: 16px 24px; }
            .button { display: block; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="badge">📋 New Task</div>
                <h1>You have been assigned a new task!</h1>
                <p>Project: {{ $task?->project?->name ?? 'N/A' }}</p>
            </div>

            <div class="content">
                <h2>Hello, {{ $user->name }}! 👋</h2>
                <p>
                    <strong>{{ $assigner?->name ?? 'Your team leader' }}</strong> has assigned you a new task.
                </p>

                <div class="task-details">
                    <p style="font-weight: 600; font-size: 16px;">
                        🎯 {{ $task?->title ?? 'Task' }}
                    </p>
                    @if($task?->description)
                        <p style="color: #64748b; font-size: 14px;">
                            {{ $task->description }}
                        </p>
                    @endif
                    <div style="margin-top: 8px; display: flex; gap: 16px; font-size: 14px; color: #64748b;">
                        @if($task?->due_date)
                            <span>🗓️ Due: {{ $task->due_date->format('M d, Y') }}</span>
                        @endif
                        @if($task?->priority)
                            <span>⚡ Priority: {{ ucfirst($task->priority) }}</span>
                        @endif
                    </div>
                </div>

                <div style="margin-top: 24px;">
                    <a href="{{ route('tasks.submit', $task) }}" class="button">
                        View Task →
                    </a>
                </div>

                <div style="margin-top: 16px; font-size: 14px; color: #94a3b8;">
                    💡 You can view all your tasks in the dashboard.
                </div>
            </div>

            <div class="footer">
                <p>
                    <a href="{{ route('profile.edit') }}">Manage notification preferences</a>
                    &bull;
                    <a href="{{ route('dashboard') }}">Go to Dashboard</a>
                </p>
                <p style="margin-top: 8px;">&copy; {{ date('Y') }} EqualFlow. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>