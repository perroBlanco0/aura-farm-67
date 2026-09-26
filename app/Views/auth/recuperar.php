<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar clave · Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/michi.css">
    <style>
        body { display: grid; place-items: center; padding: 24px 16px; }
        .form-card { width: min(430px, 100%); padding: clamp(26px, 6vw, 40px); }
        .form-card .brand { color: var(--ink); text-shadow: none; margin-bottom: 26px; }
        .form-card .brand-mark { box-shadow: 0 6px 16px rgba(147, 212, 0, .4); }
        h1 { margin: 0 0 6px; font-size: clamp(1.6rem, 6vw, 2.1rem); font-weight: 600; letter-spacing: -.01em; }
        h1 span { color: var(--sky-1); }
        .sub { color: var(--muted); font-size: .88rem; font-weight: 700; margin-bottom: 22px; line-height: 1.55; }
        button { margin-top: 24px; width: 100%; }
        .links { margin-top: 20px; font-size: .85rem; }
        a { color: var(--sky-1); text-decoration: none; font-weight: 900; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<main class="card form-card">
    <div class="brand"><span class="brand-mark">M</span><span>MICHI ARENA</span></div>
    <h1>RECUPERA TU <span>CLAVE</span></h1>
    <p class="sub">Te enviamos un código de 6 dígitos a tu correo registrado. Vale por 15 minutos.</p>

    <?php if (! empty($error)): ?><p class="error"><?= esc($error) ?></p><?php endif ?>
    <?php if (! empty($ok)): ?><p class="ok"><?= esc($ok) ?></p><?php endif ?>

    <form action="<?= site_url('recuperar') ?>" method="post">
        <?= csrf_field() ?>
        <label for="email">CORREO DE TU CUENTA</label>
        <input id="email" name="email" type="email" required autocomplete="email" autofocus>
        <button type="submit">ENVIAR CÓDIGO</button>
    </form>

    <div class="links"><a href="<?= site_url('login') ?>">← Volver a entrar</a></div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/js/michi.js"></script>
<script>
<?php if (! empty($error)): ?>
MichiToast.fire({ icon: 'error', title: <?= json_encode((string) $error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> });
<?php endif ?>
<?php if (! empty($ok)): ?>
MichiToast.fire({ icon: 'success', title: <?= json_encode((string) $ok, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> });
<?php endif ?>
</script>
</body>
</html>
