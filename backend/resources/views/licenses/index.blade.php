<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Offline-First Activation Server Simulator</title>
    <style>
        :root {
            --primary: #002B93;
            --accent: #CC5900;
            --bg: #F4F6F9;
            --card-bg: #FFFFFF;
            --text-dark: #1E293B;
            --text-muted: #64748B;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg);
            color: var(--text-dark);
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        h1 {
            color: var(--primary);
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 43, 147, 0.1), 0 2px 4px -1px rgba(0, 43, 147, 0.06);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 43, 147, 0.15), 0 4px 6px -2px rgba(0, 43, 147, 0.05);
            filter: brightness(1.1);
        }

        .btn-accent {
            background-color: var(--accent);
            box-shadow: 0 4px 6px -1px rgba(204, 89, 0, 0.1), 0 2px 4px -1px rgba(204, 89, 0, 0.06);
        }

        .btn-accent:hover {
            box-shadow: 0 10px 15px -3px rgba(204, 89, 0, 0.15), 0 4px 6px -2px rgba(204, 89, 0, 0.05);
        }

        .btn-danger {
            background-color: var(--danger);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1);
        }

        .btn-danger:hover {
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.15);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 8px;
        }

        .alert {
            background-color: #DEF7EC;
            color: #03543F;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            color: var(--text-muted);
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 2px solid #E2E8F0;
            font-size: 14px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #F1F5F9;
            font-size: 14px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-available {
            background-color: #E0F2FE;
            color: #0369A1;
        }

        .badge-active {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-revoked {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .badge-expired {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .text-mono {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
            background-color: #F8FAFC;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #E2E8F0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>🔑 License Server Simulator</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0 0;">Demo & Activation management dashboard for Offline-First SaaS simulation</p>
            </div>
            <form action="{{ route('licenses.generate') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-accent">✨ Generate New Key</button>
            </form>
        </header>

        @if(session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>License Key</th>
                        <th>Registered Device ID</th>
                        <th>Status</th>
                        <th>Expiration Date</th>
                        <th>Action (Simulation)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licenses as $license)
                        <tr>
                            <td>
                                <span class="text-mono">{{ $license->license_key }}</span>
                            </td>
                            <td>
                                @if($license->device_id)
                                    <span style="font-weight: 500;">{{ $license->device_id }}</span>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Not registered yet</span>
                                @endif
                            </td>
                            <td>
                                @if($license->expires_at->isPast())
                                    <span class="badge badge-expired">EXPIRED</span>
                                @elseif($license->status === 'AVAILABLE')
                                    <span class="badge badge-available">AVAILABLE</span>
                                @elseif($license->status === 'ACTIVE')
                                    <span class="badge badge-active">ACTIVE</span>
                                @else
                                    <span class="badge badge-revoked">REVOKED</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 500; color: {{ $license->expires_at->isPast() ? 'var(--danger)' : 'var(--text-dark)' }}">
                                    {{ $license->expires_at->format('Y-m-d H:i:s') }}
                                </span>
                                @if($license->expires_at->isPast())
                                    <div style="font-size: 11px; color: var(--danger); font-weight: 600; margin-top: 2px;">Expired</div>
                                @else
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ $license->expires_at->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td>
                                @if(!$license->expires_at->isPast())
                                    <form action="{{ route('licenses.force-expire', $license->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">⚡ Force Expire</button>
                                    </form>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px; font-style: italic;">No actions</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                No licenses generated yet. Click "Generate New Key" above to start.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
