<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>Offline - {{ config('app.name', 'Inventory System') }}</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/favicon-32x32.png">
    <link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 1rem;
            max-width: 32rem;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        }
        .icon-circle {
            width: 5rem;
            height: 5rem;
            margin: 0 auto 1.5rem;
            border-radius: 9999px;
            background: rgba(239, 68, 68, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f87171;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            margin-bottom: 1rem;
        }
        .pulse-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background-color: #ef4444;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }
        p {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }
        .feature-box {
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.75rem;
            text-align: left;
            font-size: 0.85rem;
            color: #cbd5e1;
        }
        .feature-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .feature-box li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .feature-box li:last-child {
            margin-bottom: 0;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        @media (min-width: 640px) {
            .btn-group {
                flex-direction: row;
            }
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            flex: 1;
            border: none;
        }
        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .btn-secondary {
            background-color: #334155;
            color: #f1f5f9;
        }
        .btn-secondary:hover {
            background-color: #475569;
        }
        .online-banner {
            display: none;
            background-color: #10b981;
            color: #ffffff;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div id="onlineNotice" class="online-banner">
            Connection Restored! Reloading page...
        </div>

        <div class="icon-circle">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 4.243a9 9 0 01-12.728 0m0 0l2.829-2.829m-2.829 2.829L3 21m5.636-12.728a5 5 0 017.072 0m-7.072 0L3 3m12.728 5.636L21 3M1 1l22 22"></path>
            </svg>
        </div>

        <div class="badge">
            <span class="pulse-dot"></span>
            <span>Offline Mode</span>
        </div>

        <h1>No Internet Connection</h1>
        <p>You are currently offline or unable to reach the server. Cached static assets and the offline POS register remain accessible.</p>

        <div class="feature-box">
            <ul>
                <li>
                    <svg width="18" height="18" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Offline POS records sales to IndexedDB</span>
                </li>
                <li>
                    <svg width="18" height="18" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Automatic sync when network reconnects</span>
                </li>
                <li>
                    <svg width="18" height="18" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Zero duplicate sales with UUID idempotency</span>
                </li>
            </ul>
        </div>

        <div class="btn-group">
            <button onclick="window.location.reload()" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Retry Connection
            </button>
            <a href="/sales/create" class="btn btn-secondary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Open Offline POS
            </a>
        </div>
    </div>

    <script>
        window.addEventListener('online', function() {
            var notice = document.getElementById('onlineNotice');
            if (notice) {
                notice.style.display = 'block';
            }
            setTimeout(function() {
                window.location.reload();
            }, 1200);
        });
    </script>
</body>
</html>

