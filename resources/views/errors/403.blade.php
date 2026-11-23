@php
    $user = auth()->user();
    $isStudent = $user && strcasecmp((string) optional($user->role)->name, 'Student') === 0;
    $canSeeDashboard = $user && ! $isStudent && $user->hasPermission('view_dashboard');
    $schoolName = optional(\App\Models\SchoolSetting::current())->name ?: config('app.name');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>No access · {{ $schoolName }}</title>
    <style>
        :root { --ink: #0f172a; --muted: #475569; --line: #e2e8f0; --bg: #f1f5f9; --card: #ffffff; --brand: #166534; --brand-ink: #ffffff; }
        @media (prefers-color-scheme: dark) {
            :root { --ink: #e2e8f0; --muted: #94a3b8; --line: #334155; --bg: #0f172a; --card: #1e293b; --brand: #22c55e; --brand-ink: #052e16; }
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 16px;
               background: var(--bg); color: var(--ink); font: 16px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .card { width: 100%; max-width: 440px; background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 28px 24px; }
        .code { font-size: 13px; font-weight: 700; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; margin: 0 0 8px; }
        h1 { font-size: 22px; line-height: 1.3; margin: 0 0 8px; }
        p { margin: 0 0 16px; color: var(--muted); }
        .who { font-size: 14px; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; margin-bottom: 20px; }
        .who strong { color: var(--ink); }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0 18px; border-radius: 8px;
               font: inherit; font-weight: 600; text-decoration: none; cursor: pointer; border: 1px solid var(--line); background: transparent; color: var(--ink); }
        .btn-primary { background: var(--brand); border-color: var(--brand); color: var(--brand-ink); }
        form { margin: 0; }
    </style>
</head>
<body>
    <main class="card" role="main">
        <p class="code">403 · No access</p>

        @if ($isStudent)
            <h1>Student accounts can't use the web portal</h1>
            <p>Results, attendance, fees and notices for students are in the school's mobile app. Sign in there with the same account.</p>
        @else
            <h1>You don't have access to this page</h1>
            <p>Your account doesn't have permission to open <strong>{{ '/' . ltrim(request()->path(), '/') }}</strong>. If you need it, ask a school administrator to update your role.</p>
        @endif

        @if ($user)
            <div class="who">Signed in as <strong>{{ $user->name }}</strong>@if ($user->role) · {{ $user->role->name }}@endif</div>
        @endif

        <div class="actions">
            @if ($canSeeDashboard)
                <a class="btn btn-primary" href="{{ route('dashboard') }}">Go to dashboard</a>
            @endif

            @if ($user)
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn {{ $canSeeDashboard ? '' : 'btn-primary' }}">Log out</button>
                </form>
            @else
                <a class="btn btn-primary" href="{{ route('login') }}">Sign in</a>
            @endif
        </div>
    </main>
</body>
</html>
