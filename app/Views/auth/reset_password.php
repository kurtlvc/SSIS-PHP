<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CuyoTech University Student Services Information System">
    <meta name="theme-color" content="#0d9488">
    <link rel="icon" type="image/webp" href="/ssis/assets/cuyotech-logo-900x900.webp">
    <title>Reset Password - <?= htmlspecialchars($system_title ?? $config['system_title'] ?? 'CuyoSSIS') ?></title>
    <link href="/ssis/assets/vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/ssis/assets/vendor/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/ssis/assets/css/style.css" rel="stylesheet">
    <style>
        :root { --teal:#0d9488; --teal-dark:#0f766e; --navy:#0f172a; --gray-50:#f8fafc; --gray-200:#e2e8f0; --gray-500:#64748b; --white:#fff; }
        html,body { font-family:'Inter','Segoe UI',Tahoma,Geneva,Verdana,sans-serif; margin:0; height:100vh; overflow:hidden; background:var(--gray-50); }
        .login-split { display:flex; flex-direction:column; height:100vh; overflow:hidden; }
        @media (min-width:992px){ .login-split{flex-direction:row;} }
        .login-left { position:relative; background-image:url('/ssis/assets/cuyotech-bg-1200x675.webp'); background-size:cover; background-position:center; background-repeat:no-repeat; flex:0 0 100%; min-height:320px; }
        @media (min-width:992px){ .login-left{flex:0 0 53%; min-height:100vh;} }
        .login-left::before{content:'';position:absolute;inset:0;background:linear-gradient(145deg,rgba(15,23,42,.55),rgba(15,23,42,.35));z-index:1;}
        .login-left .content-wrapper{position:relative;z-index:2;height:100%;display:flex;flex-direction:column;justify-content:center;padding:3rem 2.5rem;}
        @media (min-width:992px){ .login-left .content-wrapper{padding:4rem 3.5rem;} }
        .info-card{background:rgba(15,23,42,.72);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.12);border-radius:1.25rem;max-width:420px;margin:0 auto;padding:2.5rem 2rem;text-align:left;}
        @media (min-width:992px){ .info-card{margin:0;max-width:none;width:100%;} }
        .info-card .logo{width:56px;height:56px;margin-bottom:1.25rem;}
        .info-card .brand{font-weight:700;font-size:.85rem;letter-spacing:.08em;color:rgba(255,255,255,.7);text-transform:uppercase;margin-bottom:.5rem;}
        .info-card .title{font-weight:800;font-size:2rem;line-height:1.2;color:var(--white);margin-bottom:.5rem;}
        .info-card .subtitle{font-weight:500;font-size:1rem;color:rgba(255,255,255,.75);margin-bottom:1.5rem;}
        .info-card .desc{font-size:.95rem;line-height:1.6;color:rgba(255,255,255,.65);}
        .login-right{flex:1 1 auto;display:flex;align-items:center;justify-content:center;background:var(--gray-50);padding:2.5rem 1.5rem;min-height:480px;}
        @media (min-width:992px){ .login-right{flex:0 0 47%;min-height:100vh;padding:3rem 2.5rem;} }
        .login-card{background:var(--white);border-radius:1.25rem;box-shadow:0 20px 40px -12px rgba(15,23,42,.12),0 8px 16px -8px rgba(15,23,42,.08);width:100%;max-width:400px;margin:0 auto;}
        .login-card .card-body{padding:2.5rem 2rem;}
        .login-card .crest{width:56px;height:56px;margin:0 auto 1.5rem;}
        .login-card h2{font-weight:700;font-size:1.35rem;color:var(--navy);margin-bottom:.25rem;}
        .login-card .lead{font-size:.95rem;color:var(--gray-500);margin-bottom:2rem;}
        .form-label{font-weight:500;font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;color:var(--teal);margin-bottom:.5rem;}
        .input-group-wrapper{position:relative;}
        .input-group-wrapper .form-control{border-radius:.75rem;border:1.5px solid var(--gray-200);padding:.875rem 1rem .875rem 3rem;font-size:1rem;transition:border-color .15s,box-shadow .15s;background:var(--white);}
        .input-group-wrapper .form-control:focus{border-color:var(--teal);box-shadow:0 0 0 3px rgba(13,148,136,.15);outline:none;}
        .input-icon{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--gray-500);font-size:1.1rem;pointer-events:none;}
        .password-toggle{position:absolute;right:.875rem;top:50%;transform:translateY(-50%);border:none;background:transparent;color:var(--gray-500);padding:.25rem;font-size:1.1rem;cursor:pointer;transition:color .15s;}
        .password-toggle:hover{color:var(--teal);}
        .btn-signin{width:100%;background:var(--teal);border:none;border-radius:.75rem;padding:.95rem 1.5rem;font-weight:600;font-size:1rem;color:var(--white);transition:background .15s,transform .1s,box-shadow .15s;box-shadow:0 4px 14px -4px rgba(13,148,136,.4);}
        .btn-signin:hover{background:var(--teal-dark);transform:translateY(-1px);box-shadow:0 6px 20px -6px rgba(13,148,136,.5);}
        .btn-signin:active{transform:translateY(0);}
        .footer-strip{background:var(--navy);color:rgba(255,255,255,.55);font-size:.75rem;padding:.75rem 1.5rem;text-align:center;flex-shrink:0;}
        .footer-strip a{color:rgba(255,255,255,.7);text-decoration:none;font-weight:500;}
        .footer-strip a:hover{color:var(--teal);}
    </style>
</head>
<body>
    <div class="login-split">
        <div class="login-left">
            <div class="content-wrapper d-flex flex-column justify-content-center">
                <div class="info-card card border-0">
                    <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="CuyoTech Logo" class="logo img-fluid mx-auto d-block">
                    <div class="brand">CuyoTech University</div>
                    <h1 class="title">CuyoSSIS</h1>
                    <p class="subtitle">Student Services Information System</p>
                    <p class="desc">Choose a new password for your account. Your password must be at least 8 characters.</p>
                </div>
            </div>
        </div>
        <div class="login-right">
            <div class="login-card card border-0">
                <div class="card-body">
                    <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="Institutional Crest" class="crest img-fluid mx-auto d-block">
                    <h2>Choose New Password</h2>
                    <p class="lead">Enter your new password below</p>

                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4 alert-custom-radius" role="alert" aria-live="polite">
                            <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/ssis/reset-password" autocomplete="new-password">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                        <div class="mb-4">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group-wrapper">
                                <i class="input-icon bi bi-lock" aria-hidden="true"></i>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="New password" required minlength="8" aria-required="true" autocomplete="new-password">
                                <button type="button" class="password-toggle" id="togglePassword" title="Show password" aria-label="Show password" aria-controls="password" aria-pressed="false">
                                    <i class="bi bi-eye-slash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <div class="input-group-wrapper">
                                <i class="input-icon bi bi-lock" aria-hidden="true"></i>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                       placeholder="Confirm new password" required minlength="8" aria-required="true" autocomplete="new-password">
                            </div>
                        </div>
                        <button type="submit" class="btn-signin"><i class="bi bi-save me-2" aria-hidden="true"></i>Save New Password</button>
                    </form>

                    <div class="help-text mt-3 pt-3 border-top help-text-custom">
                        <a href="/ssis/forgot-password" class="text-muted">Resend reset link</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer-strip w-100">
        &copy; <?= date('Y') ?> CuyoTech University &middot; CuyoSSIS &middot; Academic Year <?= $academic_year ?? (date('Y') . '&ndash;' . date('Y', strtotime('+1 year'))) ?>
    </footer>
    <script src="/ssis/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            if (toggle) {
                toggle.addEventListener('click', function() {
                    const isShowing = password.getAttribute('type') === 'text';
                    password.setAttribute('type', isShowing ? 'password' : 'text');
                });
            }
        });
    </script>
</body>
</html>
