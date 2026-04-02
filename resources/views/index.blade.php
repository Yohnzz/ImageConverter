<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ImgDrop — Gambar Jadi Link Instan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
         *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0a0a0f; --surface: #111118; --surface2: #18181f;
            --border: #ffffff0f; --border-hover: #ffffff22;
            --accent: #7c6aff; --accent2: #ff6a9b; --accent3: #6affd4;
            --text: #f0f0f8; --muted: #6b6b80; --success: #4ade80; --error: #ff4d6a;
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg); color: var(--text);
            min-height: 100vh; overflow-x: hidden;
        }
        body::before {
            content: ''; position: fixed; inset: 0;
            background-image:
                radial-gradient(ellipse 80% 50% at 20% 10%, #7c6aff18 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%, #ff6a9b12 0%, transparent 60%);
            pointer-events: none; z-index: 0;
        }

        /* NAVBAR */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: clamp(0.75rem, 4vw, 1rem) clamp(1rem, 5vw, 2rem);
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(20px); background: #0a0a0fcc;
            transition: all 0.3s ease;
        }
        .logo {
            font-family: 'Syne', sans-serif; 
            font-size: clamp(1.1rem, 5vw, 1.5rem); 
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; 
            background-clip: text; text-decoration: none;
        }
        
        /* HAMBURGER MENU */
        .hamburger {
            display: none; flex-direction: column; gap: 3px; 
            width: 24px; height: 20px; cursor: pointer; padding: 4px;
            z-index: 1001; transition: transform 0.3s ease;
        }
        .hamburger span {
            width: 100%; height: 2px; background: var(--text);
            transition: all 0.3s ease; border-radius: 2px;
        }
        .hamburger.active span:nth-child(1) { 
            transform: rotate(45deg) translate(6px, 6px); 
        }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { 
            transform: rotate(-45deg) translate(6px, -6px); 
        }
        
        .nav-right { 
            display: flex; align-items: center; gap: clamp(0.5rem, 3vw, 1rem); 
        }
        .nav-link {
            color: var(--muted); text-decoration: none; 
            font-size: clamp(0.75rem, 3vw, 0.9rem);
            font-weight: 500; transition: all 0.2s; padding: 0.5rem 0.75rem;
            border-radius: 6px; white-space: nowrap;
        }
        .nav-link:hover { color: var(--text); background: var(--surface2); }
        
        .nav-user {
            display: flex; align-items: center; gap: 0.5rem;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 99px; padding: 0.4rem 0.75rem 0.4rem 0.5rem;
            transition: all 0.2s;
        }
        .nav-avatar {
            width: clamp(24px, 6vw, 30px); height: clamp(24px, 6vw, 30px); 
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: clamp(0.65rem, 2.5vw, 0.8rem); font-weight: 700; 
            color: white; flex-shrink: 0;
        }
        .nav-name { 
            font-size: clamp(0.7rem, 2.5vw, 0.85rem); 
            color: var(--text); 
            max-width: clamp(80px, 15vw, 140px); 
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap; 
        }
        .logout-btn {
            background: none; border: none; color: var(--muted); cursor: pointer;
            font-size: clamp(0.7rem, 2.5vw, 0.8rem); font-family: 'DM Sans', sans-serif;
            padding: 0.5rem 0.75rem; border-radius: 6px; transition: all 0.2s;
        }
        .logout-btn:hover { color: var(--error); background: #ff4d6a18; }

        /* MOBILE MENU DROPDOWN */
        .mobile-menu {
            position: fixed; top: 0; right: -100%; width: 280px; height: 100vh;
            background: var(--surface); backdrop-filter: blur(20px);
            transition: right 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-left: 1px solid var(--border); z-index: 999;
            padding: 5rem 1.5rem 2rem;
        }
        .mobile-menu.active { right: 0; }
        .mobile-menu-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: #000000aa; opacity: 0; visibility: hidden;
            transition: all 0.4s ease; z-index: 998;
        }
        .mobile-menu-overlay.active { opacity: 1; visibility: visible; }
        .mobile-menu-item {
            display: block; padding: 1rem 0; font-size: 1rem; color: var(--text);
            text-decoration: none; border-bottom: 1px solid var(--border);
            transition: color 0.2s;
        }
        .mobile-menu-item:hover { color: var(--accent); }
        .mobile-menu-item:last-child { border-bottom: none; }
        .mobile-user {
            margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);
            display: flex; align-items: center; gap: 0.75rem;
        }

        /* MAIN CONTENT */
        main { position: relative; z-index: 1; padding-top: clamp(5.5rem, 18vw, 7rem); }

        .hero {
            text-align: center; padding: clamp(2rem, 8vw, 4rem) 1rem clamp(1.5rem, 6vw, 2.5rem);
            max-width: min(90vw, 700px); margin: 0 auto;
        }
        .hero-tag {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: #7c6aff18; border: 1px solid #7c6aff33; color: #a89bff;
            padding: clamp(0.3rem, 2vw, 0.4rem) clamp(0.8rem, 4vw, 1rem); 
            border-radius: 99px; font-size: clamp(0.7rem, 2.5vw, 0.85rem);
            font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 1.2rem;
        }
        .hero-tag::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent); animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }
        h1 {
            font-family: 'Syne', sans-serif; 
            font-size: clamp(1.75rem, 8vw, 4rem);
            font-weight: 800; line-height: 1.1; 
            letter-spacing: clamp(-1px, -0.03em, -2px); 
            margin-bottom: 1rem;
        }
        h1 span {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 50%, var(--accent3) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .hero-desc { 
            color: var(--muted); 
            font-size: clamp(0.9rem, 3.5vw, 1.1rem); 
            line-height: 1.7; 
            max-width: min(90vw, 500px); margin: 0 auto 1rem auto; 
        }

        /* UPLOAD CARD */
        .card-wrap { 
            max-width: min(95vw, 600px); 
            margin: clamp(1rem, 4vw, 2rem) auto clamp(2rem, 8vw, 4rem); 
            padding: 0 clamp(0.5rem, 2vw, 1.5rem); 
        }
        .upload-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: clamp(16px, 5vw, 24px); overflow: hidden; 
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .upload-card:hover { 
            border-color: var(--border-hover); 
            box-shadow: 0 10px 40px #00000044;
        }

        /* DROP ZONE */
        .drop-zone {
            padding: clamp(1.75rem, 8vw, 3rem) clamp(1.5rem, 6vw, 2.5rem); 
            border-bottom: 1px solid var(--border);
            text-align: center; cursor: pointer; position: relative; 
            transition: all 0.3s ease;
        }
        .drop-zone.dragover { background: #7c6aff08; }
        .drop-zone input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; 
            width: 100%; height: 100%;
        }
        .drop-icon {
            width: clamp(48px, 12vw, 64px); height: clamp(48px, 12vw, 64px); 
            margin: 0 auto clamp(0.75rem, 4vw, 1.25rem);
            background: linear-gradient(135deg, #7c6aff22, #ff6a9b11);
            border: 1px solid #7c6aff33; border-radius: clamp(12px, 4vw, 20px);
            display: flex; align-items: center; justify-content: center;
            font-size: clamp(1.25rem, 6vw, 1.75rem); transition: all 0.3s;
        }
        .drop-zone:hover .drop-icon { 
            transform: scale(1.08) rotate(-5deg); 
            box-shadow: 0 8px 25px #7c6aff33;
        }
        .drop-title { 
            font-family: 'Syne', sans-serif; 
            font-size: clamp(0.95rem, 4vw, 1.15rem); 
            font-weight: 700; margin-bottom: 0.5rem; 
        }
        .drop-subtitle { 
            font-size: clamp(0.75rem, 3vw, 0.9rem); 
            color: var(--muted); 
        }

        /* PROGRESS BAR */
        .progress-bar { 
            display: none; height: 4px; background: var(--surface2); overflow: hidden; 
        }
        .progress-bar.visible { display: block; }
        .progress-fill {
            height: 100%; width: 0%; transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(90deg, var(--accent), var(--accent2), var(--accent3));
            background-size: 200% 100%; animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* PREVIEW */
        .preview-wrap { 
            display: none; padding: clamp(1rem, 5vw, 1.5rem) clamp(1.25rem, 6vw, 2rem); 
            border-bottom: 1px solid var(--border); position: relative;
        }
        .preview-wrap.visible { display: flex; gap: clamp(0.75rem, 4vw, 1.25rem); align-items: center; }
        .preview-thumb {
            width: clamp(60px, 18vw, 80px); height: clamp(60px, 18vw, 80px); 
            border-radius: 12px; object-fit: cover;
            border: 2px solid var(--border); flex-shrink: 0; transition: all 0.3s;
        }
        .preview-info { flex: 1; min-width: 0; }
        .preview-name { 
            font-weight: 600; font-size: clamp(0.8rem, 3vw, 0.95rem); 
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; 
            margin-bottom: 0.25rem;
        }
        .preview-size { 
            font-size: clamp(0.7rem, 2.5vw, 0.8rem); color: var(--muted); 
        }
        .preview-remove {
            position: absolute; top: clamp(0.5rem, 3vw, 0.75rem); 
            right: clamp(0.5rem, 3vw, 0.75rem);
            background: var(--surface2); border: 1px solid var(--border); color: var(--muted);
            width: clamp(36px, 10vw, 44px); height: clamp(36px, 10vw, 44px); 
            border-radius: 10px; cursor: pointer; font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center; 
            transition: all 0.25s; flex-shrink: 0;
        }
        .preview-remove:hover { 
            background: #ff4d6a22; border-color: var(--error); color: var(--error);
            transform: scale(1.05);
        }

        /* FORM */
        .form-bottom { 
            padding: clamp(1rem, 5vw, 1.5rem) clamp(1.25rem, 6vw, 2rem); 
            display: flex; flex-direction: column; gap: clamp(0.75rem, 4vw, 1.25rem); 
        }
        .alias-label { 
            font-size: clamp(0.7rem, 2.8vw, 0.85rem); 
            color: var(--muted); font-weight: 500; margin-bottom: 0.5rem; 
        }
        .alias-row {
            display: flex; align-items: stretch; flex-direction: row;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 12px; overflow: hidden; transition: all 0.3s;
        }
        .alias-row:focus-within { 
            border-color: var(--accent); 
            box-shadow: 0 0 0 3px #7c6aff22; 
            background: var(--surface2);
        }
        .alias-prefix {
            padding: clamp(0.75rem, 4vw, 1rem) clamp(1rem, 5vw, 1.25rem); 
            font-size: clamp(0.75rem, 3vw, 0.85rem); color: var(--muted);
            white-space: nowrap; 
            background: #0a0a0f88; border-right: 1px solid var(--border);
            user-select: none; font-weight: 500;
            display: flex; align-items: center;
        }
        .alias-input {
            flex: 1; padding: clamp(0.75rem, 4vw, 1rem) clamp(1rem, 5vw, 1.25rem);
            background: transparent; border: none; color: var(--text);
            font-size: clamp(0.8rem, 3vw, 0.9rem); font-family: 'DM Sans', sans-serif;
            outline: none; transition: all 0.2s;
        }
        .alias-input::placeholder { color: var(--muted); }
        .alias-input:hover { background: #ffffff05; }
        .alias-input:focus { background: #ffffff08; }
        .alias-hint { 
            font-size: clamp(0.68rem, 2.6vw, 0.78rem); 
            color: var(--muted); margin-top: 0.5rem; 
            line-height: 1.4;
        }

        .submit-btn {
            width: 100%; padding: clamp(1rem, 5vw, 1.25rem);
            background: linear-gradient(135deg, var(--accent), #9b59ff);
            border: none; border-radius: 14px; color: white;
            font-family: 'Syne', sans-serif; font-size: clamp(0.9rem, 4vw, 1.1rem); 
            font-weight: 700; letter-spacing: 0.03em; cursor: pointer; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; overflow: hidden; min-height: 52px;
            box-shadow: 0 4px 15px #7c6aff33;
        }
        .submit-btn::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 30%, #ffffff25 50%, transparent);
            opacity: 0; transition: opacity 0.3s; transform: translateX(-100%);
        }
        .submit-btn:hover::before { 
            opacity: 1; transform: translateX(100%); 
        }
        .submit-btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 12px 35px #7c6aff55; 
        }
        .submit-btn:active { transform: translateY(0); }
        .submit-btn:disabled { 
            opacity: 0.5; cursor: not-allowed; transform: none; 
            box-shadow: 0 2px 8px #7c6aff22; 
        }

        /* ERROR */
        .error-msg {
            display: none; margin: 0 clamp(1rem, 5vw, 1.5rem) clamp(1rem, 5vw, 1.25rem);
            background: #ff4d6a15; border: 1px solid #ff4d6a44;
            border-radius: 12px; padding: clamp(0.75rem, 4vw, 1rem) clamp(1rem, 5vw, 1.25rem); 
            font-size: clamp(0.8rem, 3vw, 0.9rem); color: var(--error); line-height: 1.5;
        }
        .error-msg.visible { display: block; animation: shake 0.4s ease-in-out; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }

        /* RESULT */
        .result-card {
            display: none; margin: 0 clamp(1rem, 5vw, 1.5rem) clamp(1.5rem, 6vw, 2rem);
            background: #4ade8018; border: 1px solid #4ade8044;
            border-radius: 16px; padding: clamp(1.25rem, 6vw, 1.5rem);
            animation: slideIn 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .result-card.visible { display: block; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-12px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .result-label { 
            font-size: clamp(0.7rem, 2.8vw, 0.85rem); 
            text-transform: uppercase; letter-spacing: 0.1em; 
            color: var(--success); font-weight: 700; margin-bottom: 1rem; 
        }
        .result-url-row {
            display: flex; align-items: center; gap: 0.75rem;
            background: #0a0a0f99; border: 1px solid var(--border);
            border-radius: 10px; padding: clamp(0.75rem, 4vw, 1rem) clamp(1rem, 5vw, 1.25rem); 
            margin-bottom: 1rem;
        }
        .result-url { 
            flex: 1; font-size: clamp(0.8rem, 3.2vw, 0.95rem); 
            color: var(--accent3); word-break: break-all; text-decoration: none; 
            min-width: 0; font-weight: 500;
        }
        .result-url:hover { text-decoration: underline; }
        .copy-btn {
            background: var(--surface2); border: 1px solid var(--border); color: var(--text);
            padding: clamp(0.5rem, 3vw, 0.75rem) clamp(1rem, 4vw, 1.25rem); 
            border-radius: 8px; font-size: clamp(0.75rem, 3vw, 0.85rem); 
            cursor: pointer; font-family: 'DM Sans', sans-serif; font-weight: 600; 
            white-space: nowrap; transition: all 0.25s; flex-shrink: 0; min-height: 44px;
        }
        .copy-btn:hover { 
            background: var(--accent); border-color: var(--accent); 
            transform: translateY(-1px);
        }
        .copy-btn.copied { 
            background: var(--success); border-color: var(--success); color: #000; 
            animation: pulse-green 0.6s ease;
        }
        @keyframes pulse-green {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .result-open-btn {
            display: block; padding: clamp(0.75rem, 4vw, 1rem); 
            background: transparent; border: 1px solid #4ade8044; 
            border-radius: 10px; color: var(--success); font-weight: 600;
            font-size: clamp(0.75rem, 3vw, 0.9rem); font-family: 'DM Sans', sans-serif; 
            cursor: pointer; text-align: center; text-decoration: none; 
            transition: all 0.25s; margin-bottom: 1rem;
        }
        .result-open-btn:hover { 
            background: #4ade8022; transform: translateY(-1px);
            box-shadow: 0 6px 20px #4ade8033;
        }
        .result-meta { 
            font-size: clamp(0.68rem, 2.6vw, 0.8rem); 
            color: var(--muted); text-align: center;
        }

        /* SPINNER */
        .spinner {
            display: inline-block; width: 16px; height: 16px;
            border: 2px solid #ffffff44; border-top-color: white;
            border-radius: 50%; animation: spin 0.8s linear infinite;
            margin-right: 0.5rem; vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* RECENT UPLOADS */
        .section { 
            max-width: min(95vw, 700px); 
            margin: 0 auto clamp(3rem, 12vw, 6rem); 
            padding: 0 clamp(0.75rem, 3vw, 1.5rem); 
        }
        .section-header { 
            display: flex; justify-content: space-between; align-items: baseline; 
            margin-bottom: clamp(1rem, 5vw, 1.5rem); 
        }
        .section-title { 
            font-family: 'Syne', sans-serif; 
            font-size: clamp(0.8rem, 3vw, 1rem); 
            font-weight: 700; color: var(--muted); 
            text-transform: uppercase; letter-spacing: 0.1em; 
        }
        .see-all { 
            font-size: clamp(0.75rem, 3vw, 0.9rem); 
            color: var(--accent); text-decoration: none; 
            font-weight: 500; transition: all 0.2s;
        }
        .see-all:hover { opacity: 0.8; transform: translateX(4px); }
        
        .recent-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(clamp(160px, 42vw, 200px), 1fr)); 
            gap: clamp(0.75rem, 3vw, 1.25rem); 
        }
        .recent-item {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 16px; overflow: hidden; transition: all 0.3s;
            text-decoration: none; color: inherit; display: block;
        }
        .recent-item:hover { 
            border-color: var(--border-hover); 
            transform: translateY(-4px) scale(1.02); 
            box-shadow: 0 12px 35px #00000044;
        }
        .recent-thumb { 
            width: 100%; aspect-ratio: 16/9; object-fit: cover; 
            display: block; background: var(--surface2); 
            transition: transform 0.3s;
        }
        .recent-item:hover .recent-thumb { transform: scale(1.05); }
        .recent-meta { padding: clamp(0.75rem, 4vw, 1rem); }
        .recent-name { 
            font-size: clamp(0.7rem, 2.8vw, 0.85rem); 
            font-weight: 500; white-space: nowrap; overflow: hidden; 
            text-overflow: ellipsis; margin-bottom: 0.5rem; 
        }
        .recent-code { 
            font-size: clamp(0.65rem, 2.5vw, 0.75rem); 
            color: var(--accent); font-family: monospace; font-weight: 500;
        }
        .recent-visits { 
            font-size: clamp(0.65rem, 2.4vw, 0.75rem); 
            color: var(--muted); font-weight: 600; margin-bottom: 0.25rem;
            display: block;
        }

        footer { 
            text-align: center; padding: clamp(2rem, 10vw, 3rem) clamp(1rem, 5vw, 2rem); 
            border-top: 1px solid var(--border); color: var(--muted); 
            font-size: clamp(0.75rem, 3vw, 0.85rem); 
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 768px) {
            .hamburger { display: flex; }
            .nav-right { display: none; }
            .nav-right.mobile-active { display: flex; flex-direction: column; 
                position: fixed; top: 100%; left: 0; right: 0; 
                background: var(--surface2); padding: 1rem; gap: 0.5rem;
                border-top: 1px solid var(--border); }
        }

        @media (max-width: 600px) {
            .preview-wrap { padding: 1.25rem; }
            .recent-grid { grid-template-columns: repeat(2, 1fr); }
            .alias-row { flex-direction: column; }
            .alias-prefix { border-right: none; border-bottom: 1px solid var(--border); }
        }

        @media (max-width: 480px) {
            .recent-grid { grid-template-columns: 1fr; }
            .section-header { flex-direction: column; gap: 0.75rem; align-items: stretch; }
            .see-all { text-align: center; padding: 0.75rem; background: var(--surface2); 
                border-radius: 8px; border: 1px solid var(--border); }
            .alias-row { flex-direction: column; }
            .alias-prefix { 
                border-right: none; 
                border-bottom: 1px solid var(--border);
                justify-content: flex-start;
                padding: clamp(0.5rem, 2vw, 0.75rem) clamp(0.75rem, 3vw, 1rem);
            }
            .alias-input { 
                padding: clamp(0.6rem, 2.5vw, 0.75rem) clamp(0.75rem, 3vw, 1rem);
            }
        }

        @media (max-width: 360px) {
            .card-wrap { padding: 0 0.5rem; }
            main { padding-top: 4.5rem; }
        }

        /* Landscape Tablet */
        @media (max-height: 500px) and (min-width: 768px) {
            main { padding-top: 4rem; }
            .hero { padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body>

<!-- HAMBURGER MENU OVERLAY -->
<div class="mobile-menu-overlay" id="menuOverlay"></div>

<!-- MOBILE MENU DROPDOWN -->
<div class="mobile-menu" id="mobileMenu">
    <a href="/links" class="mobile-menu-item">📋 Semua Link Saya</a>
    @if(auth()->check())
        <div class="mobile-user">
            <div class="nav-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div style="font-weight: 600; margin-bottom: 0.25rem;">{{ auth()->user()->name }}</div>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="mobile-menu-item" style="font-weight: 500; color: var(--error);">🚪 Keluar</button>
                </form>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}" class="mobile-menu-item">🔐 Login</a>
    @endif
</div>

<nav>
    <a href="/" class="logo">ImgDrop ⚡</a>
    
    <!-- HAMBURGER ICON -->
    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>
    
    <!-- DESKTOP NAV -->
    <div class="nav-right" id="navRight">
        <a href="/links" class="nav-link">📋 Semua Link</a>
        @if(auth()->check())
            <div class="nav-user">
                <div class="nav-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="nav-name">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        @else
            <div class="nav-user">
                <div class="nav-avatar">G</div>
                <span class="nav-name">Guest</span>
            </div>
            <a href="{{ route('login') }}" class="nav-link">🔐 Login</a>
        @endif
    </div>
</nav>

<main>
    <section class="hero">
        <div class="hero-tag">Upload · Convert · Bagikan</div>
        <h1>Gambar Kamu<br><span>Jadi Link Instan</span></h1>
        <p class="hero-desc">Upload gambar, dapatkan short link yang bisa langsung dibagikan. Simpel, cepat, tanpa ribet.</p>
        @if($isGuest)
            <p class="hero-desc" style="margin-top: 0.75rem;">
                Mode guest aktif: privasi tetap aman, sisa upload hari ini {{ $guestRemainingUploads }}/5, maksimal {{ $maxUploadMb }} MB per file.
            </p>
        @endif
    </section>

    <div class="card-wrap">
        <div class="upload-card">
            <div class="drop-zone" id="dropZone">
                <input type="file" id="fileInput" accept="image/jpeg,image/png,image/gif,image/webp">
                <div class="drop-icon">🖼️</div>
                <div class="drop-title">Klik atau drag gambar ke sini</div>
                <div class="drop-subtitle">JPG, PNG, GIF, WebP — Maks. {{ $maxUploadMb }} MB</div>
            </div>

            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill"></div>
            </div>

            <div class="preview-wrap" id="previewWrap">
                <img class="preview-thumb" id="previewThumb" src="" alt="preview">
                <div class="preview-info">
                    <div class="preview-name" id="previewName">—</div>
                    <div class="preview-size" id="previewSize">—</div>
                </div>
                <button class="preview-remove" id="removeBtn" title="Hapus">✕</button>
            </div>

            <div class="form-bottom">
                <div>
                    <label class="alias-label">Short Link (opsional)</label>
                    <div class="alias-row">
                        <span class="alias-prefix">{{ url('/i/') }}/</span>
                        <input type="text" class="alias-input" id="aliasInput"
                               placeholder="nama-custom" maxlength="30">
                    </div>
                    <div class="alias-hint">Kosongi untuk kode otomatis. Contoh: foto-wisuda</div>
                </div>
                <button class="submit-btn" id="submitBtn" disabled>⚡ Generate Link</button>
            </div>

            <div class="error-msg" id="errorMsg"></div>

            <div class="result-card" id="resultCard">
                <div class="result-label">✓ Link Berhasil Dibuat!</div>
                <div class="result-url-row">
                    <a class="result-url" id="resultUrl" href="#" target="_blank">—</a>
                    <button class="copy-btn" id="copyBtn">Salin</button>
                </div>
                <a class="result-open-btn" id="resultOpenBtn" href="#" target="_blank">🔗 Buka Link di Tab Baru →</a>
                <div class="result-meta" id="resultMeta"></div>
            </div>
        </div>
    </div>

    @if($recentLinks->count())
    <section class="section">
        <div class="section-header">
            <div class="section-title">Upload Terakhirmu</div>
            <a href="/links" class="see-all">Lihat semua</a>
        </div>
        <div class="recent-grid">
            @foreach($recentLinks as $link)
            <a href="{{ $link->getShortUrl() }}" target="_blank" class="recent-item">
                <img class="recent-thumb" src="{{ $link->getImageUrl() }}"
                     alt="{{ $link->original_filename }}" loading="lazy"
                     onerror="this.style.background='#18181f'">
                <div class="recent-meta">
    <span class="recent-visits">👁 {{ $link->visit_count }}</span>
    <div class="recent-name">{{ $link->original_filename }}</div>
    
    @if(auth()->check() && auth()->user()->isAdmin())
        <div style="font-size: 0.65rem; color: var(--accent2); margin-top: 4px;">
            👤 Owner: {{ $link->user->name ?? 'Unknown' }}
        </div>
    @endif
    
    <div class="recent-code">/i/{{ $link->custom_alias ?? $link->short_code }}</div>
</div>
            </a>
            @endforeach
        </div>
    </section>
    @endif
</main>


<script>
const fileInput     = document.getElementById('fileInput');
const dropZone      = document.getElementById('dropZone');
const previewWrap   = document.getElementById('previewWrap');
const previewThumb  = document.getElementById('previewThumb');
const previewName   = document.getElementById('previewName');
const previewSize   = document.getElementById('previewSize');
const removeBtn     = document.getElementById('removeBtn');
const aliasInput    = document.getElementById('aliasInput');
const submitBtn     = document.getElementById('submitBtn');
const errorMsg      = document.getElementById('errorMsg');
const resultCard    = document.getElementById('resultCard');
const resultUrl     = document.getElementById('resultUrl');
const resultOpenBtn = document.getElementById('resultOpenBtn');
const copyBtn       = document.getElementById('copyBtn');
const resultMeta    = document.getElementById('resultMeta');
const progressBar   = document.getElementById('progressBar');
const progressFill  = document.getElementById('progressFill');

let selectedFile = null;

function formatBytes(b) {
    if (b >= 1048576) return (b / 1048576).toFixed(1) + ' MB';
    if (b >= 1024)    return (b / 1024).toFixed(0) + ' KB';
    return b + ' B';
}

function validateImageFile(file) {
    // Tentukan max size berdasarkan login status
    const isLoggedIn = document.querySelector('meta[name="csrf-token"]') ? true : false;
    const maxSizeBytes = isLoggedIn ? 104857600 : 5242880; // 100MB vs 5MB
    const maxSizeMb = isLoggedIn ? 100 : 5;
    
    // Validasi 1: Tipe file
    if (!file.type.startsWith('image/')) {
        return { valid: false, message: '❌ File harus berupa gambar! Gunakan JPG, PNG, GIF, atau WebP.' };
    }

    // Validasi 2: Format yang didukung
    const allowedFormats = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedFormats.includes(file.type)) {
        return { valid: false, message: '❌ Format tidak didukung! Gunakan JPG, PNG, GIF, atau WebP.' };
    }

    // Validasi 3: Ukuran file
    if (file.size > maxSizeBytes) {
        const fileSizeMb = (file.size / 1048576).toFixed(2);
        return { 
            valid: false, 
            message: isLoggedIn 
                ? `❌ File terlalu besar! File Anda: ${fileSizeMb} MB, Maksimal: ${maxSizeMb} MB.`
                : `❌ File terlalu besar! File Anda: ${(file.size / 1024).toFixed(0)} KB, Maksimal: ${maxSizeMb} MB. Silakan login untuk upload hingga 100 MB.`
        };
    }

    return { valid: true, message: '' };
}

function showPreview(file) {
    selectedFile = file;
    const reader = new FileReader();
    reader.onload = e => {
        previewThumb.src = e.target.result;
        previewName.textContent = file.name;
        previewSize.textContent = formatBytes(file.size);
        previewWrap.classList.add('visible');
        submitBtn.disabled = false;
        resultCard.classList.remove('visible');
        errorMsg.classList.remove('visible');
    };
    reader.readAsDataURL(file);
}

function clearFile() {
    selectedFile = null;
    fileInput.value = '';
    previewWrap.classList.remove('visible');
    submitBtn.disabled = true;
}

fileInput.addEventListener('change', () => { 
    if (fileInput.files[0]) {
        const file = fileInput.files[0];
        const validation = validateImageFile(file);
        
        if (!validation.valid) {
            errorMsg.textContent = validation.message;
            errorMsg.classList.add('visible');
            fileInput.value = ''; // Clear input
            clearFile();
        } else {
            errorMsg.classList.remove('visible');
            selectedFile = file;
            const reader = new FileReader();
            reader.onload = e => {
                previewThumb.src = e.target.result;
                previewName.textContent = file.name;
                previewSize.textContent = formatBytes(file.size);
                previewWrap.classList.add('visible');
                submitBtn.disabled = false;
                resultCard.classList.remove('visible');
            };
            reader.readAsDataURL(file);
        }
    }
});

removeBtn.addEventListener('click', () => {
    clearFile();
    resultCard.classList.remove('visible');
    errorMsg.classList.remove('visible');
});

// Auto convert spaces to hyphens in alias input
aliasInput.addEventListener('input', (e) => {
    e.target.value = e.target.value.replace(/\s+/g, '-').toLowerCase();
});

dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    
    if (!file) return;
    
    const validation = validateImageFile(file);
    
    if (!validation.valid) {
        errorMsg.textContent = validation.message;
        errorMsg.classList.add('visible');
    } else {
        errorMsg.classList.remove('visible');
        selectedFile = file;
        const reader = new FileReader();
        reader.onload = e => {
            previewThumb.src = e.target.result;
            previewName.textContent = file.name;
            previewSize.textContent = formatBytes(file.size);
            previewWrap.classList.add('visible');
            submitBtn.disabled = false;
            resultCard.classList.remove('visible');
        };
        reader.readAsDataURL(file);
    }
});

submitBtn.addEventListener('click', async () => {
    if (!selectedFile) return;
    
    // Reset state
    errorMsg.classList.remove('visible');
    errorMsg.style.color = 'var(--error)'; // Pastikan warna error
    resultCard.classList.remove('visible');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner"></span>Uploading...';

    progressBar.classList.add('visible');
    let prog = 0;
    const iv = setInterval(() => {
        prog = Math.min(prog + Math.random() * 15, 85);
        progressFill.style.width = prog + '%';
    }, 150);

    const fd = new FormData();
    fd.append('image', selectedFile);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    const alias = aliasInput.value.trim();
    if (alias) fd.append('custom_alias', alias);

    try {
        const res = await fetch('/upload', { 
            method: 'POST', 
            body: fd,
            headers: {
                'Accept': 'application/json', // Memastikan Laravel kirim JSON
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await res.json();
        clearInterval(iv);
        progressFill.style.width = '100%';
        setTimeout(() => { progressBar.classList.remove('visible'); progressFill.style.width = '0%'; }, 600);

        if (res.ok && data.success) {
            // Berhasil
            resultUrl.setAttribute('href', data.short_url);
            resultUrl.textContent = data.short_url;
            resultOpenBtn.setAttribute('href', data.short_url);
            resultMeta.textContent = '📁 ' + data.filename + ' · ' + data.size;
            resultCard.classList.add('visible');
            resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            clearFile();
            aliasInput.value = '';
        } else {
            // Penanganan Error Spesifik berdasarkan Status Code atau Response
            let errorText = 'Terjadi kesalahan sistem.';

            if (res.status === 413) {
                errorText = '❌ File terlalu besar! Maksimal ukuran adalah ' + '<div class="drop-subtitle" id="dropSubtitle">JPG, PNG, GIF, WebP — Maks. {{ $maxUploadMb }} MB</div>';
            } else if (res.status === 429) {
                errorText = '⚠️ Limit tercapai! Kamu sudah mencapai batas upload gratis hari ini. Silakan login atau coba lagi besok.';
            } else if (res.status === 422) {
                // Error Validasi Laravel (Format file salah, alias duplikat, dll)
                errorText = data.errors ? Object.values(data.errors).flat().join(' ') : data.message;
            } else {
                errorText = data.message || 'Upload gagal. Silakan coba beberapa saat lagi.';
            }

            errorMsg.innerHTML = errorText;
            errorMsg.classList.add('visible');
        }
    } catch (e) {
        clearInterval(iv);
        progressBar.classList.remove('visible');
        // Error koneksi atau CORS
        errorMsg.innerHTML = '🌐 Koneksi terputus atau bermasalah. Pastikan internetmu stabil dan coba lagi.';
        errorMsg.classList.add('visible');
        console.error(e);
    }

    submitBtn.disabled = false;
    submitBtn.innerHTML = '⚡ Generate Link';
});

copyBtn.addEventListener('click', () => {
    const url = resultUrl.getAttribute('href');
    const done = () => {
        copyBtn.textContent = '✓ Tersalin!';
        copyBtn.classList.add('copied');
        setTimeout(() => { copyBtn.textContent = 'Salin'; copyBtn.classList.remove('copied'); }, 2000);
    };
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(done);
    } else {
        const ta = document.createElement('textarea');
        ta.value = url; document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta); done();
    }
});

/* NAVBAR MOBILE SCRIPT */
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
const menuOverlay = document.getElementById('menuOverlay');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    mobileMenu.classList.toggle('active');
    menuOverlay.classList.toggle('active');
    document.body.style.overflow = hamburger.classList.contains('active') ? 'hidden' : '';
});

menuOverlay.addEventListener('click', () => {
    hamburger.classList.remove('active');
    mobileMenu.classList.remove('active');
    menuOverlay.classList.remove('active');
    document.body.style.overflow = '';
});

// Close menu on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        hamburger.classList.remove('active');
        mobileMenu.classList.remove('active');
        menuOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
});
</script>
</body>
</html>
