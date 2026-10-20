<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CuyoTech University Student Services Information System">
    <meta name="theme-color" content="#10b981">
    <link rel="icon" type="image/webp" href="/ssis/assets/cuyotech-logo-900x900.webp">
    <title>Forgot Password - <?= htmlspecialchars($system_title ?? ($config['system_title'] ?? 'CuyoSSIS')) ?></title>
    <link href="/ssis/assets/vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/ssis/assets/vendor/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/ssis/assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --brand: #10b981;
            --brand-dark: #059669;
            --brand-darker: #047857;
            --brand-soft: rgba(16, 185, 129, .1);
            --teal: #0f5d4b;
            --teal-dark: #084c3d;
            --navy: #0f172a;
            --navy-light: #1e293b;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f0f1f5;
            --gray-200: #d1d5db;
            --gray-300: #cbd5e1;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-tertiary: #9ca3af;
            --border: #d1d5db;
            --error: #ef4444;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--gray-50);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }
        .login-split {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        @media (min-width: 768px) and (max-width: 1199px) {
            .login-split { flex-direction: row; }
        }
        @media (min-width: 1200px) {
            .login-split { flex-direction: row; }
        }
        /* ===== Left Panel (branding) ===== */
        .login-left {
            position: relative;
            flex: 0 0 100%;
            min-height: 320px;
            background: linear-gradient(145deg, var(--teal), var(--teal-dark)),
                url('/ssis/assets/cuyotech-bg-1200x675.webp') center/cover no-repeat;
            display: none;
        }
        @media (min-width: 768px) {
            .login-left { display: block; flex: 0 0 40%; min-height: 100vh; }
        }
        @media (min-width: 1200px) {
            .login-left { flex: 0 0 50%; }
        }
        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(15, 23, 42, .6), rgba(15, 23, 42, .4));
            z-index: 1;
        }
        .login-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 30% 30%, rgba(255, 255, 255, .06) 1px, transparent 0),
                radial-gradient(circle at 70% 80%, rgba(255, 255, 255, .04) 1px, transparent 0);
            background-size: 20px 20px;
            opacity: .35;
            z-index: 1;
            pointer-events: none;
        }
        .float-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .09);
            z-index: 0;
            pointer-events: none;
            animation: float 5.5s ease-in-out infinite;
        }
        @media (prefers-reduced-motion: reduce) {
            .float-shape { animation: none; }
        }
        .float-shape.s1 { width: 110px; height: 110px; top: 12%; left: 8%;   animation-delay: 0s; }
        .float-shape.s2 { width: 70px;  height: 70px;  bottom: 10%; right: 10%; animation-delay: .8s; }
        .float-shape.s3 { width: 90px;  height: 90px;  top: 48%; left: 14%;  animation-delay: 1.6s; }
        .float-shape.s4 { width: 60px;  height: 60px;  bottom: 22%; left: 22%; animation-delay: 2.4s; }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); opacity: .8; }
            50%      { transform: translateY(-10px) scale(1.06); opacity: .95; }
        }
        .login-left .content-wrapper {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
        }
        @media (min-width: 768px) {
            .login-left .content-wrapper { padding: 48px; }
        }
        .info-card {
            background: rgba(15, 23, 42, .72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            padding: 28px 24px;
            margin: 0 auto;
            max-width: 420px;
            text-align: center;
        }
        @media (min-width: 768px) {
            .info-card { margin: 0; max-width: 100%; text-align: left; }
        }
        .info-card .logo-badge {
            width: auto;
            height: auto;
            background: none;
            border-radius: 0;
            margin: 0 auto 1.5rem;
        }
        .info-card .logo-badge img {
            width: auto;
            height: 48px;
        }
        .info-card .brand { font-weight: 700; font-size: 1.1rem; letter-spacing: .5px; color: rgba(255, 255, 255, .7); text-transform: uppercase; }
        .info-card .title { font-weight: 800; font-size: 2rem; line-height: 1.2; color: var(--white); margin: .5rem 0; }
        .info-card .subtitle { font-weight: 400; font-size: 13px; color: rgba(255, 255, 255, .7); margin-bottom: 1.25rem; }
        .info-card .desc { font-size: 13px; line-height: 1.6; color: rgba(255, 255, 255, .65); }
        .info-card .features { margin-top: 1.5rem; text-align: left; }
        .info-card .feature-item {
            font-size: 12px; font-weight: 500; color: rgba(255, 255, 255, .8);
            line-height: 1.8; display: flex; align-items: center; gap: .5rem;
        }
        .info-card .feature-item i { color: var(--brand); }
        /* ===== Right Panel (form) ==== */
        .login-right {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            overflow-y: auto;
            min-height: 0;
        }
        @media (min-width: 768px) {
            .login-right { padding: 40px 24px; }
        }
        .login-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, .12), 0 8px 16px -8px rgba(15, 23, 42, .08);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            opacity: 0;
            transform: translateY(12px);
            animation: fadeInUp .45s ease-in .1s forwards;
        }
        @media (prefers-reduced-motion: reduce) {
            .login-card { animation: none; opacity: 1; transform: none; }
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        .login-card .crest { width: 48px; height: 48px; margin: 0 auto 1.5rem; }
        .login-card h2 { font-weight: 700; font-size: 1.5rem; color: var(--navy); margin-bottom: .25rem; }
        .login-card .lead { font-size: 13px; color: var(--text-secondary); margin-bottom: 1.75rem; }

        .form-label {
            font-weight: 600; font-size: 11px; color: var(--text-secondary);
            text-transform: uppercase; letter-spacing: .06em; margin-bottom: .5rem;
        }
        .input-group-wrapper { position: relative; margin-bottom: 1.25rem; }
        .input-group-wrapper .form-control {
            height: 44px;
            padding: 10px 14px 10px 44px;
            font-size: 14px; font-weight: 400; color: var(--text-primary);
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--white);
            transition: border-color .2s cubic-bezier(.4,0,.2,1), box-shadow .2s cubic-bezier(.4,0,.2,1);
        }
        .input-group-wrapper .form-control::placeholder { color: var(--text-tertiary); }
        .input-group-wrapper .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-soft);
            outline: none;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--brand);
            font-size: 1.1rem;
            pointer-events: none;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
        }
        .input-group-wrapper .form-control.error {
            border-color: var(--error);
            background: rgba(239, 68, 68, .03);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .1);
        }
        .field-message { font-size: 11px; margin-top: 4px; min-height: 1em; color: var(--text-secondary); }
        .field-error  { color: var(--error); }
        .field-success{ color: var(--brand); }

        .btn-signin {
            width: 100%; height: 44px;
            background: var(--brand); border: none; border-radius: 8px;
            font-size: 15px; font-weight: 600; color: var(--white);
            text-transform: capitalize;
            padding: 0 1.5rem; margin-top: 1.5rem; cursor: pointer;
            box-shadow: 0 4px 14px -4px rgba(16, 185, 129, .4);
            transition: background .2s cubic-bezier(.4,0,.2,1), transform .1s, box-shadow .2s;
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .btn-signin:hover { background: var(--brand-dark); box-shadow: 0 6px 20px -6px rgba(16, 185, 129, .5); transform: translateY(-1px); }
        .btn-signin:active { background: var(--brand-darker); transform: scale(.98); }
        .btn-signin:disabled { background: var(--gray-200); color: var(--gray-500); cursor: not-allowed; opacity: .6; box-shadow: none; transform: none; }
        .btn:focus-visible,
        .form-check-input:focus,
        .password-toggle:focus-visible {
            outline: 2px solid var(--brand);
            outline-offset: 3px;
        }
        .spinner-border { width: 18px; height: 18px; }

        .help-text {
            margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);
            font-size: 12px; color: var(--text-secondary); text-align: center;
        }
        .help-text a { color: var(--brand); font-weight: 500; text-decoration: none; transition: color .2s; }
        .help-text a:hover { text-decoration: underline; }

        .alert-danger.shake { animation: shake .35s ease-in-out; }
        @keyframes shake { 16% { transform: translateX(.2rem); } 33% { transform: translateX(-.2rem); } 49% { transform: translateX(.2rem); } 66% { transform: translateX(-.2rem); } 83% { transform: translateX(.15rem); } 100% { transform: translateX(0); } }

        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: .001ms !important; transition-duration: .001ms !important; }
        }
        @media (max-width: 767px) {
            .login-card { border-radius: 12px; }
            .login-card h2 { font-size: 1.35rem; }
        }
    </style>
</head>
<body>
    <div class="login-split">
        <!-- Left Panel: branding -->
        <div class="login-left" aria-hidden="true">
            <div class="float-shape s1"></div>
            <div class="float-shape s2"></div>
            <div class="float-shape s3"></div>
            <div class="float-shape s4"></div>
            <div class="content-wrapper">
                <div class="info-card card border-0">
                    <div class="logo-badge">
                        <img src="/ssis/assets/cuyossis-3204x981.webp" alt="CuyoSSIS" style="width: auto; height: 48px;">
                    </div>
                    <div class="brand">CuyoTech University</div>
                    <h1 class="title">CuyoSSIS</h1>
                    <p class="subtitle">Student Services Information System</p>
                    <p class="desc">Enter your email below and we will send you a link to reset your password.</p>
                    <div class="features">
                        <div class="feature-item"><i class="bi bi-check2-circle"></i> Academic Records</div>
                        <div class="feature-item"><i class="bi bi-check2-circle"></i> Enrollment & Grades</div>
                        <div class="feature-item"><i class="bi bi-check2-circle"></i> Secure Document Services</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: login form -->
        <div class="login-right">
            <div class="login-card card border-0">
                <div class="card-body p-4">
                    <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="CuyoSSIS" class="crest img-fluid mx-auto d-block">
                    <h2>Reset Your Password</h2>
                    <p class="lead">Enter your institutional email to receive a reset link</p>

                    
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4 shake" role="alert" aria-live="polite">
                            <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" aria-live="polite">
                            <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/ssis/forgot-password" autocomplete="off" id="forgotForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                        <div class="mb-0">
                            <label for="email" class="form-label">Institutional Email</label>
                            <div class="input-group-wrapper">
                                <i class="input-icon bi bi-envelope" aria-hidden="true"></i>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="you@student.cuyotech.edu" required aria-required="true" autocomplete="email"
                                       aria-describedby="emailMessage">
                                <div id="emailMessage" class="field-message" aria-live="polite"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-signin" id="btnSignin" aria-busy="false" aria-live="polite">
                            <span class="btn-label"><i class="bi bi-send me-2" aria-hidden="true"></i>Send Reset Link</span>
                            <span class="btn-spinner d-none"><span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Sending...</span>
                        </button>
                    </form>

                    <div class="help-text">
                        <a href="/ssis/login" class="text-muted"><i class="bi bi-arrow-left me-1"></i>Back to Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="w-100 text-center py-3 small text-secondary" role="contentinfo">
        &copy; <?= date('Y') ?> CuyoTech University &middot; CuyoSSIS &middot; Academic Year <?= $academic_year ?? (date('Y') . '&ndash;' . date('Y', strtotime('+1 year'))) ?>
    </footer>

    <script src="/ssis/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            "use strict";

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var emailField = document.getElementById('email');
            var form = document.getElementById('forgotForm');
            var signin = document.getElementById('btnSignin');

            if (form) {
                form.addEventListener('submit', function () {
                    if (signin) {
                        signin.disabled = true;
                        signin.setAttribute('aria-busy', 'true');
                        var label = signin.querySelector('.btn-label');
                        var spinner = signin.querySelector('.btn-spinner');
                        if (label) label.classList.add('d-none');
                        if (spinner) spinner.classList.remove('d-none');
                    }
                });
            }

            if (emailField) {
                var msg = emailField.parentNode.querySelector('.field-message');
                emailField.addEventListener('input', function () {
                    var val = this.value.trim();
                    if (val.indexOf('@') !== -1) {
                        if (emailRegex.test(val)) {
                            this.classList.remove('error'); this.classList.add('success');
                            if (msg) { msg.textContent = 'Valid email format'; msg.className = 'field-message field-success'; }
                        } else {
                            this.classList.remove('success'); this.classList.add('error');
                            if (msg) { msg.textContent = 'Please enter a valid email address'; msg.className = 'field-message field-error'; }
                        }
                    } else {
                        this.classList.remove('error', 'success');
                        if (msg) msg.textContent = '';
                    }
                });
            }
        })();
    </script>
</body>
</html>