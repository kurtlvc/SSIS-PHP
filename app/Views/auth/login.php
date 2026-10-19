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
            --cuyo-green: #1e4f3f;
            --cuyo-green-dark: #153d2d;
            --cuyo-green-light: #2ea66a;
            --cuyo-white: #ffffff;
            --cuyo-gray: #f0f1f5;
            --cuyo-gray-dark: #6c7580;
            --cuyo-gray-border: #e4e6eb;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
        }
        .login-left {
            background-image: url('/sis/assets/cuyosis-3204x981.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            position: relative;
        }
        .login-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(21, 61, 45, 0.45), rgba(21, 61, 45, 0.35));
            z-index: 1;
        }
        .login-left .map-motif {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 15% 30%, rgba(255,255,255,0.06) 2px, transparent 3px),
                radial-gradient(circle at 75% 80%, rgba(255,255,255,0.04) 2px, transparent 3px),
                radial-gradient(circle at 85% 20%, rgba(255,255,255,0.05) 1.5px, transparent 3px),
                radial-gradient(circle at 40% 60%, rgba(255,255,255,0.03) 2px, transparent 3px);
            z-index: 0;
            pointer-events: none;
        }
        .login-info-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .login-right {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--cuyo-white);
        }
        .login-card {
            border-radius: 1rem;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
            max-width: 420px;
            width: 100%;
        }
        .login-card .card-body { padding: 2rem 1.75rem; }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--cuyo-green);
            font-size: 1.05rem;
            z-index: 2;
        }
        .input-with-icon { position: relative; }
        .input-with-icon .form-control { padding-left: 2.75rem; }
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            border: none;
            background: transparent;
            color: var(--cuyo-gray-dark);
            padding: 0.25rem;
        }
        .btn-signin {
            border-radius: 0.6rem;
            font-weight: 600;
            padding: 0.65rem;
        }
        .footer-thin {
            height: 3.5rem;
            min-height: 3.5rem;
        }
    </style>
</head>
<body class="bg-cuyo-gray">
    <div class="d-flex flex-column min-vh-100 flex-lg-row">

        <!-- Left Panel: Scenic Island + Info Card -->
        <div class="login-left flex-lg-unordered order-2 order-lg-first position-relative">
            <div class="map-motif"></div>
            <div class="position-relative h-100 d-flex align-items-center justify-content-center z-2 px-4">
                <div class="login-info-card card border-0 text-white mx-auto" style="max-width: 380px;">
                    <div class="card-body text-center py-4 px-4">
                        <img src="/sis/assets/cuyotech-logo-900x900.webp" alt="CuyoTech Logo" width="80" height="80" class="mb-3 img-fluid">
                        <h1 class="h4 fw-bold mb-1">CuyoSIS</h1>
                        <p class="small opacity-80 mb-3">Student Information System</p>
                        <p class="small opacity-90 mb-0 leading-relaxed">
                            Your gateway to academic records, enrollment, grades,
                            and document services at CuyoTech University.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="login-right flex-lg-first order-1 order-lg-last flex-grow-1">
            <div class="container d-flex justify-content-center px-4 px-lg-5">
                <div class="login-card card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="/sis/assets/cuyotech-logo-900x900.webp" alt="Institutional Crest" width="64" height="64" class="img-fluid">
                        </div>

                        <h2 class="h5 fw-bold text-center mb-4 text-cuyo-green">Sign In to CuyoSIS</h2>

                        <?php if (isset($error) && $error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/sis/login" autocomplete="off">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                            <div class="mb-3">
                                <label for="login" class="form-label fw-medium small text-cuyo-green text-uppercase letter-spacing-0.5">
                                    Institutional Email / Username
                                </label>
                                <div class="input-with-icon">
                                    <i class="input-icon bi bi-person"></i>
                                    <input type="text" class="form-control form-control-lg border-cuyo-gray-border" id="login" name="login"
                                           placeholder="Enter your email or username" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium small text-cuyo-green text-uppercase letter-spacing-0.5">
                                    Password
                                </label>
                                <div class="input-with-icon">
                                    <i class="input-icon bi bi-lock"></i>
                                    <input type="password" class="form-control form-control-lg border-cuyo-gray-border" id="password" name="password"
                                           placeholder="Enter your password" required>
                                    <button type="button" class="password-toggle" id="togglePassword" title="Show password">
                                        <i class="bi bi-eye-slash fs-6"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 small">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label text-muted" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                <a href="/sis/password-reset" class="text-cuyo-green text-decoration-none fw-medium">
                                    Forgot password?
                                </a>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-cuyo-green text-white btn-signin py-2">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4 small text-muted border-top border-cuyo-gray-border pt-3">
                            Need assistance? Contact the
                            <a href="mailto:registrar@cutyotech.edu" class="text-cuyo-green text-decoration-none fw-medium">Registrar's Office</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-thin d-flex align-items-center justify-content-center bg-white border-top border-cuyo-gray-border mt-auto">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row">
                <div class="col-12 text-center small text-cuyo-gray-dark">
                    &copy; <?= date('Y') ?> CuyoTech University &middot; CuyoSIS &middot; Academic Year <?= $academic_year ?? (date('Y') . '&ndash;' . date('Y', strtotime('+1 year'))) ?>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
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
