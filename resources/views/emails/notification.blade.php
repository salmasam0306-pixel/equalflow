<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EqualFlow Notification</title>
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
            background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
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
            background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
            color: white !important;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-top: 8px;
        }
        .button:hover {
            background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
            text-decoration: none;
        }
        .footer {
            padding: 24px 40px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
        .footer a {
            color: #1a2a4a;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .logo {
            font-size: 28px;
            font-weight: 800;
            color: white;
            text-decoration: none;
        }
        .logo span {
            color: #93b4e8;
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
        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 24px 0;
        }
        .meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: #94a3b8;
            margin: 16px 0;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
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
            <!-- Header -->
            <div class="header">
                <a href="{{ url('/') }}" class="logo">
                    Equal<span>Flow</span>
                </a>
                @if(isset($type))
                    <div class="badge">{{ ucfirst(str_replace('_', ' ', $type)) }}</div>
                @endif
                <h1>{{ $title }}</h1>
                @if(isset($data['project_name']))
                    <p>Project: {{ $data['project_name'] }}</p>
                @endif
            </div>

            <!-- Content -->
            <div class="content">
                <h2>Hello, {{ $user->name }}! 👋</h2>
                <p>{{ $message }}</p>

                @if(isset($data['task']))
                    <div style="background: #f8fafc; border-radius: 8px; padding: 16px; margin: 16px 0;">
                        <p style="margin: 0; font-weight: 600;">Task: {{ $data['task']->title ?? 'N/A' }}</p>
                        @if(isset($data['task']->description))
                            <p style="margin: 4px 0 0; font-size: 14px; color: #64748b;">
                                {{ Str::limit($data['task']->description, 100) }}
                            </p>
                        @endif
                    </div>
                @endif

                @if(isset($actionUrl))
                    <div style="margin-top: 24px;">
                        <a href="{{ $actionUrl }}" class="button">
                            {{ $actionText ?? 'View Details' }}
                        </a>
                    </div>
                @endif

                @if(isset($data['additional_info']))
                    <div class="divider"></div>
                    <div style="font-size: 14px; color: #64748b;">
                        {!! $data['additional_info'] !!}
                    </div>
                @endif

                @if(isset($data['due_date']))
                    <div class="meta">
                        <span class="meta-item">
                            🗓️ Due: {{ \Carbon\Carbon::parse($data['due_date'])->format('M d, Y') }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>
                    You received this email because you have notifications enabled in EqualFlow.
                    <br>
                    <a href="{{ route('profile.edit') }}">Manage your notification preferences</a>
                    &bull;
                    <a href="{{ route('dashboard') }}">Go to Dashboard</a>
                </p>
                <p style="margin-top: 8px;">
                    &copy; {{ date('Y') }} EqualFlow. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>