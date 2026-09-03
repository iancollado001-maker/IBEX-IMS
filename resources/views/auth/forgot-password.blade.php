<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBEX IMS — Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-icon" style="width:52px;height:52px;border-radius:12px;font-size:24px;margin:0 auto 0.75rem;background:var(--ibex-accent);display:flex;align-items:center;justify-content:center;color:#fff;">
                <i class="bi bi-lock-fill"></i>
            </div>
            <div style="font-family:var(--font-display);font-weight:800;font-size:1.4rem;letter-spacing:2px;">Reset Password</div>
            <p style="font-size:12px;color:var(--ibex-text-muted);margin-top:6px;">Enter your email to receive a reset link.</p>
        </div>

        @if (session('status'))
        <div style="background:rgba(63,185,80,0.1);border:1px solid rgba(63,185,80,0.3);border-radius:8px;padding:0.75rem 1rem;margin-bottom:1rem;color:var(--ibex-success);font-size:13px;">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div style="background:rgba(248,81,73,0.1);border:1px solid rgba(248,81,73,0.3);border-radius:8px;padding:0.75rem 1rem;margin-bottom:1rem;color:var(--ibex-danger);font-size:13px;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" style="font-size:11.5px;font-weight:600;color:var(--ibex-text-muted);text-transform:uppercase;letter-spacing:0.8px;">Email Address</label>
                <input type="email" name="email" required autofocus
                    class="ibex-input form-control"
                    value="{{ old('email') }}"
                    placeholder="admin@ibex.local">
            </div>
            <button type="submit" class="btn-ibex btn-ibex-primary w-100" style="justify-content:center;">
                <i class="bi bi-send"></i> Send Reset Link
            </button>
        </form>

        <div style="text-align:center;margin-top:1.25rem;">
            <a href="{{ route('login') }}" style="font-size:12px;color:var(--ibex-accent);text-decoration:none;">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</div>
</body>
</html>
