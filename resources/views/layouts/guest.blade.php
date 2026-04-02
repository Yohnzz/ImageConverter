<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ImgDrop') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0a0a0f; --surface: #111118; --surface2: #18181f;
            --border: #ffffff1a; --accent: #7c6aff; --accent2: #ff6a9b;
            --text: #f0f0f8; --muted: #8c8ca5; --danger: #ff4d6a; --success: #4ade80;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(ellipse 80% 50% at 20% 10%, #7c6aff18 0%, transparent 60%),
                radial-gradient(ellipse 70% 50% at 90% 90%, #ff6a9b12 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }
        .auth-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .auth-left, .auth-right {
            padding: clamp(1.5rem, 4vw, 3rem);
        }
        .auth-left {
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .brand {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .left-headline h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.4rem, 3vw, 2.2rem);
            line-height: 1.15;
            margin-bottom: 0.8rem;
        }
        .left-headline p { color: var(--muted); line-height: 1.7; max-width: 45ch; }
        .left-dots { display: flex; gap: 0.45rem; }
        .left-dots span {
            width: 7px; height: 7px; border-radius: 999px;
            background: #ffffff33;
        }
        .left-dots span.on { background: linear-gradient(135deg, var(--accent), var(--accent2)); }

        .auth-right {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .panel {
            width: min(460px, 100%);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: clamp(1.2rem, 3vw, 2rem);
        }
        .form-heading { margin-bottom: 1.4rem; }
        .form-heading h1 { font-family: 'Syne', sans-serif; font-size: 1.7rem; margin-bottom: 0.3rem; }
        .form-heading p { color: var(--muted); font-size: 0.92rem; }

        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            font-size: 0.78rem;
            color: var(--muted);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .field input[type="text"], .field input[type="email"], .field input[type="password"] {
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface2);
            color: var(--text);
            padding: 0.7rem 0.85rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px #7c6aff22;
        }
        .field-error { margin-top: 0.4rem; color: var(--danger); font-size: 0.8rem; }

        .btn-primary {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            background: linear-gradient(135deg, var(--accent), #9b59ff);
            color: white;
            cursor: pointer;
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            margin-top: 0.35rem;
        }
        .row-between { display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
        .check-label { color: var(--muted); font-size: 0.86rem; display: inline-flex; align-items: center; gap: 0.45rem; }
        .check-label input { accent-color: var(--accent); }
        .link { color: #a89bff; text-decoration: none; }
        .link:hover { text-decoration: underline; }
        .form-footer { text-align: center; color: var(--muted); font-size: 0.9rem; margin-top: 1.1rem; }
        .alert-info, .alert-success {
            border-radius: 10px;
            padding: 0.7rem 0.8rem;
            margin-bottom: 1rem;
            font-size: 0.88rem;
        }
        .alert-info { background: #7c6aff22; border: 1px solid #7c6aff40; color: #c4bbff; }
        .alert-success { background: #4ade8018; border: 1px solid #4ade8045; color: #89f5b4; }

        @media (max-width: 900px) {
            .auth-wrapper { grid-template-columns: 1fr; }
            .auth-left { border-right: 0; border-bottom: 1px solid var(--border); }
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-left">
        <div class="brand">ImgDrop ⚡</div>

        <div class="left-headline">
            {{ $leftHeadline ?? '' }}
        </div>

        <div class="left-dots">
            {{ $leftDots ?? '' }}
        </div>
    </div>

    <div class="auth-right">
        <div class="panel">
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
