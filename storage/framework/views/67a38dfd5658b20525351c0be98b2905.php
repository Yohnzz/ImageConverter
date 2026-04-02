<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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

        /* NAV */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 1rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(20px); background: #0a0a0fcc;
        }
        .logo {
            font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            text-decoration: none;
        }
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .nav-link {
            color: var(--muted); text-decoration: none; font-size: 0.85rem;
            font-weight: 500; transition: color 0.2s;
        }
        .nav-link:hover { color: var(--text); }
        .nav-user {
            display: flex; align-items: center; gap: 0.5rem;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 99px; padding: 0.35rem 0.8rem 0.35rem 0.5rem;
        }
        .nav-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; color: white; flex-shrink: 0;
        }
        .nav-name { font-size: 0.8rem; color: var(--text); max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .logout-btn {
            background: none; border: none; color: var(--muted); cursor: pointer;
            font-size: 0.75rem; font-family: 'DM Sans', sans-serif;
            padding: 0; transition: color 0.2s;
        }
        .logout-btn:hover { color: var(--error); }

        /* MAIN */
        main { position: relative; z-index: 1; padding-top: 6rem; }

        .hero {
            text-align: center; padding: 3rem 1.5rem 2rem;
            max-width: 700px; margin: 0 auto;
        }
        .hero-tag {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: #7c6aff18; border: 1px solid #7c6aff33; color: #a89bff;
            padding: 0.35rem 0.9rem; border-radius: 99px; font-size: 0.78rem;
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
            font-family: 'Syne', sans-serif; font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 0.8rem;
        }
        h1 span {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 50%, var(--accent3) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .hero-desc { color: var(--muted); font-size: 1rem; line-height: 1.7; max-width: 480px; margin: 0 auto; }

        /* CARD */
        .card-wrap { max-width: 560px; margin: 1.5rem auto 4rem; padding: 0 1.5rem; }
        .upload-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 20px; overflow: hidden; transition: border-color 0.3s;
        }
        .upload-card:hover { border-color: var(--border-hover); }

        /* DROP ZONE */
        .drop-zone {
            padding: 2.5rem 2rem; border-bottom: 1px solid var(--border);
            text-align: center; cursor: pointer; position: relative; transition: background 0.3s;
        }
        .drop-zone.dragover { background: #7c6aff08; }
        .drop-zone input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .drop-icon {
            width: 56px; height: 56px; margin: 0 auto 1rem;
            background: linear-gradient(135deg, #7c6aff22, #ff6a9b11);
            border: 1px solid #7c6aff33; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; transition: transform 0.3s;
        }
        .drop-zone:hover .drop-icon { transform: scale(1.05) rotate(-3deg); }
        .drop-title { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; margin-bottom: 0.3rem; }
        .drop-subtitle { font-size: 0.8rem; color: var(--muted); }

        /* PROGRESS */
        .progress-bar { display: none; height: 3px; background: var(--surface2); overflow: hidden; }
        .progress-bar.visible { display: block; }
        .progress-fill {
            height: 100%; width: 0%;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            transition: width 0.3s ease;
        }

        /* PREVIEW */
        .preview-wrap { display: none; padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--border); }
        .preview-wrap.visible { display: flex; gap: 1rem; align-items: center; }
        .preview-thumb {
            width: 64px; height: 64px; border-radius: 10px; object-fit: cover;
            border: 1px solid var(--border); flex-shrink: 0;
        }
        .preview-info { flex: 1; min-width: 0; }
        .preview-name { font-weight: 500; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .preview-size { font-size: 0.73rem; color: var(--muted); margin-top: 0.2rem; }
        .preview-remove {
            background: none; border: 1px solid var(--border); color: var(--muted);
            width: 32px; height: 32px; border-radius: 8px; cursor: pointer; font-size: 1rem;
            display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0;
        }
        .preview-remove:hover { background: #ff4d6a22; border-color: var(--error); color: var(--error); }

        /* FORM */
        .form-bottom { padding: 1.2rem 1.5rem; display: flex; flex-direction: column; gap: 0.9rem; }
        .alias-label { font-size: 0.75rem; color: var(--muted); font-weight: 500; margin-bottom: 0.4rem; display: block; }
        .alias-row {
            display: flex; align-items: stretch;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 10px; overflow: hidden; transition: border-color 0.2s;
        }
        .alias-row:focus-within { border-color: var(--accent); }
        .alias-prefix {
            padding: 0.72rem 0.75rem; font-size: 0.73rem; color: var(--muted);
            white-space: nowrap; flex-shrink: 0; border-right: 1px solid var(--border);
            background: #0a0a0f66; display: flex; align-items: center;
            user-select: none; max-width: 180px; overflow: hidden; text-overflow: ellipsis;
        }
        .alias-input {
            flex: 1; background: transparent; border: none;
            padding: 0.72rem 0.9rem; color: var(--text);
            font-family: 'DM Sans', sans-serif; font-size: 0.875rem; outline: none; min-width: 0;
        }
        .alias-input::placeholder { color: var(--muted); }
        .alias-hint { font-size: 0.71rem; color: var(--muted); margin-top: 0.35rem; }

        .submit-btn {
            width: 100%; padding: 0.85rem;
            background: linear-gradient(135deg, var(--accent), #9b59ff);
            border: none; border-radius: 10px; color: white;
            font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 700;
            letter-spacing: 0.02em; cursor: pointer; transition: all 0.25s;
            position: relative; overflow: hidden;
        }
        .submit-btn::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 30%, #ffffff18);
            opacity: 0; transition: opacity 0.2s;
        }
        .submit-btn:hover::before { opacity: 1; }
        .submit-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 24px #7c6aff40; }
        .submit-btn:active { transform: translateY(0); }
        .submit-btn:disabled { opacity: 0.45; cursor: not-allowed; transform: none; box-shadow: none; }

        /* ERROR */
        .error-msg {
            display: none; margin: 0 1.5rem 0.8rem;
            background: #ff4d6a11; border: 1px solid #ff4d6a33;
            border-radius: 10px; padding: 0.8rem 1rem; font-size: 0.82rem; color: var(--error); line-height: 1.5;
        }
        .error-msg.visible { display: block; }

        /* RESULT */
        .result-card {
            display: none; margin: 0 1.5rem 1.5rem;
            background: #4ade8011; border: 1px solid #4ade8033;
            border-radius: 12px; padding: 1.2rem;
        }
        .result-card.visible { display: block; animation: slideIn 0.35s ease; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .result-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--success); font-weight: 600; margin-bottom: 0.7rem; }
        .result-url-row {
            display: flex; align-items: center; gap: 0.6rem;
            background: #0a0a0f99; border: 1px solid var(--border);
            border-radius: 8px; padding: 0.65rem 0.9rem; margin-bottom: 0.6rem;
        }
        .result-url { flex: 1; font-size: 0.85rem; color: var(--accent3); word-break: break-all; text-decoration: none; min-width: 0; }
        .result-url:hover { text-decoration: underline; }
        .copy-btn {
            background: var(--surface2); border: 1px solid var(--border); color: var(--text);
            padding: 0.4rem 0.9rem; border-radius: 6px; font-size: 0.75rem; cursor: pointer;
            font-family: 'DM Sans', sans-serif; font-weight: 500; white-space: nowrap; transition: all 0.2s; flex-shrink: 0;
        }
        .copy-btn:hover { background: var(--accent); border-color: var(--accent); }
        .copy-btn.copied { background: var(--success); border-color: var(--success); color: #000; }
        .result-open-btn {
            display: block; padding: 0.5rem; background: transparent;
            border: 1px solid #4ade8033; border-radius: 8px; color: var(--success);
            font-size: 0.78rem; font-family: 'DM Sans', sans-serif; cursor: pointer;
            text-align: center; text-decoration: none; transition: background 0.2s; margin-bottom: 0.6rem;
        }
        .result-open-btn:hover { background: #4ade8015; }
        .result-meta { font-size: 0.71rem; color: var(--muted); }

        /* SPINNER */
        .spinner {
            display: inline-block; width: 14px; height: 14px;
            border: 2px solid #ffffff44; border-top-color: white;
            border-radius: 50%; animation: spin 0.7s linear infinite;
            margin-right: 0.4rem; vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* RECENT */
        .section { max-width: 660px; margin: 0 auto 5rem; padding: 0 1.5rem; }
        .section-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1rem; }
        .section-title { font-family: 'Syne', sans-serif; font-size: 0.85rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; }
        .see-all { font-size: 0.8rem; color: var(--accent); text-decoration: none; transition: opacity 0.2s; }
        .see-all:hover { opacity: 0.7; }
        .recent-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 0.75rem; }
        .recent-item {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 12px; overflow: hidden; transition: all 0.25s;
            text-decoration: none; color: inherit; display: block;
        }
        .recent-item:hover { border-color: var(--border-hover); transform: translateY(-2px); }
        .recent-thumb { width: 100%; aspect-ratio: 16/9; object-fit: cover; display: block; background: var(--surface2); }
        .recent-meta { padding: 0.65rem; }
        .recent-name { font-size: 0.75rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.2rem; }
        .recent-code { font-size: 0.68rem; color: var(--accent); font-family: monospace; }
        .recent-visits { font-size: 0.65rem; color: var(--muted); float: right; }

        footer { text-align: center; padding: 2rem; border-top: 1px solid var(--border); color: var(--muted); font-size: 0.78rem; }

        @media (max-width: 520px) {
            nav { padding: 1rem 1.2rem; }
            .recent-grid { grid-template-columns: repeat(2, 1fr); }
            h1 { letter-spacing: -0.5px; }
            .alias-prefix { max-width: 120px; font-size: 0.65rem; }
            .nav-name { display: none; }
        }
    </style>
</head>
<body>

<nav>
    <a href="/" class="logo">ImgDrop ⚡</a>
    <div class="nav-right">
        <a href="/links" class="nav-link">Semua Link</a>

        
        <div class="nav-user">
            <div class="nav-avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
            <span class="nav-name"><?php echo e(auth()->user()->name); ?></span>
        </div>

        
        <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
            <?php echo csrf_field(); ?>
            <button type="submit" class="logout-btn">Keluar</button>
        </form>
    </div>
</nav>

<main>
    <section class="hero">
        <div class="hero-tag">Upload · Convert · Bagikan</div>
        <h1>Gambar Kamu<br><span>Jadi Link Instan</span></h1>
        <p class="hero-desc">Upload gambar, dapatkan short link yang bisa langsung dibagikan. Simpel, cepat, tanpa ribet.</p>
    </section>

    <div class="card-wrap">
        <div class="upload-card">
            <div class="drop-zone" id="dropZone">
                <input type="file" id="fileInput" accept="image/jpeg,image/png,image/gif,image/webp">
                <div class="drop-icon">🖼️</div>
                <div class="drop-title">Klik atau drag gambar ke sini</div>
                <div class="drop-subtitle">JPG, PNG, GIF, WebP — Maks. 10 MB</div>
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
                        <span class="alias-prefix"><?php echo e(url('/i/')); ?>/</span>
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

    <?php if($recentLinks->count()): ?>
    <section class="section">
        <div class="section-header">
            <div class="section-title">Upload Terakhirmu</div>
            <a href="/links" class="see-all">Lihat semua</a>
        </div>
        <div class="recent-grid">
            <?php $__currentLoopData = $recentLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($link->getShortUrl()); ?>" target="_blank" class="recent-item">
                <img class="recent-thumb" src="<?php echo e($link->getImageUrl()); ?>"
                     alt="<?php echo e($link->original_filename); ?>" loading="lazy"
                     onerror="this.style.background='#18181f'">
                <div class="recent-meta">
                    <span class="recent-visits">👁 <?php echo e($link->visit_count); ?></span>
                    <div class="recent-name"><?php echo e($link->original_filename); ?></div>
                    <div class="recent-code">/i/<?php echo e($link->custom_alias ?? $link->short_code); ?></div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>
</main>

<footer>
    ImgDrop &copy; <?php echo e(date('Y')); ?> — Gambar jadi link, sesimpel itu.
</footer>

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

fileInput.addEventListener('change', () => { if (fileInput.files[0]) showPreview(fileInput.files[0]); });

removeBtn.addEventListener('click', () => {
    clearFile();
    resultCard.classList.remove('visible');
    errorMsg.classList.remove('visible');
});

dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        showPreview(file);
    } else {
        errorMsg.textContent = 'File harus berupa gambar!';
        errorMsg.classList.add('visible');
    }
});

submitBtn.addEventListener('click', async () => {
    if (!selectedFile) return;
    errorMsg.classList.remove('visible');
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
        const res  = await fetch('/upload', { method: 'POST', body: fd });
        const data = await res.json();

        clearInterval(iv);
        progressFill.style.width = '100%';
        setTimeout(() => { progressBar.classList.remove('visible'); progressFill.style.width = '0%'; }, 600);

        if (res.ok && data.success) {
            resultUrl.setAttribute('href', data.short_url);
            resultUrl.textContent = data.short_url;
            resultOpenBtn.setAttribute('href', data.short_url);
            resultMeta.textContent = '📁 ' + data.filename + ' · ' + data.size;
            resultCard.classList.add('visible');
            resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            clearFile();
            aliasInput.value = '';
        } else {
            const msgs = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : (data.message || 'Upload gagal, coba lagi.');
            errorMsg.textContent = msgs;
            errorMsg.classList.add('visible');
        }
    } catch (e) {
        clearInterval(iv);
        progressBar.classList.remove('visible');
        errorMsg.textContent = 'Koneksi bermasalah. Coba lagi ya!';
        errorMsg.classList.add('visible');
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
</script>
</body>
</html><?php /**PATH D:\ImageConverter\imageconvert\resources\views/index.blade.php ENDPATH**/ ?>