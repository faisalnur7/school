<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Platform Admin</title>
    <style>
        body { margin:0; min-height:100vh; display:grid; place-items:center; background:#f1f5f9; color:#0f172a; font:16px/1.5 system-ui,sans-serif; }
        main { width:min(92vw, 460px); padding:2rem; background:#fff; border:1px solid #dbe4ee; border-radius:1rem; box-shadow:0 14px 35px rgba(15,23,42,.1); }
        h1 { margin-top:0; font-size:1.4rem; }
        label { display:block; margin:.9rem 0 .3rem; font-weight:700; }
        input { box-sizing:border-box; width:100%; padding:.7rem .8rem; border:1px solid #cbd5e1; border-radius:.5rem; font:inherit; }
        button { margin-top:1.2rem; width:100%; padding:.75rem; border:0; border-radius:.5rem; background:#0f172a; color:#fff; font-weight:700; cursor:pointer; }
        .error { color:#b91c1c; }
    </style>
</head>
<body>
<main>
    <h1>Create the first Platform Admin</h1>
    <p>This one-time setup creates the reserved platform role and its first account.</p>
    @if ($errors->any())
        <div class="error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('platform-admin.bootstrap', ['token' => $token]) }}">
        @csrf
        <label for="name">Name</label>
        <input id="name" name="name" value="{{ old('name') }}" required autofocus>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
        <button type="submit">Create Platform Admin</button>
    </form>
</main>
</body>
</html>
