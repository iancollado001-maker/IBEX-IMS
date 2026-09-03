<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBEX IMS — New Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo" style="text-align:center;margin-bottom:1.5rem;">
            <div style="font-family:var(--font-display);font-weight:800;font-size:1.4rem;letter-spacing:2px;">Set New Password</div>
        </div>

        @if ($errors->any())
        <div style="background:rgba(248,81,73,0.1);border:1px solid rgba(248,81,73,0.3);border-radius:8px;padding:0.75rem;margin-bottom:1rem;color:var(--ibex-danger);font-size:13px;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-3">
                <label class="form-label" style="font-size:11.5px;font-weight:600;color:var(--ibex-text-muted);text-transform:uppercase;letter-spacing:0.8px;">Email</label>
                <input type="email" name="email" required class="ibex-input form-control" value="{{ old('email', $request->email) }}">
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:11.5px;font-weight:600;color:var(--ibex-text-muted);text-transform:uppercase;letter-spacing:0.8px;">New Password</label>
                <input type="password" name="password" required class="ibex-input form-control" placeholder="Min 8 characters">
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:11.5px;font-weight:600;color:var(--ibex-text-muted);text-transform:uppercase;letter-spacing:0.8px;">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="ibex-input form-control">
            </div>
            <button type="submit" class="btn-ibex btn-ibex-primary w-100" style="justify-content:center;">
                <i class="bi bi-check-circle"></i> Reset Password
            </button>
        </form>
    </div>
</div>
</body>
</html>
