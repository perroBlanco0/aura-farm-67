<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva clave · Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #08090d; --lime: #b8ff36; --cyan: #44eaff; --muted: #9ca4b7;
        }
        * { box-sizing: border-box; }
        html, body { width: 100%; max-width: 100%; overflow-x: hidden; }
        body {
            margin: 0; min-height: 100vh; display: grid; place-items: center;
            color: #fff; font-family: Inter, sans-serif; padding: 20px;
            background:
                radial-gradient(circle at 20% 12%, rgba(68,234,255,.12), transparent 26rem),
                radial-gradient(circle at 82% 78%, rgba(255,79,200,.13), transparent 24rem),
                var(--bg);
        }
        .card {
            width: min(430px, 100%); padding: clamp(26px, 6vw, 44px);
            border: 1px solid rgba(255,255,255,.1); border-radius: 26px;
            background: linear-gradient(150deg, rgba(25,29,40,.94), rgba(12,14,20,.97));
            box-shadow: 0 30px 90px rgba(0,0,0,.45);
        }
        .brand { display: flex; align-items: center; gap: 11px; font-weight: 800; letter-spacing: .08em; margin-bottom: 30px; }
        .brand-mark {
            width: 40px; height: 40px; display: grid; place-items: center; border-radius: 12px;
            background: var(--cyan); color: #08090d; font-family: "Archivo Black", sans-serif;
            box-shadow: 0 0 26px rgba(68,234,255,.35);
        }
        h1 { margin: 0 0 6px; font: clamp(1.6rem, 6vw, 2.1rem)/1 "Archivo Black", sans-serif; letter-spacing: -.03em; }
        h1 span { color: var(--cyan); }
        .sub { color: var(--muted); font-size: .85rem; margin-bottom: 22px; line-height: 1.55; }
        label { display: block; margin: 16px 0 8px; font-weight: 800; font-size: .82rem; letter-spacing: .05em; }
        input {
            width: 100%; padding: 14px 16px; border-radius: 13px; font: 600 1rem Inter, sans-serif;
            border: 1px solid rgba(255,255,255,.14); background: #0d1017; color: #fff;
        }
        input:focus { outline: 2px solid var(--cyan); border-color: transparent; }
        button {
            margin-top: 24px; width: 100%; border: 0; border-radius: 14px; padding: 15px;
            cursor: pointer; color: #090b0e; font: 800 1rem Inter, sans-serif;
            background: linear-gradient(100deg, var(--lime), #eaff78);
            box-shadow: 0 10px 35px rgba(184,255,54,.18); transition: transform .18s;
        }
        button:hover { transform: translateY(-2px); }
        .error, .ok { padding: 12px 14px; border-radius: 12px; font-size: .84rem; margin-bottom: 6px; }
        .error { background: rgba(255,79,103,.13); border: 1px solid rgba(255,79,103,.3); color: #ff9bac; }
        .ok { background: rgba(184,255,54,.1); border: 1px solid rgba(184,255,54,.3); color: var(--lime); }
        .links { margin-top: 22px; font-size: .8rem; }
        a { color: var(--cyan); text-decoration: none; font-weight: 700; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<main class="card">
    <div class="brand"><span class="brand-mark">M</span><span>MICHI ARENA</span></div>
    <h1>CÓDIGO + <span>NUEVA CLAVE</span></h1>
    <p class="sub">Pega el código que te llegó por correo y elige tu nueva clave.</p>

    <?php if (! empty($error)): ?><p class="error"><?= esc($error) ?></p><?php endif ?>
    <?php if (! empty($ok)): ?><p class="ok"><?= esc($ok) ?></p><?php endif ?>

    <form action="<?= site_url('verificar') ?>" method="post">
        <?= csrf_field() ?>
        <label for="email">CORREO</label>
        <input id="email" name="email" type="email" required value="<?= esc($email ?? '') ?>" autocomplete="email">
        <label for="codigo">CÓDIGO DE 6 DÍGITOS</label>
        <input id="codigo" name="codigo" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code">
        <label for="clave">NUEVA CLAVE</label>
        <input id="clave" name="clave" type="password" minlength="6" required autocomplete="new-password">
        <button type="submit">CAMBIAR CLAVE</button>
    </form>

    <div class="links"><a href="<?= site_url('login') ?>">← Volver a entrar</a></div>
</main>
</body>
</html>
