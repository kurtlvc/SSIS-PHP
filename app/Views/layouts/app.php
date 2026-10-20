<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CuyoTech University Student Services Information System">
    <meta name="theme-color" content="#1e4f3f">
    <link rel="icon" type="image/webp" href="/ssis/assets/cuyotech-logo-900x900.webp">
    <title><?= htmlspecialchars($pageTitle ?? ($config['system_title'] ?? 'CuyoSSIS')) ?></title>
    <link href="/ssis/assets/vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/ssis/assets/vendor/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/ssis/assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --cuyo-teal: #0d9488;
            --cuyo-teal-dark: #0f766e;
            --cuyo-navy: #0f172a;
            --cuyo-navy-light: #1e293b;
            --cuyo-gray: #f8fafc;
            --cuyo-border: #e2e8f0;
            
            /* Dashboard dark theme colors */
            --dash-bg: #0f172a;
            --dash-card-bg: rgba(255, 255, 255, 0.05);
            --dash-card-border: rgba(255, 255, 255, 0.1);
            --dash-card-hover-bg: rgba(255, 255, 255, 0.08);
            --dash-card-hover-border: rgba(255, 255, 255, 0.15);
            --dash-card-active-bg: rgba(59, 130, 246, 0.1);
            --dash-card-active-border: rgba(59, 130, 246, 0.3);
            --dash-text-primary: #ffffff;
            --dash-text-secondary: rgba(255, 255, 255, 0.8);
            --dash-text-tertiary: rgba(255, 255, 255, 0.6);
            --dash-text-muted: rgba(255, 255, 255, 0.5);
            --dash-text-disabled: rgba(255, 255, 255, 0.4);
            --dash-accent-blue: #3b82f6;
            --dash-accent-green: #10b981;
            --dash-accent-amber: #f59e0b;
            --dash-accent-cyan: #06b6d4;
            --dash-accent-red: #ef4444;
            
            /* Light theme overrides */
            --light-dash-bg: #f8fafc;
            --light-dash-card-bg: #ffffff;
            --light-dash-card-border: #e2e8f0;
            --light-dash-card-hover-bg: #f1f5f9;
            --light-dash-card-hover-border: #cbd5e1;
            --light-dash-card-active-bg: #eff6ff;
            --light-dash-card-active-border: #3b82f6;
            --light-dash-text-primary: #0f172a;
            --light-dash-text-secondary: #334155;
            --light-dash-text-tertiary: #64748b;
            --light-dash-text-muted: #94a3b8;
            --light-dash-text-disabled: #cbd5e1;
        }
        
        /* Theme-aware variables - default dark */
        :root,
        [data-theme="dark"] {
            --bg: var(--dash-bg);
            --card-bg: var(--dash-card-bg);
            --card-border: var(--dash-card-border);
            --card-hover-bg: var(--dash-card-hover-bg);
            --card-hover-border: var(--dash-card-hover-border);
            --card-active-bg: var(--dash-card-active-bg);
            --card-active-border: var(--dash-card-active-border);
            --text-primary: var(--dash-text-primary);
            --text-secondary: var(--dash-text-secondary);
            --text-tertiary: var(--dash-text-tertiary);
            --text-muted: var(--dash-text-muted);
            --text-disabled: var(--dash-text-disabled);
            --accent-blue: var(--dash-accent-blue);
            --accent-green: var(--dash-accent-green);
            --accent-amber: var(--dash-accent-amber);
            --accent-cyan: var(--dash-accent-cyan);
            --accent-red: var(--dash-accent-red);
            --sidebar-bg: linear-gradient(180deg, var(--cuyo-navy) 0%, var(--cuyo-navy-light) 100%);
            --sidebar-text: #e2e8f0;
            --header-bg: rgba(15, 23, 42, 0.95);
            --header-border: rgba(255, 255, 255, 0.12);
            --input-bg: var(--dash-card-bg);
            --input-border: var(--dash-card-border);
            --input-text: var(--dash-text-primary);
            --input-placeholder: var(--dash-text-disabled);
            --table-border: var(--dash-card-border);
            --table-header-text: var(--dash-text-tertiary);
            --table-hover-bg: var(--dash-card-hover-bg);
            --dropdown-bg: rgba(15, 23, 42, 0.95);
            --dropdown-border: rgba(255, 255, 255, 0.15);
            --dropdown-item-text: var(--dash-text-secondary);
            --dropdown-hover-bg: rgba(255, 255, 255, 0.1);
            --dropdown-hover-text: var(--dash-text-primary);
            --alert-bg: var(--dash-card-bg);
            --alert-border: var(--dash-card-border);
            --alert-danger-bg: rgba(239, 68, 68, 0.1);
            --alert-danger-border: rgba(239, 68, 68, 0.3);
            --alert-success-bg: rgba(16, 185, 129, 0.1);
            --alert-success-border: rgba(16, 185, 129, 0.3);
            --modal-bg: rgba(15, 23, 42, 0.95);
            --modal-border: rgba(255, 255, 255, 0.15);
            --modal-text: var(--dash-text-primary);
        }
        
        [data-theme="light"] {
            --bg: var(--light-dash-bg);
            --card-bg: var(--light-dash-card-bg);
            --card-border: var(--light-dash-card-border);
            --card-hover-bg: var(--light-dash-card-hover-bg);
            --card-hover-border: var(--light-dash-card-hover-border);
            --card-active-bg: var(--light-dash-card-active-bg);
            --card-active-border: var(--light-dash-card-active-border);
            --text-primary: var(--light-dash-text-primary);
            --text-secondary: var(--light-dash-text-secondary);
            --text-tertiary: var(--light-dash-text-tertiary);
            --text-muted: var(--light-dash-text-muted);
            --text-disabled: var(--light-dash-text-disabled);
            --accent-blue: var(--dash-accent-blue);
            --accent-green: var(--dash-accent-green);
            --accent-amber: var(--dash-accent-amber);
            --accent-cyan: var(--dash-accent-cyan);
            --accent-red: var(--dash-accent-red);
            --sidebar-bg: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            --sidebar-text: #1e293b;
            --header-bg: rgba(255, 255, 255, 0.95);
            --header-border: rgba(15, 23, 42, 0.1);
            --input-bg: #ffffff;
            --input-border: #e2e8f0;
            --input-text: #0f172a;
            --input-placeholder: #94a3b8;
            --table-border: #e2e8f0;
            --table-header-text: #64748b;
            --table-hover-bg: #f1f5f9;
            --dropdown-bg: #ffffff;
            --dropdown-border: #e2e8f0;
            --dropdown-item-text: #334155;
            --dropdown-hover-bg: #f1f5f9;
            --dropdown-hover-text: #0f172a;
            --alert-bg: #ffffff;
            --alert-border: #e2e8f0;
            --alert-danger-bg: #fef2f2;
            --alert-danger-border: #fecaca;
            --alert-success-bg: #f0fdf4;
            --alert-success-border: #bbf7d0;
            --modal-bg: #ffffff;
            --modal-border: #e2e8f0;
            --modal-text: #0f172a;
        }
        
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        .app-shell { min-height: 100vh; display: flex; }
        
        /* Prevent flash of wrong theme */
        .theme-loading { visibility: hidden; }
        .theme-loaded .theme-loading { visibility: visible; }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            z-index: 1020;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--card-border);
        }
        .sidebar .brand-link {
            display: flex;
            align-items: center;
            gap: .6rem;
            color: var(--cuyo-teal);
            font-weight: 700;
            font-size: 1.15rem;
            text-decoration: none;
            padding: 1rem;
        }
        .sidebar .brand-link img { width: 32px; height: 32px; }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 0.5rem; }
        .sidebar .nav-link {
            color: var(--text-tertiary);
            border-radius: 6px;
            padding: .625rem .875rem;
            font-size: .875rem;
            font-weight: 500;
            gap: .6rem;
            min-height: 40px;
            transition: background-color .15s cubic-bezier(.4,0,.2,1), color .15s cubic-bezier(.4,0,.2,1);
        }
        .sidebar .nav-link:hover {
            background: var(--card-hover-bg);
            color: var(--text-primary);
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link[aria-current="page"] {
            background: var(--card-active-bg);
            color: var(--text-primary);
            border-left: 3px solid var(--accent-blue);
        }
        .sidebar .nav-link:focus-visible {
            outline: 2px solid var(--accent-blue);
            outline-offset: 2px;
        }
        .main-wrap { margin-left: 250px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .header {
            position: sticky;
            top: 0;
            z-index: 1010;
            background: var(--header-bg);
            border-bottom: 1px solid var(--header-border);
            backdrop-filter: blur(8px);
            height: 60px;
        }
        .header h1 { color: var(--text-primary); }
        .main-content { padding: 1.5rem 2rem; flex: 1; overflow-y: auto; background: var(--bg); }
        .user-avatar { width: 36px; height: 36px; object-fit: cover; flex-shrink: 0; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1015; }
        .sidebar-overlay.open { display: block; }
        @media (max-width: 768px) {
            .main-wrap { margin-left: 0; }
            .sidebar { position: fixed; width: 280px; min-height: 100vh; transform: translateX(-100%); transition: transform .25s cubic-bezier(.4,0,.2,1); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-toggle { display: flex; }
        }
        
        /* Dashboard Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 24px;
        }
        @media (max-width: 1199px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
        }
        @media (max-width: 767px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 12px;
                margin-top: 16px;
            }
        }

        /* Dashboard Cards - Theme Aware */
        .dash-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 24px;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
        }
        .dash-card:hover {
            background: var(--card-hover-bg);
            border-color: var(--card-hover-border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
            text-decoration: none;
            color: inherit;
        }
        .dash-card:focus-visible {
            outline: none;
            border: 2px solid var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .dash-card:active {
            background: var(--card-active-bg);
            border-color: var(--card-active-border);
            transform: translateY(0);
            transition-duration: 100ms;
        }
        
        .dash-card__icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            flex-shrink: 0;
        }
        .dash-card__icon--success { background: rgba(16, 185, 129, 0.15); color: var(--accent-green); }
        .dash-card__icon--info { background: rgba(59, 130, 246, 0.15); color: var(--accent-blue); }
        .dash-card__icon--warning { background: rgba(245, 158, 11, 0.15); color: var(--accent-amber); }
        .dash-card__icon--cyan { background: rgba(6, 182, 212, 0.15); color: var(--accent-cyan); }
        
        .dash-card__content { flex: 1; display: flex; flex-direction: column; }
        .dash-card__title { font-size: 14px; font-weight: 600; color: var(--text-secondary); margin: 0 0 8px 0; text-transform: capitalize; }
        .dash-card__subtitle { font-size: 12px; font-weight: 400; color: var(--text-muted); margin: 0 0 12px 0; line-height: 1.5; }
        .dash-card__value { font-size: 32px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; line-height: 1.2; }
        .dash-card__value--text { font-size: 18px; font-weight: 600; }
        .dash-card__meta { font-size: 11px; font-weight: 400; color: var(--text-disabled); margin-top: auto; }
        .dash-card__trend { display: inline-flex; align-items: center; font-size: 11px; font-weight: 500; }
        .dash-card__trend--positive { color: var(--accent-green); }
        .dash-card__trend--negative { color: var(--accent-red); }
        .dash-card__status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green); margin-right: 6px; vertical-align: middle; }
        .dash-card__badge { display: inline-flex; align-items: center; padding: 2px 8px; font-size: 10px; font-weight: 600; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em; }
        .dash-card__badge--critical { background: rgba(239, 68, 68, 0.15); color: var(--accent-red); border: 1px solid rgba(239, 68, 68, 0.3); }
        .dash-card__action { font-size: 13px; font-weight: 500; color: var(--accent-blue); text-decoration: none; margin-top: auto; padding-top: 12px; transition: opacity 200ms ease-in-out, color 200ms ease-in-out; align-self: flex-start; }
        .dash-card__action:hover { opacity: 0.8; text-decoration: underline; color: var(--accent-blue); }
        .dash-card__action:focus-visible { outline: 2px solid var(--accent-blue); outline-offset: 2px; border-radius: 4px; }
        
        /* Card load animation */
        .dash-card { opacity: 0; transform: translateY(10px); animation: dashCardIn 300ms cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        .dash-card:nth-child(1) { animation-delay: 0ms; }
        .dash-card:nth-child(2) { animation-delay: 50ms; }
        .dash-card:nth-child(3) { animation-delay: 100ms; }
        .dash-card:nth-child(4) { animation-delay: 150ms; }
        @keyframes dashCardIn { to { opacity: 1; transform: translateY(0); } }
        @media (prefers-reduced-motion: reduce) { .dash-card { animation: none; opacity: 1; transform: none; } .dash-card:hover { transform: none; } }
        
        /* Quick Links Card Theme Aware */
        .quick-links-card { background: var(--card-bg) !important; border: 1px solid var(--card-border) !important; border-radius: 12px; }
        .quick-links-card .card-header { color: var(--text-primary); background: transparent; border-bottom: 1px solid var(--card-border); }
        .quick-links-card .btn-outline-primary {
            --bs-btn-color: var(--accent-blue);
            --bs-btn-border-color: var(--card-hover-border);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--accent-blue);
            --bs-btn-hover-border-color: var(--accent-blue);
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: var(--accent-blue);
            --bs-btn-active-border-color: var(--accent-blue);
            --bs-btn-focus-box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            padding: 12px 16px;
        }

        /* Search Filter Bar Theme Aware */
        .search-filter-bar .form-label { letter-spacing: .05em; color: var(--text-tertiary); }
        .search-filter-bar .form-control { background: var(--input-bg); border-color: var(--input-border); color: var(--input-text); }
        .search-filter-bar .form-control::placeholder { color: var(--input-placeholder); }
        .search-filter-bar .form-control:focus { border-color: var(--accent-blue); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); background: var(--card-hover-bg); color: var(--input-text); }
        .search-filter-bar .input-group-text { background: var(--input-bg); border-color: var(--input-border); color: var(--text-tertiary); }
        #noQuickMatch { color: var(--text-disabled); }

        /* Focus indicators (WCAG) */
        .btn:focus-visible, .form-control:focus { outline: 2px solid var(--accent-blue); outline-offset: 2px; }
        
        /* Breadcrumb theme aware */
        .breadcrumb-item a { color: var(--text-tertiary); text-decoration: none; }
        .breadcrumb-item a:hover { color: var(--text-primary); }
        .breadcrumb-item.active { color: var(--text-muted); }
        .breadcrumb-item + .breadcrumb-item::before { color: var(--text-disabled); }
        
        /* Table theme aware */
        .table { color: var(--text-primary); }
        .table th, .table td { border-color: var(--table-border); }
        .table thead th { color: var(--table-header-text); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; background: var(--card-hover-bg); }
        .table tbody tr:hover { background: var(--table-hover-bg); }
        .table-responsive { border-radius: 8px; }
        
        /* Alert theme aware */
        .alert { background: var(--alert-bg); border-color: var(--alert-border); color: var(--text-primary); }
        .alert-danger { background: var(--alert-danger-bg); border-color: var(--alert-danger-border); color: #dc2626; }
        .alert-success { background: var(--alert-success-bg); border-color: var(--alert-success-border); color: #059669; }
        .alert .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        [data-theme="light"] .alert .btn-close { filter: none; }
        
        /* Dropdown theme aware */
        .dropdown-menu { background: var(--dropdown-bg); border: 1px solid var(--dropdown-border); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); border-radius: 8px; padding: 4px; min-width: 180px; }
        .dropdown-item { color: var(--dropdown-item-text); border-radius: 6px; padding: .5rem 1rem; font-size: .875rem; }
        .dropdown-item:hover, .dropdown-item:focus { background: var(--dropdown-hover-bg); color: var(--dropdown-hover-text); }
        .dropdown-divider { border-color: var(--dropdown-border); margin: 4px 0; }
        
        /* Modal theme aware */
        .modal-content { background: var(--modal-bg); border: 1px solid var(--modal-border); color: var(--modal-text); }
        .modal-header, .modal-footer { border-color: var(--modal-border); }
        .modal-title { color: var(--modal-text); }
        .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        [data-theme="light"] .btn-close { filter: none; }
        
        /* Form controls theme aware */
        .form-control, .form-select { background: var(--input-bg); border-color: var(--input-border); color: var(--input-text); }
        .form-control:focus, .form-select:focus { background: var(--input-bg); border-color: var(--accent-blue); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); color: var(--input-text); }
        .form-control::placeholder { color: var(--input-placeholder); }
        .form-label { color: var(--text-secondary); }
        .form-check-input { border-color: var(--input-border); }
        .form-check-input:checked { background-color: var(--accent-blue); border-color: var(--accent-blue); }
        
        /* Override hardcoded white backgrounds in card headers */
        .card-header { background: var(--card-hover-bg) !important; border-color: var(--card-border) !important; color: var(--text-primary) !important; }
        .card { background: var(--card-bg) !important; border-color: var(--card-border) !important; color: var(--text-primary) !important; }
        .card-body { color: var(--text-primary) !important; }
        
        /* Badge theme aware */
        .badge { font-weight: 500; }
        
        /* Theme toggle button */
        .theme-toggle { background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all .15s; }
        .theme-toggle:hover { background: var(--card-hover-bg); border-color: var(--card-hover-border); color: var(--text-primary); }
        .theme-toggle:focus-visible { outline: 2px solid var(--accent-blue); outline-offset: 2px; }
        
        /* Skip link */
        .skip-link { position: absolute; top: -100%; left: 1rem; z-index: 9999; padding: .5rem 1rem; background: var(--card-bg); border: 2px solid var(--accent-blue); color: var(--text-primary); border-radius: 4px; text-decoration: none; transition: top .2s; }
        .skip-link:focus { top: 1rem; }
        
        /* Sidebar toggle for mobile */
        .sidebar-toggle { display: none; }
        @media (max-width: 768px) { .sidebar-toggle { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); border-radius: 8px; } .sidebar-toggle:hover { background: var(--card-hover-bg); color: var(--text-primary); } }
        
        /* Main content background */
        .main-content { background: var(--bg); min-height: calc(100vh - 60px); }
        
        /* Page headings in main content */
        .main-content h1, .main-content h2, .main-content h3, .main-content h4, .main-content h5, .main-content h6 {
            color: var(--text-primary);
        }
        
        /* Prevent scroll on body when sidebar open on mobile */
        body.sidebar-open { overflow: hidden; }
    </style>
    <!-- Theme initialization script - runs before paint to prevent flash -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList.add('theme-loaded');
        })();
    </script>
</head>
<body class="theme-loading bg-var">
<a href="#main-content" class="skip-link skip-link-custom">Skip to main content</a>

<?php if (isset($_SESSION['account_id'])): ?>
<div class="app-shell d-flex">

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

    <!-- Sidebar -->
    <aside class="sidebar d-flex flex-column" role="navigation" aria-label="Sidebar navigation" id="sidebar">
        <div class="sidebar-header p-3 border-bottom-var">
            <?php
            $role = $_SESSION['account_role'] ?? 'student';
            $home = '/ssis/' . ($role === 'student' ? 'student/dashboard' : ($role === 'admin' ? 'admin/dashboard' : ($role === 'cashier' ? 'cashier/dashboard' : ($role === 'dean' || $role === 'chair' ? 'registrar/dashboard' : 'department/dashboard'))));
            ?>
            <a class="brand-link" href="<?= $home ?>">
                <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="CuyoSSIS">
                <span>CuyoSSIS</span>
            </a>
        </div>

        <nav class="sidebar-nav nav nav-pills flex-column mx-2 my-4 gap-1" role="navigation" aria-label="Sidebar navigation">
            <?php if ($role === 'student'): ?>
                <li class="nav-item"><a class="nav-link" href="/ssis/student/dashboard"><i class="bi bi-speedometer2 mx-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/student/grades"><i class="bi bi-journal-text mx-2"></i>Grades</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/student/schedule"><i class="bi bi-calendar-event mx-2"></i>Schedule</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/student/documents/request"><i class="bi bi-folder2-open mx-2"></i>Documents</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/student/documents/history"><i class="bi bi-clock mx-2"></i>Request History</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/student/clearance"><i class="bi bi-clipboard-check mx-2"></i>Clearance</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/student/profile"><i class="bi bi-person mx-2"></i>Profile</a></li>
            <?php elseif (in_array($role, ['faculty', 'chair', 'dean'])): ?>
                <li class="nav-item"><a class="nav-link" href="/ssis/registrar/dashboard"><i class="bi bi-speedometer2 mx-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/registrar/students"><i class="bi bi-people mx-2"></i>Students</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/registrar/enrollments"><i class="bi bi-journal-check mx-2"></i>Enrollments</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/registrar/grades/encode"><i class="bi bi-pencil-square mx-2"></i>Grade Encoding</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/registrar/documents/queue"><i class="bi bi-folder2-open mx-2"></i>Document Queue</a></li>
            <?php elseif ($role === 'cashier'): ?>
                <li class="nav-item"><a class="nav-link" href="/ssis/cashier/dashboard"><i class="bi bi-speedometer2 mx-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/cashier/payments/process"><i class="bi bi-credit-card-2-front mx-2"></i>Process Payment</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/cashier/reports/daily"><i class="bi bi-bar-chart-steps mx-2"></i>Daily Report</a></li>
            <?php elseif ($role === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="/ssis/admin/dashboard"><i class="bi bi-speedometer2 mx-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/admin/users"><i class="bi bi-people mx-2"></i>Users</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/admin/roles"><i class="bi bi-shield-lock mx-2"></i>Roles & Permissions</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/admin/system-settings"><i class="bi bi-gear mx-2"></i>System Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/admin/audit-logs"><i class="bi bi-clipboard-data mx-2"></i>Audit Logs</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/registrar/students"><i class="bi bi-person-badge mx-2"></i>Student Records</a></li>
                <li class="nav-item"><a class="nav-link" href="/ssis/department/clearance"><i class="bi bi-clipboard-check mx-2"></i>Clearance Queue</a></li>
            <?php endif; ?>
        </nav>

        <div class="p-3 border-top-var">
            <a class="nav-link text-warning" href="/ssis/logout">
                <i class="bi bi-box-arrow-right mx-2"></i>Logout
            </a>
        </div>
    </aside>

    <!-- Main -->
    <div class="main-wrap flex-grow-1 d-flex flex-column">
        <header class="header d-flex align-items-center justify-content-between px-4 py-2 header-fixed">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle d-lg-none" id="sidebarToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">
                    <i class="bi bi-list fs-1_25rem"></i>
                </button>
                <h1 class="h5 fw-bold mb-0 text-var-primary"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme" title="Toggle light/dark mode">
                    <i class="bi bi-moon-stars" id="themeIcon" class="fs-1_1rem"></i>
                </button>
                <div class="dropdown">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="User menu"
                       class="d-flex align-items-center text-decoration-none gap-2 text-var-primary">
                        <span class="user-avatar-custom">
                            <?= strtoupper(substr($_SESSION['account_login'] ?? 'U', 0, 1)) ?>
                        </span>
                        <span class="d-none d-sm-inline fw-medium"><?= htmlspecialchars($_SESSION['account_login'] ?? 'User') ?></span>
                        <i class="bi bi-chevron-down small text-var-tertiary"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($role === 'student'): ?>
                            <li><a class="dropdown-item" href="/ssis/student/profile">Profile</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="/ssis/logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main id="main-content" class="main-content flex-grow-1 overflow-y-auto" role="main">
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $key => $msg): ?>
                    <?php if (in_array($key, ['error', 'success']) && !empty(trim($msg))): ?>
                        <div class="alert alert-<?= $key === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert" aria-live="polite">
                            <i class="bi bi-<?= $key === 'error' ? 'exclamation-circle' : 'check-circle' ?> me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>
    </div>
</div>
<?php else: ?>
    <main id="main-content" class="main-content" role="main">
        <?= $content ?? '' ?>
    </main>
<?php endif; ?>

<script src="/ssis/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    "use strict";
    var here = window.location.pathname + window.location.search;
    document.querySelectorAll(".sidebar .nav-link").forEach(function (link) {
        var href = link.getAttribute("href") || "";
        if (href === here) {
            link.classList.add("active");
            link.setAttribute("aria-current", "page");
        } else {
            link.setAttribute("aria-current", "false");
        }
    });

    function runWhenReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    // Active nav link
    runWhenReady(function() {
        var here = window.location.pathname + window.location.search;
        document.querySelectorAll(".sidebar .nav-link").forEach(function (link) {
            var href = link.getAttribute("href") || "";
            if (href === here) {
                link.classList.add("active");
                link.setAttribute("aria-current", "page");
            } else {
                link.setAttribute("aria-current", "false");
            }
        });
    });

    // Theme toggle
    runWhenReady(function() {
        var toggle = document.getElementById('themeToggle');
        var icon = document.getElementById('themeIcon');
        var html = document.documentElement;

        function updateIcon(theme) {
            if (theme === 'dark') {
                icon.className = 'bi bi-moon-stars';
            } else {
                icon.className = 'bi bi-sun';
            }
        }

        function setTheme(theme) {
            html.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            updateIcon(theme);
        }

        // Initialize
        var currentTheme = localStorage.getItem('theme') || 'dark';
        updateIcon(currentTheme);

        if (toggle) {
            toggle.addEventListener('click', function() {
                var newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });
        }
    });

    // Sidebar toggle for mobile
    runWhenReady(function() {
        var toggle = document.getElementById('sidebarToggle');
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
            document.body.classList.remove('sidebar-open');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        }

        function openSidebar() {
            if (sidebar) sidebar.classList.add('open');
            if (overlay) overlay.classList.add('open');
            document.body.classList.add('sidebar-open');
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
        }

        if (toggle && sidebar) {
            toggle.addEventListener('click', function() {
                if (sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar on nav link click (mobile)
        document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    });

    // Quick link search
    runWhenReady(function() {
        var q = document.getElementById("quickLinkSearch");
        if (q) {
            var list = document.getElementById("quickLinkList");
            var warn = document.getElementById("noQuickMatch");
            q.addEventListener("input", function () {
                var term = q.value.toLowerCase();
                var items = list ? list.querySelectorAll("a") : [];
                var shown = 0;
                items.forEach(function (a) {
                    var ok = a.textContent.toLowerCase().indexOf(term) !== -1;
                    a.style.display = ok ? "" : "none";
                    if (ok) shown++;
                });
                if (warn) {
                    warn.textContent = shown ? "" : "No matching links found";
                    warn.classList.toggle("d-none", !!shown);
                }
            });
        }
    });
})();
</script>
</body>
</html>
