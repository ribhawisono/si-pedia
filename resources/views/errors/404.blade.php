<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — SI-Pedia</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,600,700,800,900&display=swap" rel="stylesheet">
    <script>if(localStorage.getItem('si-pedia-theme')==='dark'){document.documentElement.classList.add('dark')}</script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Poppins',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f8fafc;color:#0a0b2f;padding:2rem}
        html.dark body{background:#0f1117;color:#e2e8f0}
        .card{background:#fff;border-radius:1.5rem;padding:3rem 2.5rem;max-width:480px;width:100%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.08)}
        html.dark .card{background:#1a1d27;box-shadow:0 4px 24px rgba(0,0,0,0.4)}
        .code{font-size:6rem;font-weight:900;line-height:1;background:linear-gradient(135deg,#336cbc,#5b91d6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        h1{font-size:1.25rem;font-weight:700;margin:0.75rem 0 0.5rem}
        p{color:#64748b;font-size:0.875rem;line-height:1.6;margin-bottom:2rem}
        html.dark p{color:#94a3b8}
        .links{display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap}
        a{display:inline-flex;align-items:center;gap:0.5rem;padding:0.625rem 1.5rem;border-radius:0.75rem;font-weight:600;font-size:0.875rem;text-decoration:none;transition:all 0.2s}
        .btn-primary{background:#336cbc;color:#fff}
        .btn-primary:hover{background:#2a589a;transform:translateY(-1px)}
        .btn-secondary{border:1.5px solid #e2e8f0;color:#475569}
        html.dark .btn-secondary{border-color:#374151;color:#94a3b8}
        .btn-secondary:hover{background:#f8fafc}
        html.dark .btn-secondary:hover{background:#252836}
        .icon{font-size:3rem;margin-bottom:1rem}
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🔍</div>
        <div class="code">404</div>
        <h1>Halaman Tidak Ditemukan</h1>
        <p>Halaman yang kamu cari tidak ada, sudah dipindahkan, atau mungkin URL-nya salah.</p>
        <div class="links">
            <a href="/" class="btn-primary">← Beranda</a>
            <a href="/catalog" class="btn-secondary">📄 Katalog</a>
            <a href="/search" class="btn-secondary">🔍 Cari</a>
        </div>
    </div>
</body>
</html>
