<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta · Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/michi.css">
    <style>
        body { display: grid; place-items: center; padding: 24px 16px; }
        .splash { width: min(430px, 100%); text-align: center; }
        .mascot {
            width: 118px; height: 118px; margin: 0 auto 14px;
            border-radius: 50%; object-fit: cover;
            border: 5px solid #fff;
            box-shadow: var(--shadow-lg), 0 0 0 10px rgba(255, 255, 255, .22);
            animation: bob 3.2s ease-in-out infinite;
        }
        @keyframes bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .logo {
            margin: 0; color: #fff; letter-spacing: .02em;
            font-size: clamp(2.4rem, 9vw, 3.4rem); line-height: .95; font-weight: 700;
            text-shadow: 0 3px 0 rgba(30, 60, 120, .35), 0 10px 30px rgba(20, 50, 100, .45);
        }
        .logo span { color: var(--lime); }
        .tagline { margin: 10px 0 24px; color: #eaf6ff; font-weight: 800; font-size: .95rem; text-shadow: 0 2px 8px rgba(20, 50, 100, .4); }
        .form-card { padding: clamp(24px, 6vw, 36px); text-align: left; }
        .form-card h2 { margin: 0 0 4px; font-size: 1.4rem; font-weight: 600; color: var(--ink); }
        .form-card .sub { color: var(--muted); font-size: .85rem; font-weight: 700; margin-bottom: 6px; }
        .form-card button { margin-top: 24px; width: 100%; }
        .links { margin-top: 20px; display: flex; justify-content: center; gap: 18px; font-size: .85rem; }
        .links a { color: var(--sky-1); text-decoration: none; font-weight: 900; }
        .links a:hover { text-decoration: underline; }
        .hint { margin-top: 16px; color: var(--muted); font-size: .75rem; text-align: center; font-weight: 700; }
    </style>
</head>
<body>
<main class="splash">
    <img class="mascot" src="/img/cartas/michi.jpg" alt="Michi, la mascota de la arena">
    <h1 class="logo">MICHI <span>ARENA</span></h1>
    <p class="tagline">Crea tu cuenta y recibe tu correo de bienvenida.</p>

    <div class="card form-card">
        <h2>ÚNETE A LA ARENA</h2>
        <p class="sub">Empiezas con 1000 de Aura, 1500 $MICHI y 6 michis base — el resto se atrapa en el mapa.</p>

        <?php if (! empty($error)): ?><p class="error"><?= esc($error) ?></p><?php endif ?>

        <form action="<?= site_url('registro') ?>" method="post">
            <?= csrf_field() ?>
            <label for="username">NOMBRE DE MICHI</label>
            <input id="username" name="username" required minlength="3" maxlength="80" autocomplete="username" autofocus>
            <label for="email">CORREO</label>
            <input id="email" name="email" type="email" required autocomplete="email">
            <label for="clave">CLAVE</label>
            <input id="clave" name="clave" type="password" required minlength="6" autocomplete="new-password">
            <label for="clave2">REPITE LA CLAVE</label>
            <input id="clave2" name="clave2" type="password" required minlength="6" autocomplete="new-password">
            <button type="submit">CREAR MI MICHI</button>
        </form>

        <div class="links">
            <a href="<?= site_url('login') ?>">Ya tengo cuenta</a>
        </div>
        <p class="hint">Te mandaremos un correo de bienvenida con tu link de acceso.</p>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/js/michi.js"></script>
<script>
<?php if (! empty($error)): ?>
MichiToast.fire({ icon: 'error', title: <?= json_encode((string) $error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> });
<?php endif ?>
</script>
</body>
</html>
