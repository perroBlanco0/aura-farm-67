<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Michi Arena · Mapa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        :root {
            --bg: #08090d;
            --panel: rgba(18, 21, 29, .82);
            --lime: #b8ff36;
            --cyan: #44eaff;
            --pink: #ff4fc8;
            --gold: #ffd452;
            --muted: #9ca4b7;
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            color: #fff;
            font-family: Inter, sans-serif;
            background: var(--bg);
            overflow: hidden;
        }
        #mapa {
            position: fixed;
            inset: 0;
            height: 100dvh;
            width: 100%;
            z-index: 1;
        }
        .leaflet-container { background: var(--bg); font-family: Inter, sans-serif; }

        .hud {
            position: fixed;
            top: calc(env(safe-area-inset-top, 0px) + 14px);
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: min(720px, calc(100% - 24px));
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background: var(--panel);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 18px 50px rgba(0,0,0,.45);
        }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 800; letter-spacing: .08em; font-size: .8rem; white-space: nowrap; }
        .brand-mark {
            width: 34px; height: 34px; display: grid; place-items: center; border-radius: 11px;
            color: #08090d; background: var(--lime); box-shadow: 0 0 22px rgba(184,255,54,.4);
            font-family: "Archivo Black", sans-serif; font-size: .95rem;
        }
        .hud-nav { display: flex; gap: 8px; align-items: center; }
        .hud-nav a {
            padding: 8px 13px; border-radius: 999px; font-size: .72rem; font-weight: 800;
            min-height: 44px; display: inline-flex; align-items: center;
            color: #fff; text-decoration: none; background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1); white-space: nowrap;
        }
        .hud-nav a:hover { border-color: var(--cyan); }
        .balance {
            border: 1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.06);
            border-radius: 999px; padding: 8px 13px; color: var(--gold); font-weight: 800;
            font-size: .72rem; white-space: nowrap;
        }

        .hud-error {
            position: fixed;
            top: calc(env(safe-area-inset-top, 0px) + 74px);
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: min(560px, calc(100% - 24px));
            padding: 12px 16px;
            border-radius: 16px;
            background: rgba(255,79,103,.16);
            border: 1px solid rgba(255,79,103,.4);
            color: #ff9bac;
            font-size: .82rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 18px 50px rgba(0,0,0,.4);
        }

        .btn-rescan {
            position: fixed;
            right: 18px;
            bottom: calc(env(safe-area-inset-bottom, 0px) + 26px);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 9px;
            border: 0;
            border-radius: 999px;
            padding: 15px 22px;
            cursor: pointer;
            color: #090b0e;
            background: linear-gradient(100deg, var(--lime), #eaff78);
            font: 800 .85rem Inter, sans-serif;
            letter-spacing: .06em;
            box-shadow: 0 14px 42px rgba(184,255,54,.3);
            transition: transform .18s, box-shadow .18s;
        }
        .btn-rescan:hover { transform: translateY(-2px) scale(1.03); box-shadow: 0 18px 52px rgba(184,255,54,.4); }
        .btn-rescan:active { transform: scale(.97); }
        .btn-rescan .radar { display: inline-block; }
        .btn-rescan.girando .radar { animation: girar .6s ease; }
        @keyframes girar { to { transform: rotate(360deg); } }

        .chip-ubicacion {
            position: fixed;
            left: 18px;
            bottom: calc(env(safe-area-inset-bottom, 0px) + 30px);
            z-index: 1000;
            padding: 10px 15px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background: var(--panel);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
            box-shadow: 0 14px 40px rgba(0,0,0,.4);
            max-width: calc(100% - 190px);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .marker-gato {
            width: 46px; height: 46px;
            border-radius: 50%;
            border: 3px solid var(--cyan);
            box-shadow: 0 0 0 4px rgba(68,234,255,.18), 0 10px 24px rgba(0,0,0,.5);
            background: #12151d;
            object-fit: cover;
        }
        .marker-jugador {
            width: 52px; height: 52px;
            border-radius: 50%;
            border: 3px solid var(--lime);
            box-shadow: 0 0 0 5px rgba(184,255,54,.22), 0 0 30px rgba(184,255,54,.35), 0 10px 24px rgba(0,0,0,.5);
            background: #12151d;
            object-fit: cover;
        }
        .jugador-wrap { position: relative; width: 52px; height: 52px; }
        .pulso {
            position: absolute;
            inset: -12px;
            border-radius: 50%;
            border: 2px solid rgba(184,255,54,.55);
            animation: pulso 2s ease-out infinite;
            pointer-events: none;
        }
        @keyframes pulso {
            0% { transform: scale(.6); opacity: .9; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .tooltip-michi {
            background: rgba(18,21,29,.92) !important;
            border: 1px solid rgba(255,255,255,.18) !important;
            border-radius: 999px !important;
            color: #fff !important;
            font: 800 .72rem Inter, sans-serif !important;
            padding: 5px 11px !important;
            box-shadow: 0 8px 24px rgba(0,0,0,.45) !important;
        }
        .tooltip-michi::before { display: none !important; }

        .leaflet-popup-content-wrapper {
            background: rgba(18,21,29,.94);
            color: #fff;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 18px;
            backdrop-filter: blur(12px);
        }
        .leaflet-popup-tip { background: rgba(18,21,29,.94); }
        .leaflet-popup-content { font: 700 .85rem Inter, sans-serif; margin: 12px 16px; }

        .leaflet-control-zoom {
            border: 1px solid rgba(255,255,255,.12) !important;
            border-radius: 999px !important;
            overflow: hidden;
            box-shadow: 0 14px 40px rgba(0,0,0,.4) !important;
        }
        .leaflet-control-zoom a {
            background: var(--panel) !important;
            color: #fff !important;
            border-bottom: 1px solid rgba(255,255,255,.1) !important;
            backdrop-filter: blur(14px);
        }
        .leaflet-control-attribution {
            background: rgba(8,9,13,.6) !important;
            color: #6b7386 !important;
            font-size: .6rem !important;
        }
        .leaflet-control-attribution a { color: #8b93a8 !important; }

        .michi-apareciendo { animation: aparecer .5s cubic-bezier(.2, 1.4, .4, 1) backwards; }
        @keyframes aparecer {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.25); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        .swal2-popup {
            font-family: Inter, sans-serif !important;
            background: linear-gradient(160deg, #191d28, #0d1017) !important;
            border: 1px solid rgba(255,255,255,.14) !important;
            border-radius: 26px !important;
            color: #fff !important;
        }
        .swal2-title { color: #fff !important; font-family: "Archivo Black", sans-serif !important; font-size: 1.35rem !important; letter-spacing: -.02em; }
        .swal2-html-container { color: #c4cada !important; }
        .swal2-image { border-radius: 22px !important; border: 3px solid var(--pink) !important; box-shadow: 0 0 34px rgba(255,79,200,.3) !important; object-fit: cover; }
        .swal2-confirm {
            background: linear-gradient(100deg, var(--lime), #eaff78) !important;
            color: #090b0e !important;
            border-radius: 14px !important;
            font-weight: 800 !important;
            box-shadow: 0 10px 32px rgba(184,255,54,.25) !important;
        }
        .swal2-cancel {
            background: rgba(255,255,255,.08) !important;
            color: #fff !important;
            border-radius: 14px !important;
            font-weight: 700 !important;
        }
        .swal2-deny {
            background: linear-gradient(100deg, var(--pink), #ff8ae0) !important;
            color: #fff !important;
            border-radius: 14px !important;
            font-weight: 800 !important;
            box-shadow: 0 10px 32px rgba(255,79,200,.28) !important;
        }
        .select-apuesta {
            width: 100%; padding: 13px 14px; margin-top: 14px; border: 1px solid rgba(255,255,255,.16);
            border-radius: 14px; color: #fff; background: #0d1017; font: 700 .95rem Inter, sans-serif;
        }
        .stat-michi { display: inline-block; margin: 2px 4px; padding: 6px 10px; border-radius: 9px; background: rgba(255,255,255,.06); font-size: .72rem; font-weight: 800; }
        .stat-michi.lvl { color: var(--cyan); }
        .stat-michi.atk { color: var(--pink); }
        .stat-michi.def { color: var(--lime); }

        .marker-jefe {
            width: 66px; height: 66px; border-radius: 50%;
            border: 3px solid #ff3355;
            box-shadow: 0 0 0 5px rgba(255,51,85,.25), 0 0 40px rgba(255,51,85,.55), 0 10px 26px rgba(0,0,0,.6);
            background: #1a0508; object-fit: cover;
            animation: latidoJefe 1.6s ease-in-out infinite;
        }
        @keyframes latidoJefe {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.09); }
        }
        .jefe-wrap { position: relative; width: 66px; height: 66px; }
        .aura-jefe {
            position: absolute; inset: -16px; border-radius: 50%;
            border: 2px solid rgba(255,51,85,.5);
            animation: pulso 1.8s ease-out infinite;
            pointer-events: none;
        }

        .cinematica {
            position: fixed; inset: 0; z-index: 4000;
            background: radial-gradient(ellipse at 50% 40%, #1c0a10 0%, #06030a 70%);
            display: none; place-items: center;
            flex-direction: column; text-align: center;
            padding: 24px;
        }
        .cinematica.activa { display: grid; animation: cineFade .45s ease; }
        @keyframes cineFade { from { opacity: 0; } to { opacity: 1; } }
        .cinematica.temblor { animation: cineFade .45s ease, temblor .45s linear 1.15s; }
        @keyframes temblor {
            0%, 100% { transform: translate(0,0); }
            15% { transform: translate(-10px,5px); }
            30% { transform: translate(9px,-6px); }
            45% { transform: translate(-8px,-4px); }
            60% { transform: translate(7px,6px); }
            75% { transform: translate(-6px,3px); }
        }
        .cine-alerta {
            color: #ff3355; font-family: "Archivo Black", sans-serif;
            font-size: clamp(1.05rem, 5vw, 1.6rem); letter-spacing: .14em;
            animation: parpadeo .32s steps(2) 4;
        }
        @keyframes parpadeo { 50% { opacity: 0; } }
        .cine-jefe { display: none; }
        .cine-jefe.visible { display: block; }
        .cine-jefe img {
            width: min(240px, 58vw); height: min(240px, 58vw); object-fit: cover;
            border-radius: 30px; border: 4px solid #ff3355;
            box-shadow: 0 0 80px rgba(255,51,85,.6), 0 30px 60px rgba(0,0,0,.7);
            animation: caidaJefe .55s cubic-bezier(.15,1.6,.35,1) backwards;
        }
        @keyframes caidaJefe {
            0% { transform: translateY(-120vh) scale(1.6) rotate(-8deg); opacity: 0; }
            100% { transform: translateY(0) scale(1) rotate(0); opacity: 1; }
        }
        .cine-titulo {
            margin: 22px 0 4px; color: #fff;
            font-family: "Archivo Black", sans-serif;
            font-size: clamp(1.6rem, 7vw, 2.6rem); letter-spacing: .04em;
            text-shadow: 0 0 30px rgba(255,51,85,.8);
            animation: parpadeo .5s steps(2) 2;
        }
        .cine-sub { color: #ff9bac; font-weight: 800; font-size: .85rem; letter-spacing: .1em; }
        .cine-panel { display: none; width: min(380px, 100%); margin-top: 26px; }
        .cine-panel.visible { display: block; animation: cineSube .5s cubic-bezier(.2,1.4,.4,1) backwards; }
        @keyframes cineSube { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .cine-panel .select-apuesta { margin-top: 0; }
        .cine-acciones { display: flex; gap: 12px; margin-top: 16px; }
        .cine-acciones button {
            flex: 1; border: 0; border-radius: 14px; padding: 15px 10px;
            font: 800 .9rem Inter, sans-serif; letter-spacing: .05em; cursor: pointer;
        }
        .btn-desafiar { background: linear-gradient(100deg, #ff3355, #ff7b54); color: #fff; box-shadow: 0 12px 36px rgba(255,51,85,.4); }
        .btn-desafiar:hover { filter: brightness(1.1); }
        .btn-huir { background: rgba(255,255,255,.08); color: #fff; border: 1px solid rgba(255,255,255,.16) !important; }

        .intro-michi {
            position: fixed; inset: 0; z-index: 3900;
            display: none; place-items: center; text-align: center; padding: 24px;
            background: radial-gradient(ellipse at 50% 42%, rgba(0,0,0,.35) 0%, rgba(5,6,10,.92) 75%),
                linear-gradient(180deg, color-mix(in srgb, var(--acc) 22%, transparent), transparent 60%);
        }
        .intro-michi.activa { display: grid; animation: cineFade .3s ease; }
        .intro-michi img {
            width: min(190px, 52vw); height: min(190px, 52vw); object-fit: cover;
            border-radius: 28px; border: 4px solid var(--acc);
            box-shadow: 0 0 60px color-mix(in srgb, var(--acc) 55%, transparent), 0 24px 50px rgba(0,0,0,.65);
            animation: caidaJefe .5s cubic-bezier(.15,1.6,.35,1) backwards;
        }
        .intro-michi h2 {
            margin: 18px 0 4px; color: #fff;
            font-family: "Archivo Black", sans-serif;
            font-size: clamp(1.35rem, 6vw, 2rem); letter-spacing: .03em;
            text-shadow: 0 0 26px var(--acc);
        }
        .intro-michi p { color: var(--acc); font-weight: 800; font-size: .82rem; letter-spacing: .12em; margin: 0; }

        /* ── pantalla de captura ── */
        .captura {
            position: fixed; inset: 0; z-index: 4500;
            display: none; place-items: center;
            background: radial-gradient(ellipse at 50% 38%, rgba(68,234,255,.14) 0%, rgba(5,6,10,.96) 72%);
            touch-action: none;
            user-select: none;
            -webkit-user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        .captura.activa { display: grid; animation: cineFade .3s ease; }
        .captura-inner {
            width: min(360px, calc(100% - 32px));
            display: flex; flex-direction: column; align-items: center;
            text-align: center; gap: 8px;
        }
        .captura-tag { margin: 0; color: var(--cyan); font: 800 .68rem Inter, sans-serif; letter-spacing: .18em; }
        .captura-nombre {
            margin: 0; font-family: "Archivo Black", sans-serif;
            font-size: clamp(1.25rem, 6vw, 1.9rem); letter-spacing: .02em;
            text-shadow: 0 0 26px rgba(68,234,255,.5);
        }
        .captura-nivel { margin: 0; color: var(--muted); font-weight: 800; font-size: .72rem; letter-spacing: .12em; }
        .captura-count {
            font-family: "Archivo Black", sans-serif; font-size: 1.55rem; line-height: 1;
            color: var(--gold); text-shadow: 0 0 20px rgba(255,212,82,.5);
        }
        .captura-count.urgente { color: #ff3355; animation: parpadeo .4s steps(2) infinite; }
        .captura-zona {
            position: relative;
            width: min(250px, 62vw); height: min(250px, 62vw);
            min-width: 200px; min-height: 200px;
            display: grid; place-items: center;
            touch-action: none;
            cursor: pointer;
        }
        .captura-zona::before {
            content: ""; position: absolute; inset: 0; border-radius: 50%;
            border: 2px dashed rgba(68,234,255,.4);
            animation: girar 14s linear infinite;
        }
        .captura-gato {
            width: 84%; height: 84%; object-fit: cover; border-radius: 30px;
            border: 4px solid var(--cyan);
            box-shadow: 0 0 50px rgba(68,234,255,.4), 0 24px 50px rgba(0,0,0,.6);
            animation: respirar 1.6s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes respirar { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.045); } }
        .captura-gato.tocado { animation: reboteMichi .28s cubic-bezier(.2, 1.8, .4, 1); }
        @keyframes reboteMichi {
            0% { transform: scale(1); }
            40% { transform: scale(1.16) rotate(-3deg); }
            100% { transform: scale(1); }
        }
        .emoji-flota {
            position: absolute; z-index: 3; font-size: 26px; line-height: 1;
            pointer-events: none;
            animation: flotarEmoji .8s ease-out forwards;
        }
        @keyframes flotarEmoji {
            0% { transform: translate(-50%, -50%) scale(.5); opacity: 1; }
            100% { transform: translate(-50%, -190%) scale(1.4) rotate(18deg); opacity: 0; }
        }
        .captura-barra {
            width: 100%; height: 22px; border-radius: 999px; overflow: hidden;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.16);
            position: relative;
        }
        .captura-fill {
            height: 100%; width: 0%; border-radius: inherit;
            background: linear-gradient(90deg, var(--cyan), var(--lime));
            transition: width .1s linear;
        }
        .captura-meta {
            position: absolute; inset: 0; display: grid; place-items: center;
            font: 800 .62rem Inter, sans-serif; letter-spacing: .1em; color: #fff;
            text-shadow: 0 1px 4px rgba(0,0,0,.75); pointer-events: none;
        }
        .captura-hint { margin: 0; color: var(--muted); font-size: .68rem; font-weight: 700; letter-spacing: .1em; }

        @media (max-width: 560px) {
            .hud { padding: 8px 10px; gap: 8px; }
            .brand span:last-child { display: none; }
            .hud-nav a { padding: 11px 12px; font-size: .66rem; min-height: 44px; display: inline-flex; align-items: center; }
            .balance { font-size: .66rem; padding: 7px 10px; }
            .btn-rescan { right: 14px; bottom: calc(env(safe-area-inset-bottom, 0px) + 20px); padding: 13px 18px; font-size: .78rem; }
            .chip-ubicacion { display: none; }
            .leaflet-control-attribution { font-size: .5rem !important; }
        }
    </style>
</head>
<body>
<div id="mapa"></div>

<header class="hud">
    <div class="brand">
        <span class="brand-mark">M</span>
        <span>MICHI ARENA</span>
    </div>
    <div class="hud-nav">
        <div class="balance"><?= esc(str_replace('$AURA', '$MICHI', formatear_auracoins((int) $jugador['auracoins']))) ?></div>
        <a href="<?= site_url('arena') ?>">Arena</a>
        <?php if (! empty($es_admin)): ?>
            <a href="<?= site_url('mantenedor') ?>">Mantenedor</a>
        <?php endif ?>
        <a href="<?= site_url('salir') ?>">Salir</a>
    </div>
</header>

<?php if ($error !== null): ?>
    <div class="hud-error"><?= esc($error) ?></div>
<?php endif ?>

<div class="chip-ubicacion" id="chipUbicacion">📍 Buscando tu ubicación…</div>

<div class="chip-ubicacion" id="chipLeyenda" style="bottom:calc(env(safe-area-inset-bottom, 0px) + 78px)">
    🟢 tú · 🔵 michi salvaje · 🔴 jefe final
</div>

<button class="btn-rescan" id="btnRescan" type="button">
    <span class="radar">🛰️</span> RESCANEAR
</button>

<form id="formDuelo" action="<?= site_url('arena/iniciar') ?>" method="post" style="display:none">
    <?= csrf_field() ?>
    <input type="hidden" name="apuesta" id="inputApuesta" value="">
    <input type="hidden" name="carta_id" id="inputCartaId" value="">
</form>

<form id="formJefe" action="<?= site_url('arena/jefe') ?>" method="post" style="display:none">
    <?= csrf_field() ?>
    <input type="hidden" name="apuesta" id="inputApuestaJefe" value="">
</form>

<div class="intro-michi" id="introMichi" role="dialog" aria-modal="true">
    <div>
        <img id="introImg" src="" alt="">
        <h2 id="introNombre"></h2>
        <p id="introFrase"></p>
    </div>
</div>

<div class="captura" id="captura" role="dialog" aria-modal="true">
    <div class="captura-inner">
        <p class="captura-tag">🐾 MODO CAPTURA</p>
        <h2 class="captura-nombre" id="capturaNombre"></h2>
        <p class="captura-nivel" id="capturaNivel"></p>
        <div class="captura-count" id="capturaCount">6.0</div>
        <div class="captura-zona" id="capturaZona">
            <img class="captura-gato" id="capturaImg" src="" alt="" draggable="false">
        </div>
        <div class="captura-barra">
            <div class="captura-fill" id="capturaFill"></div>
            <span class="captura-meta" id="capturaMeta">CONFIANZA 0%</span>
        </div>
        <p class="captura-hint">TOCA AL MICHI PARA GANAR SU CONFIANZA</p>
    </div>
</div>

<div class="cinematica" id="cinematica" role="dialog" aria-modal="true">
    <p class="cine-alerta" id="cineAlerta">⚠ UNA PRESENCIA SUPREMA SE ACERCA ⚠</p>
    <div class="cine-jefe" id="cineJefe">
        <img src="/img/cartas/boss.png" alt="El Michi Supremo">
        <h2 class="cine-titulo">EL MICHI SUPREMO</h2>
        <p class="cine-sub">JEFE FINAL · NIVEL 67 · CASI INVENCIBLE</p>
    </div>
    <div class="cine-panel" id="cinePanel">
        <select id="selApuestaJefe" class="select-apuesta"></select>
        <div class="cine-acciones">
            <button type="button" class="btn-huir" id="btnHuirJefe">Huir</button>
            <button type="button" class="btn-desafiar" id="btnDesafiarJefe">⚔ DESAFIAR</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const SPAWNS = <?= json_encode(array_map(static function (array $spawn): array {
        return [
            'spawn_id' => (int) $spawn['spawn_id'],
            'id'       => (int) $spawn['carta_id'],
            'nombre'   => $spawn['nombre'],
            'imagen'   => $spawn['imagen_url'],
            'lvl'      => (int) $spawn['rareza_nivel'],
            'atk'      => (int) $spawn['ataque_aura'],
            'def'      => (int) $spawn['defensa_cringe'],
        ];
    }, $spawns ?? []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const AURACOINS = <?= (int) $jugador['auracoins'] ?>;
    const MAZO_IDS = new Set(<?= json_encode($mazo_ids ?? []) ?>);
    const AVATAR_JUGADOR = <?= json_encode($jugador['avatar_url'] ?? '/img/cartas/michi.jpg') ?>;
    const APUESTAS = [100, 250, 500, 1000, 2000];
    const CENTRO_FALLBACK = [-33.4489, -70.6693];
    const RADIO_SPAWN_M = 800;
    const RESOLVER_URL = <?= json_encode(site_url('captura/resolver'), JSON_UNESCAPED_SLASHES) ?>;
    const csrfName = <?= json_encode(csrf_token()) ?>;
    const csrfHash = <?= json_encode(csrf_hash()) ?>;

    const mapa = L.map('mapa', {
        zoomControl: false,
        attributionControl: true,
    }).setView(CENTRO_FALLBACK, 16);

    L.control.zoom({ position: 'bottomright' }).addTo(mapa);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(mapa);

    const chipUbicacion = document.getElementById('chipUbicacion');

    const iconoJugador = L.divIcon({
        html: `<div class="jugador-wrap"><img src="${AVATAR_JUGADOR}" class="marker-jugador" alt="Tú"><span class="pulso"></span></div>`,
        iconSize: [52, 52],
        iconAnchor: [26, 26],
        popupAnchor: [0, -30],
        className: '',
    });

    let markerJugador = null;
    let spawns = [];
    const spawnsResueltos = new Set();

    function ponerJugador(latlng) {
        if (markerJugador) {
            markerJugador.setLatLng(latlng);
            return;
        }
        markerJugador = L.marker(latlng, { icon: iconoJugador, zIndexOffset: 1000 })
            .addTo(mapa)
            .bindPopup('Tú');
    }

    function puntoAleatorio(centro, radioM) {
        const ang = Math.random() * Math.PI * 2;
        const dist = Math.sqrt(Math.random()) * radioM;
        const dLat = (dist * Math.cos(ang)) / 111320;
        const dLng = (dist * Math.sin(ang)) / (111320 * Math.cos(centro.lat * Math.PI / 180));
        const jitter = () => (Math.random() - .5) * .0004;
        return L.latLng(centro.lat + dLat + jitter(), centro.lng + dLng + jitter());
    }

    function opcionesApuesta() {
        return APUESTAS.map(v =>
            `<option value="${v}" ${v > AURACOINS ? 'disabled' : ''}>💰 ${v.toLocaleString('es-CL')} $MICHI</option>`
        ).join('');
    }

    function pelear(michi) {
        Swal.fire({
            title: michi.nombre.toUpperCase(),
            imageUrl: michi.imagen,
            imageWidth: 140,
            imageHeight: 140,
            imageAlt: michi.nombre,
            html: `
                <div>
                    <span class="stat-michi lvl">LVL ${michi.lvl}</span>
                    <span class="stat-michi atk">⚔ ${michi.atk}</span>
                    <span class="stat-michi def">🛡 ${michi.def}</span>
                </div>
                ${MAZO_IDS.has(michi.id) ? '' : '<p style="margin:10px 0 0;color:#b8ff36;font-weight:800;font-size:.75rem;letter-spacing:.08em">🐾 SIN ATRAPAR — GANA Y ES TUYO</p>'}
                <select id="selApuesta" class="select-apuesta">${opcionesApuesta()}</select>
            `,
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: '¡A PELEAR!',
            denyButtonText: '¡ATRAPAR!',
            cancelButtonText: 'Huir',
            focusConfirm: false,
            preConfirm: () => {
                const sel = document.getElementById('selApuesta');
                const valor = parseInt(sel.value, 10);
                if (! valor || valor > AURACOINS) {
                    Swal.showValidationMessage('No te alcanzan los $MICHI para esa apuesta.');
                    return false;
                }
                return valor;
            },
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('inputApuesta').value = result.value;
                document.getElementById('inputCartaId').value = michi.id || '';
                document.getElementById('formDuelo').submit();
            } else if (result.isDenied) {
                abrirCaptura(michi);
            }
        });
    }

    function spawnear(centro) {
        spawns.forEach(m => mapa.removeLayer(m));
        spawns = [];

        for (const michi of SPAWNS) {
            if (spawnsResueltos.has(michi.spawn_id)) {
                continue;
            }
            const pos = puntoAleatorio(centro, RADIO_SPAWN_M);
            const icono = L.divIcon({
                html: `<img src="${michi.imagen}" class="marker-gato michi-apareciendo" alt="">`,
                iconSize: [46, 46],
                iconAnchor: [23, 23],
                className: '',
            });
            const marker = L.marker(pos, { icon: icono })
                .addTo(mapa)
                .bindTooltip(michi.nombre, {
                    className: 'tooltip-michi',
                    direction: 'top',
                    offset: [0, -26],
                });
            marker.on('click', () => introMichi(michi));
            spawns.push(marker);
        }

        const posJefe = puntoAleatorio(centro, RADIO_SPAWN_M);
        const iconoJefe = L.divIcon({
            html: '<div class="jefe-wrap"><img src="/img/cartas/boss.png" class="marker-jefe" alt=""><span class="aura-jefe"></span></div>',
            iconSize: [66, 66],
            iconAnchor: [33, 33],
            className: '',
        });
        const markerJefe = L.marker(posJefe, { icon: iconoJefe, zIndexOffset: 900 })
            .addTo(mapa)
            .bindTooltip('⚠ EL MICHI SUPREMO', {
                className: 'tooltip-michi',
                direction: 'top',
                offset: [0, -38],
            });
        markerJefe.on('click', abrirCinematica);
        spawns.push(markerJefe);
    }

    const FRASES_MICHI = {
        'Oiia Oiia Cat':        ['OIIA OIIA OIIA… 🌀', '#44eaff'],
        'Chipi Chipi Chapa':    ['CHIPI CHIPI CHAPA CHAPA', '#ffd452'],
        'Big Floppa':           ['EL CARACAL LEGENDARIO APARECE', '#ff4fc8'],
        'Smudge de la Mesa':    ['NO LE GUSTÓ TU ENSALADA', '#9ca4b7'],
        'Michi Llorón':         ['ESTÁ LLORANDO… ¿DE MIEDO?', '#7aa8ff'],
        'Beluga Atómico':       ['HECKER NIVEL ATÓMICO', '#b8ff36'],
        'Michi Suplicante':     ['TE RUEGA QUE NO PELEEN', '#f0b8ff'],
        'Michi Sospechoso':     ['MODO SOSPECHOSO ACTIVADO', '#ff7b54'],
    };
    const introOverlay = document.getElementById('introMichi');
    let introTimer = null;

    function introMichi(carta) {
        const [frase, color] = FRASES_MICHI[carta.nombre] || ['UN MICHI SALVAJE APARECE', '#44eaff'];
        introOverlay.style.setProperty('--acc', color);
        document.getElementById('introImg').src = carta.imagen;
        document.getElementById('introNombre').textContent = carta.nombre.toUpperCase();
        document.getElementById('introFrase').textContent = frase;
        introOverlay.classList.add('activa');
        introTimer = setTimeout(() => {
            introOverlay.classList.remove('activa');
            pelear(carta);
        }, 1500);
    }

    const cinematica = document.getElementById('cinematica');
    let cineTimers = [];

    function abrirCinematica() {
        const alerta = document.getElementById('cineAlerta');
        const jefe = document.getElementById('cineJefe');
        const panel = document.getElementById('cinePanel');
        const sel = document.getElementById('selApuestaJefe');

        alerta.style.display = 'block';
        jefe.classList.remove('visible');
        panel.classList.remove('visible');
        sel.innerHTML = opcionesApuesta();
        cinematica.classList.add('activa');

        cineTimers.forEach(t => clearTimeout(t));
        cineTimers = [
            setTimeout(() => {
                cinematica.classList.add('temblor');
                alerta.style.display = 'none';
                jefe.classList.add('visible');
            }, 1400),
            setTimeout(() => panel.classList.add('visible'), 2200),
        ];
    }

    function cerrarCinematica() {
        cineTimers.forEach(t => clearTimeout(t));
        cineTimers = [];
        cinematica.classList.remove('activa', 'temblor');
    }

    document.getElementById('btnHuirJefe').addEventListener('click', cerrarCinematica);
    document.getElementById('btnDesafiarJefe').addEventListener('click', () => {
        const valor = parseInt(document.getElementById('selApuestaJefe').value, 10);
        if (! valor || valor > AURACOINS) {
            Swal.fire({ title: 'SIN $MICHI', text: 'No te alcanzan los $MICHI para esa apuesta.', icon: 'warning' });
            return;
        }
        document.getElementById('inputApuestaJefe').value = valor;
        document.getElementById('formJefe').submit();
    });

    function centroActual() {
        return markerJugador ? markerJugador.getLatLng() : mapa.getCenter();
    }

    /* ── captura del michi ── */
    const capOverlay = document.getElementById('captura');
    const capImg = document.getElementById('capturaImg');
    const capZona = document.getElementById('capturaZona');
    const capFill = document.getElementById('capturaFill');
    const capMeta = document.getElementById('capturaMeta');
    const capCount = document.getElementById('capturaCount');
    const EMOJIS_CAPTURA = ['🐾', '❤️', '✨', '😺', '⭐', '🍣', '💚'];
    const DURACION_CAPTURA_MS = 6000;
    let captura = null;

    function abrirCaptura(michi) {
        if (captura) {
            return;
        }
        document.getElementById('capturaNombre').textContent = michi.nombre.toUpperCase();
        document.getElementById('capturaNivel').textContent =
            `NIVEL ${michi.lvl} · CONFIANZA NECESARIA ${Math.ceil(50 + michi.lvl * 1.5)}`;
        capImg.src = michi.imagen;
        capCount.textContent = (DURACION_CAPTURA_MS / 1000).toFixed(1);
        capCount.classList.remove('urgente');
        capFill.style.width = '0%';
        capMeta.textContent = 'CONFIANZA 0%';
        captura = {
            spawn: michi,
            toques: 0,
            confianza: 0,
            necesaria: 50 + michi.lvl * 1.5,
            decayPorSeg: michi.lvl * 0.6,
            inicio: performance.now(),
            ultimoFrame: null,
            ultimoTouch: -1,
            resolviendo: false,
            raf: null,
        };
        capOverlay.classList.add('activa');
        captura.raf = requestAnimationFrame(tickCaptura);
    }

    function cerrarCaptura() {
        if (captura && captura.raf) {
            cancelAnimationFrame(captura.raf);
        }
        captura = null;
        capOverlay.classList.remove('activa');
    }

    function pintarConfianza() {
        const pct = Math.min(100, captura.confianza / captura.necesaria * 100);
        capFill.style.width = `${pct}%`;
        capMeta.textContent = `CONFIANZA ${Math.floor(pct)}%`;
    }

    function tickCaptura(now) {
        if (! captura) {
            return;
        }
        const dt = captura.ultimoFrame === null ? 0 : Math.min(100, now - captura.ultimoFrame);
        captura.ultimoFrame = now;
        captura.confianza = Math.max(0, captura.confianza - captura.decayPorSeg * dt / 1000);
        const restante = Math.max(0, DURACION_CAPTURA_MS - (now - captura.inicio));
        capCount.textContent = (restante / 1000).toFixed(1);
        capCount.classList.toggle('urgente', restante <= 2000 && restante > 0);
        pintarConfianza();
        if (restante <= 0) {
            terminarCaptura();
            return;
        }
        captura.raf = requestAnimationFrame(tickCaptura);
    }

    function emojiFlotante(e) {
        const zona = capZona.getBoundingClientRect();
        const punto = (e.touches && e.touches[0]) || e;
        const x = (punto.clientX || zona.left + zona.width / 2) - zona.left;
        const y = (punto.clientY || zona.top + zona.height / 2) - zona.top;
        const emoji = document.createElement('span');
        emoji.className = 'emoji-flota';
        emoji.textContent = EMOJIS_CAPTURA[Math.floor(Math.random() * EMOJIS_CAPTURA.length)];
        emoji.style.left = `${x}px`;
        emoji.style.top = `${y}px`;
        capZona.appendChild(emoji);
        emoji.addEventListener('animationend', () => emoji.remove());
    }

    function tocarMichi(e) {
        if (! captura || captura.resolviendo) {
            return;
        }
        if (e.type === 'touchstart') {
            e.preventDefault();
            captura.ultimoTouch = performance.now();
        } else if (e.type === 'click' && performance.now() - captura.ultimoTouch < 600) {
            return;
        }
        captura.toques++;
        captura.confianza += 8;
        capImg.classList.remove('tocado');
        void capImg.offsetWidth;
        capImg.classList.add('tocado');
        emojiFlotante(e);
        if (navigator.vibrate) {
            navigator.vibrate(10);
        }
        pintarConfianza();
    }

    capZona.addEventListener('touchstart', tocarMichi, { passive: false });
    capZona.addEventListener('click', tocarMichi);

    async function terminarCaptura() {
        if (! captura || captura.resolviendo) {
            return;
        }
        captura.resolviendo = true;
        cancelAnimationFrame(captura.raf);
        captura.raf = null;

        const duracionMs = Math.round(performance.now() - captura.inicio);
        const exito = captura.confianza >= captura.necesaria ? 1 : 0;
        const spawn = captura.spawn;
        const toques = captura.toques;

        let data;
        try {
            const res = await fetch(RESOLVER_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfHash,
                },
                body: JSON.stringify({
                    spawn_id: spawn.spawn_id,
                    toques,
                    duracion_ms: duracionMs,
                    exito,
                    [csrfName]: csrfHash,
                }),
            });
            data = await res.json();
            if (! res.ok || ! data.ok) {
                throw new Error(data.mensaje || 'El servidor se confundió.');
            }
        } catch (err) {
            cerrarCaptura();
            Swal.fire({
                icon: 'error',
                title: 'La captura falló',
                text: err.message,
                confirmButtonText: 'VOLVER AL MAPA',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then(() => window.location.reload());
            return;
        }

        cerrarCaptura();
        spawnsResueltos.add(spawn.spawn_id);
        Swal.fire({
            icon: data.capturado ? 'success' : 'error',
            title: data.mensaje || (data.capturado ? `¡ATRAPASTE A ${spawn.nombre}! Ya está en tu mazo.` : 'El michi huyó'),
            confirmButtonText: 'VOLVER AL MAPA',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then(() => window.location.reload());
    }

    document.getElementById('btnRescan').addEventListener('click', (e) => {
        const btn = e.currentTarget;
        btn.classList.add('girando');
        setTimeout(() => window.location.reload(), 380);
    });

    spawnear(mapa.getCenter());

    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const latlng = L.latLng(pos.coords.latitude, pos.coords.longitude);
                mapa.setView(latlng, 16);
                ponerJugador(latlng);
                spawnear(latlng);
                chipUbicacion.textContent = '📍 Ubicación encontrada — michis cerca tuyo';
            },
            () => {
                ponerJugador(L.latLng(CENTRO_FALLBACK[0], CENTRO_FALLBACK[1]));
                spawnear(mapa.getCenter());
                chipUbicacion.textContent = '📍 Santiago de Chile (ubicación por defecto)';
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
        navigator.geolocation.watchPosition(
            (pos) => ponerJugador(L.latLng(pos.coords.latitude, pos.coords.longitude)),
            () => {},
            { enableHighAccuracy: true }
        );
    } else {
        ponerJugador(L.latLng(CENTRO_FALLBACK[0], CENTRO_FALLBACK[1]));
        chipUbicacion.textContent = '📍 Santiago de Chile (ubicación por defecto)';
    }
</script>
</body>
</html>
