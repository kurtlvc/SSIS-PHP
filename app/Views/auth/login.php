<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CuyoTech University Student Information System">
    <title>Login - <?= htmlspecialchars($system_title ?? $config['system_title'] ?? 'CuyoSIS') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/sis/assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --teal: #0d9488;
            --teal-dark: #0f766e;
            --teal-light: #14b8a6;
            --navy: #0f172a;
            --navy-light: #1e293b;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-900: #0f172a;
        }
        * { box-sizing: border-box; }
        html, body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background: var(--gray-50);
        }
        .login-split {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        @media (min-width: 992px) {
            .login-split { flex-direction: row; }
        }
        /* Left Panel - 53% on desktop, full on mobile */
        .login-left {
            position: relative;
            background-image: url('/sis/assets/cuyotech-bg-1200x675.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            flex: 0 0 100%;
            min-height: 320px;
        }
        @media (min-width: 992px) {
            .login-left {
                flex: 0 0 53%;
                min-height: 100vh;
            }
        }
        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.35));
            z-index: 1;
        }
        .login-left .content-wrapper {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.5rem;
        }
        @media (min-width: 992px) {
            .login-left .content-wrapper {
                padding: 4rem 3.5rem;
                justify-content: center;
            }
        }
        .info-card {
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.25rem;
            max-width: 420px;
            margin: 0 auto;
            padding: 2.5rem 2rem;
            text-align: left;
        }
        @media (min-width: 992px) {
            .info-card {
                margin: 0;
                max-width: none;
                width: 100%;
            }
        }
        .info-card .logo { width: 56px; height: 56px; margin-bottom: 1.25rem; }
        .info-card .brand { font-weight: 700; font-size: 0.85rem; letter-spacing: 0.08em; color: rgba(255,255,255,0.7); text-transform: uppercase; margin-bottom: 0.5rem; }
        .info-card .title { font-weight: 800; font-size: 2rem; line-height: 1.2; color: var(--white); margin-bottom: 0.5rem; }
        .info-card .subtitle { font-weight: 500; font-size: 1rem; color: rgba(255,255,255,0.75); margin-bottom: 1.5rem; }
        .info-card .desc { font-size: 0.95rem; line-height: 1.6; color: rgba(255,255,255,0.65); }

        /* Right Panel - 47% on desktop, full on mobile */
        .login-right {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gray-50);
            padding: 2.5rem 1.5rem;
            min-height: 480px;
        }
        @media (min-width: 992px) {
            .login-right {
                flex: 0 0 47%;
                min-height: 100vh;
                padding: 3rem 2.5rem;
            }
        }
        .login-card {
            background: var(--white);
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.12), 0 8px 16px -8px rgba(15, 23, 42, 0.08);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        .login-card .card-body { padding: 2.5rem 2rem; }
        .login-card .crest { width: 56px; height: 56px; margin: 0 auto 1.5rem; }
        .login-card h2 { font-weight: 700; font-size: 1.35rem; color: var(--navy); margin-bottom: 0.25rem; }
        .login-card .lead { font-size: 0.95rem; color: var(--gray-500); margin-bottom: 2rem; }

        .form-label {
            font-weight: 500;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--teal);
            margin-bottom: 0.5rem;
        }
        .input-group-wrapper { position: relative; }
        .input-group-wrapper .form-control {
            border-radius: 0.75rem;
            border: 1.5px solid var(--gray-200);
            padding: 0.875rem 1rem 0.875rem 3rem;
            font-size: 1rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: var(--white);
        }
        .input-group-wrapper .form-control:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
            outline: none;
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.15s;
        }
        .input-group-wrapper:focus-within .input-icon { color: var(--teal); }
        .password-toggle {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--gray-500);
            padding: 0.25rem;
            font-size: 1.1rem;
            cursor: pointer;
            transition: color 0.15s;
        }
        .password-toggle:hover { color: var(--teal); }

        .remember-row { display: flex; align-items: center; justify-content: space-between; margin: 1.5rem 0 1.75rem; }
        .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
            border: 1.5px solid var(--gray-300);
            border-radius: 0.3rem;
            margin-right: 0.5rem;
        }
        .form-check-input:checked { background-color: var(--teal); border-color: var(--teal); }
        .form-check-label { font-size: 0.875rem; color: var(--gray-700); cursor: pointer; }
        .forgot-link { font-size: 0.875rem; font-weight: 500; color: var(--teal); text-decoration: none; }
        .forgot-link:hover { color: var(--teal-dark); text-decoration: underline; }

        .btn-signin {
            width: 100%;
            background: var(--teal);
            border: none;
            border-radius: 0.75rem;
            padding: 0.95rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            color: var(--white);
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            box-shadow: 0 4px 14px -4px rgba(13, 148, 136, 0.4);
        }
        .btn-signin:hover { background: var(--teal-dark); transform: translateY(-1px); box-shadow: 0 6px 20px -6px rgba(13, 148, 136, 0.5); }
        .btn-signin:active { transform: translateY(0); }

        .help-text {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
            font-size: 0.85rem;
            color: var(--gray-500);
            text-align: center;
        }
        .help-text a { color: var(--teal); font-weight: 500; text-decoration: none; }
        .help-text a:hover { text-decoration: underline; }

        /* Footer - dark navy strip */
        .footer-strip {
            background: var(--navy);
            color: rgba(255,255,255,0.55);
            font-size: 0.75rem;
            padding: 0.75rem 1.5rem;
            text-align: center;
            flex-shrink: 0;
        }
        .footer-strip a { color: rgba(255,255,255,0.7); text-decoration: none; font-weight: 500; }
        .footer-strip a:hover { color: var(--teal-light); }
    </style>
</head>
<body>
    <div class="login-split">
        <!-- Left Panel: Tropical Coastal Landscape -->
        <div class="login-left">
            <div class="content-wrapper d-flex flex-column justify-content-center">
                <div class="info-card card border-0">
                    <img src="/sis/assets/cuyotech-logo-900x900.webp" alt="CuyoTech Logo" class="logo img-fluid mx-auto d-block">
                    <div class="brand">CuyoTech University</div>
                    <h1 class="title">CuyoSIS</h1>
                    <p class="subtitle">Student Information System</p>
                    <p class="desc">Access your academic records, enrollment, grades, schedules, and document services — all in one secure portal.</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="login-right">
            <div class="login-card card border-0">
                <div class="card-body">
                    <img src="/sis/assets/cuyotech-logo-900x900.webp" alt="Institutional Crest" class="crest img-fluid mx-auto d-block">
                    <h2>Welcome back</h2>
                    <p class="lead">Sign in to continue to CuyoSIS</p>

                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 0.75rem; border: none; font-size: 0.875rem;">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/sis/login" autocomplete="off" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                        <div class="mb-4">
                            <label for="login" class="form-label">Institutional Email / Username</label>
                            <div class="input-group-wrapper">
                                <i class="input-icon bi bi-person"></i>
                                <input type="text" class="form-control" id="login" name="login"
                                       placeholder="Enter your email or username" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group-wrapper">
                                <i class="input-icon bi bi-lock"></i>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Enter your password" required>
                                <button type="button" class="password-toggle" id="togglePassword" title="Show password" aria-label="Toggle password visibility">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="remember-row">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="/sis/password-reset" class="forgot-link">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-signin">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </button>
                    </form>

                    <div class="help-text">
                        Need assistance? Contact the
                        <a href="mailto:registrar@cuyotech.edu">Registrar's Office</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer: Dark Navy Strip -->
    <footer class="footer-strip w-100">
        &copy; <?= date('Y') ?> CuyoTech University &middot; CuyoSIS &middot; Academic Year <?= $academic_year ?? (date('Y') . '&ndash;' . date('Y', strtotime('+1 year'))) ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            toggle.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        });
    </script>
</body>
</html>