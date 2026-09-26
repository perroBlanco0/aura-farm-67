<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aura Arena 67</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #08090d;
            --panel: #12151d;
            --panel-soft: #191d28;
            --lime: #b8ff36;
            --cyan: #44eaff;
            --pink: #ff4fc8;
            --gold: #ffd452;
            --muted: #9ca4b7;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            color: #fff;
            font-family: Inter, sans-serif;
            background:
                radial-gradient(circle at 15% 15%, rgba(68, 234, 255, .12), transparent 30rem),
                radial-gradient(circle at 85% 10%, rgba(255, 79, 200, .13), transparent 26rem),
                var(--bg);
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: .18;
            background-image: linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: linear-gradient(to bottom, #000, transparent 80%);
        }
        .shell { width: min(1120px, calc(100% - 32px)); margin: 0 auto; padding: 36px 0 60px; position: relative; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 52px; }
        .brand { display: flex; align-items: center; gap: 12px; font-weight: 800; letter-spacing: .08em; }
        .brand-mark {
            width: 42px; height: 42px; display: grid; place-items: center; border-radius: 12px;
            color: #08090d; background: var(--lime); box-shadow: 0 0 28px rgba(184,255,54,.35);
            font-family: "Archivo Black", sans-serif;
        }
        .balance {
            border: 1px solid rgba(255,255,255,.12); background: rgba(18,21,29,.75);
            border-radius: 999px; padding: 11px 16px; color: var(--gold); font-weight: 800;
        }
        .hero { display: grid; grid-template-columns: 1.1fr .9fr; gap: 28px; align-items: stretch; }
        .copy, .challenge {
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 28px;
            background: linear-gradient(145deg, rgba(25,29,40,.92), rgba(12,14,20,.96));
            box-shadow: 0 28px 80px rgba(0,0,0,.36);
        }
        .copy { padding: clamp(28px, 5vw, 58px); overflow: hidden; position: relative; }
        .copy::after {
            content: "67"; position: absolute; right: -18px; bottom: -56px;
            font: 200px/1 "Archivo Black", sans-serif; color: rgba(184,255,54,.055); transform: rotate(-8deg);
        }
        .eyebrow { color: var(--cyan); font-size: .78rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
        h1 { margin: 14px 0 18px; max-width: 680px; font: clamp(2.55rem, 6vw, 5.35rem)/.92 "Archivo Black", sans-serif; letter-spacing: -.045em; }
        h1 span { color: var(--lime); text-shadow: 0 0 34px rgba(184,255,54,.2); }
        .lead { max-width: 570px; color: #c4cada; font-size: clamp(1rem, 2vw, 1.16rem); line-height: 1.7; }
        .tags { display: flex; flex-wrap: wrap; gap: 9px; margin-top: 28px; }
        .tag { padding: 8px 12px; border-radius: 9px; background: rgba(255,255,255,.055); color: #d7dbea; font-size: .78rem; font-weight: 700; }
        .challenge { padding: 28px; display: flex; flex-direction: column; }
        .profile { display: grid; grid-template-columns: 72px 1fr; gap: 16px; align-items: center; padding-bottom: 22px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .avatar { width: 72px; height: 72px; border-radius: 20px; background: #282e40; border: 2px solid var(--pink); }
        .profile small { color: var(--muted); text-transform: uppercase; letter-spacing: .12em; font-weight: 700; }
        .profile h2 { margin: 6px 0 3px; font-size: 1.3rem; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 22px 0 28px; }
        .stat { padding: 14px 8px; border-radius: 14px; background: rgba(255,255,255,.045); text-align: center; }
        .stat strong { display: block; font-size: 1.12rem; color: var(--lime); }
        .stat span { color: var(--muted); font-size: .72rem; }
        label { display: block; margin-bottom: 10px; font-weight: 800; }
        select {
            width: 100%; padding: 15px 16px; border: 1px solid rgba(255,255,255,.14);
            border-radius: 14px; color: #fff; background: #0d1017; font: 700 1rem Inter, sans-serif;
        }
        .hint { min-height: 38px; color: var(--muted); font-size: .78rem; line-height: 1.5; margin: 10px 0 18px; }
        button {
            width: 100%; border: 0; border-radius: 15px; padding: 16px 18px; cursor: pointer;
            color: #090b0e; background: linear-gradient(100deg, var(--lime), #eaff78);
            font: 800 1rem Inter, sans-serif; box-shadow: 0 10px 35px rgba(184,255,54,.18);
            transition: transform .18s, box-shadow .18s;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 14px 42px rgba(184,255,54,.27); }
        .error { margin: 0 0 18px; padding: 12px 14px; border-radius: 12px; background: rgba(255,79,103,.13); border: 1px solid rgba(255,79,103,.3); color: #ff9bac; font-size: .85rem; }
        .rules { margin-top: 26px; color: var(--muted); font-size: .75rem; text-align: center; line-height: 1.55; }
        @media (max-width: 820px) {
            .shell { padding-top: 22px; }
            .topbar { margin-bottom: 26px; }
            .hero { grid-template-columns: 1fr; }
            .copy { padding: 34px 26px 46px; }
        }
        @media (max-width: 480px) {
            .shell { width: min(100% - 20px, 1120px); }
            .brand span:last-child { display: none; }
            .balance { font-size: .78rem; }
            .copy, .challenge { border-radius: 22px; }
            .challenge { padding: 20px; }
        }
    </style>
</head>
<body>
<main class="shell">
    <header class="topbar">
        <div class="brand">
            <span class="brand-mark">67</span>
            <span>AURA ARENA</span>
        </div>
        <div class="balance"><?= esc(formatear_auracoins((int) $jugador['auracoins'])) ?></div>
    </header>

    <section class="hero">
        <article class="copy">
            <div class="eyebrow">Meme card battleground</div>
            <h1>APUESTA.<br>TIRA CARTA.<br><span>FARMEA AURA.</span></h1>
            <p class="lead">
                Entra a una arena donde GigaChad, Capybara Chill y el Rizzler convierten
                el cringe en daño crítico. Gana el pozo o pierde aura permanente.
            </p>
            <div class="tags">
                <span class="tag">AURA INFINITO ×1.5</span>
                <span class="tag">RAREZA 67</span>
                <span class="tag">ESCUDO CHILL</span>
                <span class="tag">ROBO DE AURA</span>
            </div>
        </article>

        <aside class="challenge">
            <div class="profile">
                <img class="avatar" src="https://api.dicebear.com/9.x/adventurer/svg?seed=<?= rawurlencode($jugador['username']) ?>&backgroundColor=b8ff36" alt="">
                <div>
                    <small>Retador conectado</small>
                    <h2><?= esc($jugador['username']) ?></h2>
                    <span><?= esc($jugador['aura_actual']) ?> / <?= esc($jugador['aura_max']) ?> Aura</span>
                </div>
            </div>

            <div class="stats">
                <div class="stat"><strong><?= esc($jugador['victorias']) ?></strong><span>VICTORIAS</span></div>
                <div class="stat"><strong><?= esc($jugador['derrotas']) ?></strong><span>DERROTAS</span></div>
                <div class="stat"><strong><?= esc(calcular_barra_aura((int) $jugador['aura_actual'], (int) $jugador['aura_max'])) ?>%</strong><span>AURA</span></div>
            </div>

            <?php if ($error !== null): ?>
                <p class="error"><?= esc($error) ?></p>
            <?php endif ?>

            <form action="<?= site_url('arena/iniciar') ?>" method="post">
                <?= csrf_field() ?>
                <label for="apuesta">¿Cuánta Aura pones en juego?</label>
                <select id="apuesta" name="apuesta" required>
                    <?php foreach ([100, 250, 500, 1000, 2000] as $apuesta): ?>
                        <option value="<?= $apuesta ?>" <?= $apuesta > (int) $jugador['auracoins'] ? 'disabled' : '' ?>>
                            <?= esc(formatear_auracoins($apuesta)) ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <p class="hint">La apuesta se retiene al iniciar. Si ganas, cobras el pozo doble; si pierdes, recibes −100 de Aura permanente.</p>
                <button type="submit">DESAFIAR A THE RIZZLER BOT</button>
            </form>
            <p class="rules">Economía y combate procesados de forma transaccional en MySQL.</p>
        </aside>
    </section>
</main>
</body>
</html>
