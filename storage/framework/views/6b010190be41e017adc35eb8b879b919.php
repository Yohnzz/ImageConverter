<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Nexus')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Sora', sans-serif;
            background: #f4f5f7;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            color: #1a1a2e;
        }

        .auth-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ── LEFT PANEL ── */
        .auth-left {
            width: 40%;
            background: linear-gradient(145deg, #042C53 0%, #185FA5 60%, #378ADD 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -40px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .brand-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .brand-icon svg {
            width: 18px; height: 18px;
            stroke: white;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 600;
            color: white;
            letter-spacing: 0.05em;
        }

        .left-headline {
            position: relative;
            z-index: 1;
        }

        .left-headline h2 {
            font-size: 22px;
            font-weight: 300;
            line-height: 1.45;
            color: white;
            margin-bottom: 12px;
            letter-spacing: -0.01em;
        }

        .left-headline p {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
        }

        .left-dots {
            display: flex;
            gap: 6px;
            position: relative;
            z-index: 1;
        }

        .left-dots span {
            display: block;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
        }

        .left-dots span.on { background: white; }

        /* ── RIGHT PANEL ── */
        .auth-right {
            flex: 1;
            background: #ffffff;
            padding: 3rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* ── FORM ELEMENTS ── */
        .form-heading { margin-bottom: 2rem; }

        .form-heading h1 {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 5px;
        }

        .form-heading p {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        .field { margin-bottom: 1.2rem; }

        .field label {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-family: 'JetBrains Mono', monospace;
        }

        .field input[type="text"],
        .field input[type="email"],
        .field input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #0f172a;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .field input:focus {
            border-color: #185FA5;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(24,95,165,0.1);
        }

        .field input::placeholder { color: #cbd5e1; font-size: 13px; }

        .field-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
        }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 12px;
            background: #185FA5;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            letter-spacing: 0.01em;
            margin-top: 0.5rem;
            text-align: center;
        }

        .btn-primary:hover { background: #0C447C; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            gap: 12px;
        }

        .check-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
        }

        .check-label input[type=checkbox] {
            accent-color: #185FA5;
            width: 14px; height: 14px;
        }

        .link {
            font-size: 13px;
            color: #185FA5;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .link:hover { color: #0C447C; }

        .form-footer {
            margin-top: 1.25rem;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 1.5rem 0;
        }

        .alert-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 13px;
            color: #1d4ed8;
            margin-bottom: 1.25rem;
            line-height: 1.5;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 13px;
            color: #15803d;
            margin-bottom: 1.25rem;
        }

        .secure-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-family: 'JetBrains Mono', monospace;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 1.5rem;
        }

        .secure-badge .dot {
            width: 6px; height: 6px;
            background: #22c55e;
            border-radius: 50%;
        }

        @media (max-width: 680px) {
            .auth-left { display: none; }
            .auth-right { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-left">
            <div class="brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <span class="brand-name">NEXUS</span>
            </div>

            <div class="left-headline">
                <?php echo e($leftHeadline ?? ''); ?>

            </div>

            <div class="left-dots">
                <?php echo e($leftDots ?? ''); ?>

            </div>
        </div>

        <div class="auth-right">
            <?php echo e($slot); ?>

        </div>
    </div>
</body>
</html>
<?php /**PATH D:\ImageConverter\imageconvert\resources\views/layouts/guest.blade.php ENDPATH**/ ?>