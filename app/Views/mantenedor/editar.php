<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar <?= esc($jugador['username']) ?> · Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #08090d; --lime: #b8ff36; --cyan: #44eaff; --pink: #ff4fc8;
            --gold: #ffd452; --muted: #9ca4b7;
        }
        * { box-sizing: border-box; }
        html, body { width: 100%; max-width: 100%; overflow-x: hidden; }
        body {
            margin: 0; min-height: 100vh; color: #fff; font-family: Inter, sans-serif;
            padding: 20px; display: grid; place-items: center;
            background:
                radial-gradient(circle at 15% 15%, rgba(68,234,255,.12), transparent 30rem),
                radial-gradient(circle at 85% 10%, rgba(255,79,200,.13), transparent 26rem),
                var(--bg);
        }
        .card {
            width: min(560px, 100%); padding: clamp(26px, 5vw, 42px);
            border: 1px solid rgba(255,255,255,.1); border-radius: 26px;
            background: linear-gradient(150deg, rgba(25,29,40,.94), rgba(12,14,20,.97));
            box-shadow: 0 30px 90px rgba(0,0,0,.45);
        }
        .brand { display: flex; align-items: center; gap: 11px; font-weight: 800; letter-spacing: .08em; margin-bottom: 26px; }
        .brand-mark {
            width: 40px; height: 40px; display: grid; place-items: center; border-radius: 12px;
            background: var(--lime); color: #08090d; font-family: "Archivo Black", sans-serif;
            box-shadow: 0 0 26px rgba(184,255,54,.35);
        }
        h1 { margin: 0 0 4px; font: clamp(1.5rem, 5vw, 2.1rem)/1 "Archivo Black", sans-serif; letter-spacing: -.03em; }
        h1 span { color: var(--pink); }
        .sub { color: var(--muted); font-size: .82rem; margin-bottom: 22px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
        label { display: block; margin: 14px 0 7px; font-weight: 800; font-size: .78rem; letter-spacing: .05em; }
        input[type=text], input[type=email], input[type=number] {
            width: 100%; padding: 13px 15px; border-radius: 12px; font: 600 .95rem Inter, sans-serif;
            border: 1px solid rgba(255,255,255,.14); background: #0d1017; color: #fff;
        }
        input:focus { outline: 2px solid var(--cyan); border-color: transparent; }
        .check { display: flex; align-items: center; gap: 10px; margin-top: 22px; font-weight: 700; font-size: .85rem; }
        .check input { width: 18px; height: 18px; accent-color: var(--lime); }
        .check span { color: var(--gold); }
        .error { padding: 12px 14px; border-radius: 12px; font-size: .84rem; margin-bottom: 8px; background: rgba(255,79,103,.13); border: 1px solid rgba(255,79,103,.3); color: #ff9bac; }
        .actions { display: flex; gap: 12px; margin-top: 26px; }
        button, .volver {
            flex: 1; border: 0; border-radius: 13px; padding: 14px; cursor: pointer;
            font: 800 .95rem Inter, sans-serif; text-align: center; text-decoration: none;
        }
        button { color: #090b0e; background: linear-gradient(100deg, var(--lime), #eaff78); box-shadow: 0 10px 35px rgba(184,255,54,.18); }
        .volver { color: #fff; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12); }
        @media (max-width: 520px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<main class="card">
    <div class="brand"><span class="brand-mark">M</span><span>MICHI ARENA</span></div>
    <h1>EDITAR <span><?= esc($jugador['username']) ?></span></h1>
    <p class="sub">Los cambios de Aura y AuraCoins se guardan vía stored procedure transaccional.</p>

    <?php if (! empty($error)): ?><p class="error"><?= esc($error) ?></p><?php endif ?>

    <form action="<?= site_url('mantenedor/editar/' . $jugador['id']) ?>" method="post">
        <?= csrf_field() ?>
        <div class="grid">
            <div>
                <label for="username">USUARIO</label>
                <input id="username" name="username" type="text" required value="<?= esc($jugador['username']) ?>">
            </div>
            <div>
                <label for="email">CORREO</label>
                <input id="email" name="email" type="email" value="<?= esc($jugador['email'] ?? '') ?>">
            </div>
            <div>
                <label for="aura_actual">AURA ACTUAL</label>
                <input id="aura_actual" name="aura_actual" type="number" min="0" required value="<?= esc($jugador['aura_actual']) ?>">
            </div>
            <div>
                <label for="aura_max">AURA MÁX</label>
                <input id="aura_max" name="aura_max" type="number" min="1" required value="<?= esc($jugador['aura_max']) ?>">
            </div>
            <div>
                <label for="auracoins">AURACOINS</label>
                <input id="auracoins" name="auracoins" type="number" min="0" required value="<?= esc($jugador['auracoins']) ?>">
            </div>
        </div>

        <label class="check">
            <input type="checkbox" name="es_admin" <?= (int) $jugador['es_admin'] === 1 ? 'checked' : '' ?>>
            <span>Es admin de la arena</span>
        </label>

        <div class="actions">
            <button type="submit">GUARDAR MICHY</button>
            <a class="volver" href="<?= site_url('mantenedor') ?>">Volver</a>
        </div>
    </form>
</main>
</body>
</html>
