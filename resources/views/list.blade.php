<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Link Saya — ImgDrop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0a0a0f; --surface: #111118; --surface2: #18181f;
            --border: #ffffff0f; --accent: #7c6aff; --accent2: #ff6a9b;
            --text: #f0f0f8; --muted: #6b6b80; --error: #ff4d6a;
        }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        body::before {
            content: ''; position: fixed; inset: 0;
            background-image: radial-gradient(ellipse 80% 50% at 20% 10%, #7c6aff12 0%, transparent 60%);
            pointer-events: none; z-index: 0;
        }
        nav {
            position: sticky; top: 0; z-index: 100; padding: 1rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--border); backdrop-filter: blur(20px); background: #0a0a0fcc;
        }
        .logo {
            font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            text-decoration: none;
        }
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .nav-link { color: var(--muted); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
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
            font-size: 0.75rem; font-family: 'DM Sans', sans-serif; padding: 0; transition: color 0.2s;
        }
        .logout-btn:hover { color: var(--error); }

        main { position: relative; z-index: 1; padding: 3rem 1.5rem; max-width: 920px; margin: 0 auto; }

        .page-title { font-family: 'Syne', sans-serif; font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0.4rem; }
        .page-sub { color: var(--muted); font-size: 0.875rem; margin-bottom: 2rem; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem; }

        .link-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden; transition: all 0.25s;
        }
        .link-card:hover { border-color: #ffffff1a; transform: translateY(-2px); }

        .card-thumb { width: 100%; aspect-ratio: 16/9; object-fit: cover; background: var(--surface2); display: block; }

        .card-body { padding: 1rem; }

        .card-filename { font-size: 0.82rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.6rem; }

        .card-url {
            display: flex; align-items: center; gap: 0.5rem;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 8px; padding: 0.5rem 0.7rem; margin-bottom: 0.7rem; min-width: 0;
        }
        .card-url a {
            flex: 1; font-size: 0.73rem; color: #a89bff; text-decoration: none;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0;
        }
        .mini-copy {
            background: none; border: 1px solid var(--border); color: var(--muted);
            cursor: pointer; font-size: 0.7rem; padding: 0.2rem 0.5rem;
            border-radius: 5px; transition: all 0.2s; white-space: nowrap;
            font-family: 'DM Sans', sans-serif; flex-shrink: 0;
        }
        .mini-copy:hover { color: var(--text); border-color: var(--accent); }
        .mini-copy.copied { color: #4ade80; border-color: #4ade8066; }

        .card-footer { display: flex; justify-content: space-between; align-items: center; font-size: 0.72rem; color: var(--muted); gap: 0.5rem; }
        .card-stats { flex: 1; min-width: 0; }
        .delete-btn {
            background: none; border: 1px solid transparent; color: var(--muted);
            padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.72rem;
            cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.2s; flex-shrink: 0;
        }
        .delete-btn:hover { background: #ff4d6a18; border-color: #ff4d6a44; color: var(--error); }

        .badge {
            display: inline-block; background: #7c6aff22; color: #a89bff;
            border: 1px solid #7c6aff33; padding: 0.1rem 0.4rem; border-radius: 4px; font-size: 0.65rem; font-weight: 600;
        }

        .empty { text-align: center; padding: 5rem 0; color: var(--muted); }
        .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty h3 { font-family: 'Syne', sans-serif; font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--text); }
        .back-link {
            display: inline-block; margin-top: 1.5rem;
            background: var(--accent); color: white; text-decoration: none;
            padding: 0.6rem 1.4rem; border-radius: 8px; font-size: 0.875rem; font-weight: 500; transition: opacity 0.2s;
        }
        .back-link:hover { opacity: 0.85; }

        .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2.5rem; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.82rem; text-decoration: none;
            border: 1px solid var(--border); color: var(--muted); transition: all 0.2s;
        }
        .pagination a:hover { border-color: var(--accent); color: var(--accent); }
        .pagination .active { background: var(--accent); border-color: var(--accent); color: white; }

        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr 1fr; }
            nav { padding: 1rem 1.2rem; }
            .nav-name { display: none; }
        }
        @media (max-width: 380px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<nav>
    <a href="/" class="logo">ImgDrop ⚡</a>
    <div class="nav-right">
        <a href="/" class="nav-link">+ Upload</a>
        <div class="nav-user">
            <div class="nav-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <span class="nav-name">{{ auth()->user()->name }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">Keluar</button>
        </form>
    </div>
</nav>

<main>
    <h1 class="page-title">Link Saya</h1>
    <p class="page-sub">Kamu punya {{ $links->total() }} gambar tersimpan</p>

    @if($links->count())
    <div class="grid" id="grid">
        @foreach($links as $link)
        <div class="link-card" id="card-{{ $link->id }}">
            <img class="card-thumb"
                 src="{{ $link->getImageUrl() }}"
                 alt="{{ $link->original_filename }}"
                 loading="lazy"
                 onerror="this.style.opacity='0.15'">
            <div class="card-body">
                <div class="card-filename" title="{{ $link->original_filename }}">{{ $link->original_filename }}</div>
                <div class="card-url">
                    <a href="{{ $link->getShortUrl() }}" target="_blank">{{ $link->getShortUrl() }}</a>
                    <button class="mini-copy" onclick="copyUrl(this, '{{ $link->getShortUrl() }}')">Salin</button>
                </div>
                <div class="card-footer">
                    <span class="card-stats">
                        {{ $link->getFileSizeFormatted() }} · 👁 {{ $link->visit_count }}
                        @if($link->custom_alias) · <span class="badge">custom</span> @endif
                    </span>
                    <button class="delete-btn" onclick="deleteLink({{ $link->id }}, this)">Hapus</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="pagination">
        {{ $links->links('pagination::simple-default') }}
    </div>
    @else
    <div class="empty">
        <div class="empty-icon">📭</div>
        <h3>Belum ada gambar</h3>
        <p>Upload gambar pertamamu sekarang!</p>
        <a href="/" class="back-link">Upload Sekarang</a>
    </div>
    @endif
</main>

<script>
function copyUrl(btn, url) {
    const done = () => {
        btn.textContent = '✓';
        btn.classList.add('copied');
        setTimeout(() => { btn.textContent = 'Salin'; btn.classList.remove('copied'); }, 2000);
    };
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(done);
    } else {
        const ta = document.createElement('textarea');
        ta.value = url; document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta); done();
    }
}

async function deleteLink(id, btn) {
    if (!confirm('Hapus gambar dan link ini?')) return;
    const orig = btn.textContent;
    btn.textContent = '...'; btn.disabled = true;
    const res = await fetch('/links/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    });
    if (res.ok) {
        const card = document.getElementById('card-' + id);
        card.style.transition = 'all 0.3s';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => card.remove(), 300);
    } else {
        btn.textContent = orig; btn.disabled = false;
        alert('Gagal menghapus.');
    }
}
</script>
</body>
</html>