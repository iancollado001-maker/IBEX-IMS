<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>IBEX IMS — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>

<div class="auth-container">
    <!-- Background grid pattern -->
    <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(249,115,22,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(249,115,22,0.04) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;"></div>

    <div class="auth-card">
        <!-- Logo -->
        <div class="auth-logo">
            <div class="logo-icon" style="width:52px;height:52px;border-radius:12px;font-size:24px;margin:0 auto 0.75rem;background:var(--ibex-accent);display:flex;align-items:center;justify-content:center;color:#fff;">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div style="font-family:var(--font-display);font-weight:800;font-size:1.8rem;letter-spacing:3px;color:var(--ibex-text);">IBEX</div>
            <div style="font-size:11px;font-weight:600;color:var(--ibex-text-muted);letter-spacing:2px;text-transform:uppercase;margin-top:2px;">Inventory Management System</div>
        </div>

        <div style="border-bottom:1px solid var(--ibex-border);margin-bottom:1.5rem;"></div>

        <!-- Validation Errors -->
        <?php if($errors->any()): ?>
        <div style="background:rgba(248,81,73,0.1);border:1px solid rgba(248,81,73,0.3);border-radius:var(--radius);padding:0.75rem 1rem;margin-bottom:1rem;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="color:var(--ibex-danger);font-size:13px;"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($error); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <!-- Session Status -->
        <?php if(session('status')): ?>
        <div style="background:rgba(63,185,80,0.1);border:1px solid rgba(63,185,80,0.3);border-radius:var(--radius);padding:0.75rem 1rem;margin-bottom:1rem;color:var(--ibex-success);font-size:13px;">
            <?php echo e(session('status')); ?>

        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label class="form-label" style="font-size:11.5px;font-weight:600;color:var(--ibex-text-muted);text-transform:uppercase;letter-spacing:0.8px;">Email Address</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--ibex-text-muted);font-size:14px;">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                        class="ibex-input form-control"
                        style="padding-left:2.2rem !important;"
                        placeholder="admin@ibex.local">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-size:11.5px;font-weight:600;color:var(--ibex-text-muted);text-transform:uppercase;letter-spacing:0.8px;">Password</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--ibex-text-muted);font-size:14px;">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="passwordField" required
                        class="ibex-input form-control"
                        style="padding-left:2.2rem !important;"
                        placeholder="••••••••">
                    <button type="button" id="togglePwd"
                        style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:transparent;border:none;color:var(--ibex-text-muted);cursor:pointer;padding:0;font-size:14px;">
                        <i class="bi bi-eye" id="pwdIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ibex-text-muted);cursor:pointer;">
                    <input type="checkbox" name="remember"
                        style="accent-color:var(--ibex-accent);width:14px;height:14px;">
                    Remember me
                </label>
                <?php if(Route::has('password.request')): ?>
                <a href="<?php echo e(route('password.request')); ?>"
                    style="font-size:12px;color:var(--ibex-accent);text-decoration:none;">Forgot password?</a>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-ibex btn-ibex-primary w-100" style="justify-content:center;padding:0.65rem;">
                <i class="bi bi-box-arrow-in-right"></i> Sign In to IBEX
            </button>
        </form>

        <div style="margin-top:1.5rem;text-align:center;font-size:11px;color:var(--ibex-text-dim);">
            Default: admin@ibex.local / password
        </div>
    </div>
</div>

<script>
document.getElementById('togglePwd').addEventListener('click', function () {
    const f = document.getElementById('passwordField');
    const i = document.getElementById('pwdIcon');
    if (f.type === 'password') {
        f.type = 'text';
        i.className = 'bi bi-eye-slash';
    } else {
        f.type = 'password';
        i.className = 'bi bi-eye';
    }
});
</script>
</body>
</html>
<?php /**PATH C:\Users\my pc\Desktop\IBEX1\ibex-ims\resources\views/auth/login.blade.php ENDPATH**/ ?>