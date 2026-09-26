<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar <?= esc($jugador['username']) ?> · Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/michi.css">
    <style>
        body { display: grid; place-items: center; padding: 24px 16px; }
        .form-card { width: min(560px, 100%); padding: clamp(26px, 5vw, 40px); }
        .form-card .brand { color: var(--ink); text-shadow: none; margin-bottom: 24px; }
        .form-card .brand-mark { box-shadow: 0 6px 16px rgba(147, 212, 0, .4); }
        h1 { margin: 0 0 4px; font-size: clamp(1.5rem, 5vw, 2rem); font-weight: 600; letter-spacing: -.01em; }
        h1 span { color: var(--sky-1); }
        .sub { color: var(--muted); font-size: .84rem; font-weight: 700; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
        .check { display: flex; align-items: center; gap: 10px; margin-top: 20px; font-weight: 800; font-size: .88rem; color: var(--ink-soft); }
        .check input { width: 20px; height: 20px; accent-color: var(--lime-deep); }
        .actions { display: flex; gap: 12px; margin-top: 26px; }
        .actions .btn, .actions button { flex: 1; }
        @media (max-width: 520px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<main class="card form-card">
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
            <a class="btn btn-secondary" href="<?= site_url('mantenedor') ?>">Volver</a>
        </div>
    </form>
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
