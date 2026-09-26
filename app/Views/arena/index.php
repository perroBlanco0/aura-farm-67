<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/michi.css">
    <style>
        .shell { width: min(1120px, calc(100% - 32px)); margin: 0 auto; padding: 30px 0 60px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 34px; }

        .hero { display: grid; grid-template-columns: 1.05fr .95fr; gap: 26px; align-items: stretch; }
        .copy, .challenge { border-radius: 28px; box-shadow: var(--shadow-lg); }
        .copy {
            padding: clamp(30px, 5vw, 56px); position: relative; overflow: hidden;
            background: linear-gradient(160deg, rgba(255,255,255,.16), rgba(255,255,255,.05));
            border: 2px solid rgba(255,255,255,.35);
            color: #fff; backdrop-filter: blur(8px);
        }
        .copy::after {
            content: "🐈"; position: absolute; right: -10px; bottom: -42px;
            font-size: 170px; line-height: 1; opacity: .16; transform: rotate(-8deg);
        }
        .eyebrow {
            display: inline-block; padding: 7px 14px; border-radius: 999px;
            background: rgba(255,255,255,.85); color: var(--sky-1);
            font-size: .72rem; font-weight: 900; letter-spacing: .14em; text-transform: uppercase;
        }
        h1 {
            margin: 18px 0 16px; max-width: 640px;
            font-size: clamp(2.4rem, 6vw, 4.6rem); line-height: .95; font-weight: 700; letter-spacing: -.02em;
            text-shadow: 0 3px 0 rgba(30, 60, 120, .3);
        }
        h1 span { color: var(--lime); }
        .lead { max-width: 560px; color: #eaf6ff; font-size: clamp(1rem, 2vw, 1.12rem); font-weight: 700; line-height: 1.65; }
        .cta-mapa {
            display: inline-flex; margin-top: 30px; padding: 17px 34px; position: relative; z-index: 1;
            font-size: 1.1rem;
        }

        .challenge { background: var(--card); padding: 28px; display: flex; flex-direction: column; }
        .profile {
            display: grid; grid-template-columns: 76px 1fr; gap: 16px; align-items: center;
            padding-bottom: 20px; border-bottom: 2px dashed var(--line);
        }
        .avatar {
            width: 76px; height: 76px; border-radius: 50%; object-fit: cover;
            border: 4px solid var(--sky-2); box-shadow: 0 8px 20px rgba(65, 180, 214, .35);
        }
        .profile small { color: var(--muted); text-transform: uppercase; letter-spacing: .12em; font-weight: 900; }
        .profile h2 { margin: 5px 0 3px; font-size: 1.35rem; font-weight: 600; }
        .profile .aura-line { color: var(--muted); font-weight: 800; font-size: .85rem; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 20px 0 24px; }
        .stat {
            padding: 13px 8px; border-radius: 16px; text-align: center;
            background: #f2f7ff; border: 2px solid var(--line);
        }
        .stat strong { display: block; font-size: 1.15rem; color: var(--sky-1); font-family: Fredoka, sans-serif; }
        .stat span { color: var(--muted); font-size: .68rem; font-weight: 900; letter-spacing: .06em; }
        .hint { min-height: 38px; color: var(--muted); font-size: .8rem; font-weight: 700; line-height: 1.5; margin: 10px 0 18px; }
        .challenge form button { width: 100%; }

        @media (max-width: 820px) {
            .shell { padding-top: 20px; }
            .topbar { margin-bottom: 22px; }
            .hero { grid-template-columns: 1fr; }
            .copy { padding: 30px 24px 44px; }
        }
        @media (max-width: 480px) {
            .shell { width: calc(100% - 20px); }
            .brand span:last-child { display: none; }
            .balance { font-size: .78rem; }
            .challenge { padding: 20px; }
        }
    </style>
</head>
<body>
<main class="shell">
    <header class="topbar">
        <div class="brand">
            <span class="brand-mark">M</span>
            <span>MICHI ARENA</span>
        </div>
        <div class="topnav">
            <a href="<?= site_url('arena') ?>" class="activo">Arena</a>
            <a href="<?= site_url('mapa') ?>">Mapa</a>
            <?php if (! empty($es_admin)): ?>
                <a href="<?= site_url('mantenedor') ?>">Mantenedor</a>
            <?php endif ?>
            <a href="<?= site_url('salir') ?>">Salir</a>
            <div class="balance"><?= esc(str_replace('$AURA', '$MICHI', formatear_auracoins((int) $jugador['auracoins']))) ?></div>
        </div>
    </header>

    <section class="hero">
        <article class="copy">
            <div class="eyebrow">La arena de los gatos virales</div>
            <h1>APUESTA.<br>TIRA MICHI.<br><span>FARMEA $MICHI.</span></h1>
            <p class="lead">
                Entra a la arena donde Oiia Oiia Cat, Big Floppa y el Michi Llorón
                convierten el cringe en daño crítico. Gana el pozo o anda a llorar al TikTok.
            </p>
            <a class="btn cta-mapa" href="<?= site_url('mapa') ?>">🧭 BUSCAR MICHIS</a>
        </article>

        <aside class="challenge">
            <div class="profile">
                <img class="avatar" src="/img/cartas/michi.jpg" alt="">
                <div>
                    <small>Entrenador michi</small>
                    <h2><?= esc($jugador['username']) ?></h2>
                    <span class="aura-line"><?= esc($jugador['aura_actual']) ?> / <?= esc($jugador['aura_max']) ?> Aura permanente</span>
                </div>
            </div>

            <div class="stats">
                <div class="stat"><strong><?= esc($jugador['victorias']) ?></strong><span>VICTORIAS</span></div>
                <div class="stat"><strong><?= esc($jugador['derrotas']) ?></strong><span>DERROTAS</span></div>
                <div class="stat"><strong><?= esc(calcular_barra_aura((int) $jugador['aura_actual'], (int) $jugador['aura_max'])) ?>%</strong><span>AURA PERMANENTE</span></div>
            </div>

            <?php if ($error !== null): ?>
                <p class="error"><?= esc($error) ?></p>
            <?php endif ?>

            <form action="<?= site_url('arena/iniciar') ?>" method="post">
                <?= csrf_field() ?>
                <label for="apuesta">¿Cuánto $MICHI pones en juego?</label>
                <select id="apuesta" name="apuesta" required>
                    <?php foreach ([100, 250, 500, 1000, 2000] as $apuesta): ?>
                        <option value="<?= $apuesta ?>" <?= $apuesta > (int) $jugador['auracoins'] ? 'disabled' : '' ?>>
                            <?= esc(str_replace('$AURA', '$MICHI', formatear_auracoins($apuesta))) ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <p class="hint">Apuestas $MICHI de tu saldo. Ganas → cobras el doble. Pierdes → la apuesta se pierde y tu Aura permanente −100.</p>
                <button type="submit">⚔️ DESAFIAR AL MICHI BOT</button>
            </form>
        </aside>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/js/michi.js"></script>
<script>
<?php if ($error !== null): ?>
MichiToast.fire({ icon: 'error', title: <?= json_encode((string) $error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> });
<?php endif ?>
</script>
</body>
</html>
