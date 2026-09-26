<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Duelo #<?= esc($duelo['id']) ?> · Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/michi.css">
    <style>
        body { background: linear-gradient(180deg, #4a90d9 0%, #41b4d6 30%, #37bfa9 62%, #79c75e 100%) fixed; }
        button { font: inherit; }
        .arena {
            width: 100%; min-width: 0; max-width: 100%; min-height: 100vh; overflow-x: hidden;
            display: grid; grid-template-rows: auto 1fr auto;
            position: relative; z-index: 1;
        }
        .opponent, .board, .hand { width: 100%; min-width: 0; max-width: 100%; }
        .opponent { padding: 18px clamp(16px, 4vw, 54px); }
        .opponent-inner, .board-inner, .hand-inner { width: min(1280px, 100%); min-width: 0; margin: 0 auto; }
        .opponent-inner {
            display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 18px;
            background: rgba(255, 255, 255, .92); border-radius: 24px; padding: 14px 20px;
            box-shadow: var(--shadow-sm);
        }
        .avatar {
            width: 70px; height: 70px; border-radius: 50%; object-fit: cover;
            border: 4px solid var(--orange); box-shadow: 0 8px 20px rgba(255, 154, 61, .35);
        }
        .identity small { color: var(--orange); font-weight: 900; letter-spacing: .12em; font-size: .7rem; }
        .identity h1 { margin: 3px 0 0; font-size: clamp(1rem, 3vw, 1.4rem); font-weight: 600; color: var(--ink); }
        .aura-panel { min-width: min(430px, 42vw); }
        .aura-label { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: .78rem; font-weight: 900; color: var(--ink-soft); }
        .aura-label span:first-child { color: var(--muted); letter-spacing: .09em; }
        .bar { height: 15px; overflow: hidden; border-radius: 999px; background: #e3e9f5; box-shadow: inset 0 2px 6px rgba(30, 60, 120, .18); }
        .bar-fill { height: 100%; border-radius: inherit; transition: width .55s cubic-bezier(.2, .8, .2, 1); }
        #opponent-bar { background: linear-gradient(90deg, var(--orange), var(--red)); }
        #player-bar { background: linear-gradient(90deg, var(--sky-2), var(--lime-deep)); }

        .board { min-height: 320px; padding: 24px clamp(16px, 4vw, 54px); display: grid; align-items: center; }
        .board-inner { display: grid; grid-template-columns: minmax(0, 1fr) 250px; gap: 22px; }
        .stage {
            min-height: 280px; border-radius: 28px; display: grid; place-items: center;
            position: relative; overflow: hidden;
            background:
                radial-gradient(circle at 50% 40%, rgba(255, 255, 255, .55), transparent 55%),
                rgba(255, 255, 255, .28);
            border: 2px solid rgba(255, 255, 255, .45);
            box-shadow: var(--shadow-sm);
        }
        .stage::before {
            content: ""; position: absolute; width: 250px; height: 250px; border-radius: 50%;
            border: 3px dashed rgba(255, 255, 255, .65);
            box-shadow: 0 0 0 30px rgba(255, 255, 255, .10), 0 0 0 64px rgba(255, 255, 255, .05);
            animation: spin 24s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .versus { position: relative; text-align: center; }
        .versus strong {
            display: block; font: 700 clamp(4rem, 10vw, 7.6rem)/.8 Fredoka, sans-serif;
            color: rgba(255, 255, 255, .85); letter-spacing: -.04em;
            text-shadow: 0 4px 0 rgba(30, 60, 120, .25);
        }
        .versus span {
            display: inline-block; margin-top: 20px; padding: 7px 16px; border-radius: 999px;
            color: #233208; background: var(--lime); font-weight: 900; letter-spacing: .12em; font-size: .78rem;
            box-shadow: 0 6px 16px rgba(147, 212, 0, .4);
        }
        .stage.hit { animation: impact .42s ease; }
        @keyframes impact { 35% { transform: scale(.985); filter: saturate(1.6); } 60% { box-shadow: inset 0 0 100px rgba(255, 90, 102, .25); } }

        /* ── zona de combate animada ── */
        .stage.fighting { min-height: 320px; }
        .combat {
            display: none;
            position: relative; z-index: 2;
            width: 100%;
            padding: clamp(36px, 6vw, 50px) clamp(8px, 3vw, 30px) clamp(16px, 3vw, 24px);
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center; gap: clamp(6px, 3vw, 30px);
        }
        .stage.fighting .combat { display: grid; }
        .stage.fighting .versus {
            position: absolute; top: 12px; left: 50%; transform: translateX(-50%); z-index: 6;
        }
        .stage.fighting .versus strong { display: none; }
        .stage.fighting .versus span { margin-top: 0; }
        .fighter { position: relative; display: flex; flex-direction: column; align-items: center; gap: 8px; min-width: 0; }
        .fighter-card {
            position: relative;
            width: clamp(92px, 24vw, 150px); aspect-ratio: 3 / 4;
            display: flex; align-items: center; justify-content: center;
            border-radius: 16px; background: #fff; padding: 7px;
            border: 3px solid #fff; box-shadow: var(--shadow-lg);
            will-change: transform;
        }
        .fighter-card img { width: 100%; height: 100%; object-fit: contain; pointer-events: none; }
        .fighter-name {
            max-width: 100%; padding: 5px 12px; border-radius: 999px;
            background: rgba(255, 255, 255, .92); color: var(--ink);
            font-size: .62rem; font-weight: 900; letter-spacing: .05em; text-transform: uppercase;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; box-shadow: var(--shadow-sm);
        }
        .combat-vs {
            font: 700 clamp(1.5rem, 5vw, 2.6rem)/1 Fredoka, sans-serif;
            color: rgba(255, 255, 255, .9); text-shadow: 0 3px 0 rgba(30, 60, 120, .25);
        }
        .fighter.enter-left .fighter-card { animation: enterLeft .42s cubic-bezier(.2, .9, .3, 1.25); }
        .fighter.enter-right .fighter-card { animation: enterRight .42s cubic-bezier(.2, .9, .3, 1.25); }
        @keyframes enterLeft {
            0% { transform: translate(-70px, 60px) rotate(-18deg) scale(.3); opacity: 0; }
            60% { transform: translate(4px, -10px) rotate(5deg) scale(1.06); opacity: 1; }
            100% { transform: none; }
        }
        @keyframes enterRight {
            0% { transform: translate(70px, -60px) rotate(18deg) scale(.3); opacity: 0; }
            60% { transform: translate(-4px, -10px) rotate(-5deg) scale(1.06); opacity: 1; }
            100% { transform: none; }
        }
        .flying-card {
            position: fixed; z-index: 60; pointer-events: none;
            object-fit: contain; background: #fff; padding: 5px;
            border-radius: 14px; box-shadow: var(--shadow-lg);
        }
        .flash-overlay { position: absolute; inset: 0; z-index: 5; border-radius: inherit; pointer-events: none; opacity: 0; }
        .flash-overlay.white { background: rgba(255, 255, 255, .45); animation: flashFade .3s ease-out; }
        .flash-overlay.gold { background: radial-gradient(circle, rgba(255, 201, 60, .6), transparent 72%); animation: flashFade .55s ease-out; }
        @keyframes flashFade { 0% { opacity: 0; } 18% { opacity: 1; } 100% { opacity: 0; } }
        .arena.shake { animation: shakeSm .38s ease-in-out; }
        .arena.shake-hard { animation: shakeLg .55s ease-in-out; }
        @keyframes shakeSm {
            20% { transform: translate(3px, -2px); } 40% { transform: translate(-3px, 2px); }
            60% { transform: translate(2px, 1px); } 80% { transform: translate(-2px, -1px); }
        }
        @keyframes shakeLg {
            15% { transform: translate(8px, -5px) rotate(.4deg); } 35% { transform: translate(-8px, 5px) rotate(-.4deg); }
            55% { transform: translate(6px, 3px) rotate(.25deg); } 75% { transform: translate(-5px, -3px); }
        }
        .impact-burst {
            position: absolute; z-index: 7; width: 90px; height: 90px; border-radius: 50%;
            background: radial-gradient(circle, #fff 0%, rgba(255, 201, 60, .95) 35%, transparent 70%);
            transform: translate(-50%, -50%) scale(0);
            animation: burst .42s ease-out forwards; pointer-events: none;
        }
        @keyframes burst {
            30% { transform: translate(-50%, -50%) scale(1.15); opacity: 1; }
            100% { transform: translate(-50%, -50%) scale(1.7); opacity: 0; }
        }
        .paw { position: absolute; z-index: 8; font-size: 17px; line-height: 1; pointer-events: none; }
        .confetti { position: absolute; top: 0; z-index: 9; line-height: 1; pointer-events: none; }
        .dmg-float {
            position: absolute; z-index: 8; left: 50%; top: -12px;
            transform: translateX(-50%);
            font: 700 clamp(1.3rem, 5.5vw, 2.1rem)/1 Fredoka, sans-serif;
            color: #fff; -webkit-text-stroke: 2px var(--red);
            text-shadow: 0 3px 0 rgba(0, 0, 0, .18);
            white-space: nowrap; pointer-events: none;
            animation: dmgFloat .95s ease-out forwards;
        }
        .dmg-float.crit { -webkit-text-stroke-color: #d99400; font-size: clamp(1.5rem, 6.5vw, 2.4rem); }
        .dmg-float.crit-label { top: 34%; -webkit-text-stroke-color: #d99400; font-size: clamp(1.1rem, 5vw, 1.8rem); letter-spacing: .06em; }
        .dmg-float.heal { -webkit-text-stroke-color: var(--lime-deep); font-size: clamp(.95rem, 4vw, 1.4rem); }
        @keyframes dmgFloat {
            0% { transform: translate(-50%, 0) scale(.6); opacity: 0; }
            18% { transform: translate(-50%, -18px) scale(1.1); opacity: 1; }
            100% { transform: translate(-50%, -70px) scale(1); opacity: 0; }
        }
        .aura-orb {
            position: absolute; z-index: 9; width: 16px; height: 16px; border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #fff, var(--sky-2) 45%, var(--sky-1));
            box-shadow: 0 0 12px 3px rgba(65, 180, 214, .7);
            pointer-events: none;
        }
        .shield-bubble {
            position: absolute; z-index: 6; inset: -13px; border-radius: 26px;
            background: radial-gradient(circle at 30% 28%, rgba(255, 255, 255, .65), rgba(65, 180, 214, .32) 55%, rgba(74, 144, 217, .55));
            border: 2px solid rgba(255, 255, 255, .85);
            box-shadow: 0 0 24px rgba(65, 180, 214, .6), inset 0 0 18px rgba(255, 255, 255, .5);
            transform: scale(0); pointer-events: none;
        }
        .shield-bubble.up { transform: scale(1); transition: transform .25s cubic-bezier(.2, .9, .3, 1.4); }
        .shield-bubble.pop { transform: scale(1.3); opacity: 0; transition: transform .22s ease-in, opacity .22s ease-in; }
        .fighter.fainted .fighter-card { animation: faint .95s ease-in forwards; }
        @keyframes faint {
            55% { transform: translateY(16px) rotate(72deg); filter: grayscale(1); opacity: .8; }
            100% { transform: translateY(70px) rotate(86deg); filter: grayscale(1); opacity: 0; }
        }
        .bar-fill.low {
            background: linear-gradient(90deg, #ff7a85, var(--red)) !important;
            animation: lowPulse .9s ease-in-out infinite;
        }
        @keyframes lowPulse { 50% { filter: brightness(1.4); } }
        @keyframes dmgFade { 0% { opacity: 0; } 20% { opacity: 1; } 100% { opacity: 0; } }
        @keyframes burstFade {
            20% { opacity: .8; transform: translate(-50%, -50%) scale(.8); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(1); }
        }
        @keyframes faintFade { 100% { filter: grayscale(1); opacity: .25; } }
        @media (prefers-reduced-motion: reduce) {
            .stage::before,
            .arena.shake, .arena.shake-hard,
            .bar-fill.low,
            .fighter.enter-left .fighter-card,
            .fighter.enter-right .fighter-card { animation: none !important; }
            .dmg-float { animation: dmgFade .55s ease-out forwards; }
            .impact-burst { animation: burstFade .35s ease-out forwards; }
            .fighter.fainted .fighter-card { animation: faintFade .6s ease-out forwards; }
        }
        @media (max-width: 760px) {
            .stage.fighting { min-height: 250px; }
            .fighter-card { width: clamp(84px, 27vw, 120px); }
            .fighter-name { font-size: .55rem; padding: 4px 9px; }
        }

        .feed { border-radius: 22px; background: rgba(255, 255, 255, .92); overflow: hidden; box-shadow: var(--shadow-sm); }
        .feed-title { padding: 13px 16px; border-bottom: 2px dashed var(--line); color: var(--sky-1); font-size: .72rem; font-weight: 900; letter-spacing: .14em; }
        #battle-log { height: 230px; overflow: auto; padding: 8px 16px 16px; scrollbar-width: thin; }
        .log-entry { padding: 10px 0; border-bottom: 1px solid var(--line); color: var(--ink-soft); font-size: .8rem; font-weight: 700; line-height: 1.55; }
        .log-entry strong { color: var(--sky-1); }
        .log-entry.error { color: var(--red); }

        .hand { overflow: hidden; padding: 20px clamp(16px, 4vw, 54px) 30px; }
        .hand-inner { overflow: hidden; }
        .hand-head {
            display: grid; grid-template-columns: minmax(0, 1fr) minmax(260px, 420px) auto;
            align-items: end; gap: 22px; margin-bottom: 18px;
            background: rgba(255, 255, 255, .92); border-radius: 24px; padding: 16px 20px;
            box-shadow: var(--shadow-sm);
        }
        .hand-head > * { min-width: 0; }
        .hand-title small { display: block; color: var(--sky-1); letter-spacing: .14em; font-weight: 900; }
        .hand-title h2 { margin: 4px 0 0; font: 600 1.3rem Fredoka, sans-serif; color: var(--ink); }
        .player-aura .aura-label { margin-bottom: 7px; }
        .wallet { color: #8a5b00; font-weight: 900; white-space: nowrap; background: linear-gradient(180deg, #ffe177, var(--gold)); border-radius: 999px; padding: 9px 16px; }

        /* cartas coleccionables estilo PoGO */
        .cards { display: grid; width: 100%; grid-template-columns: repeat(6, minmax(158px, 1fr)); gap: 13px; min-width: 0; max-width: 100%; }
        .mcard {
            position: relative; min-height: 255px; display: flex; flex-direction: column; overflow: hidden;
            border-radius: 20px; background: #fff; border: 3px solid #fff;
            box-shadow: var(--shadow-sm);
            transition: transform .2s, box-shadow .2s;
        }
        .mcard:hover { transform: translateY(-7px) rotate(-1deg); box-shadow: var(--shadow-lg); }
        .mcard-image {
            height: 104px; width: 100%; object-fit: contain;
            background: linear-gradient(135deg, #d8f2ff, #e6f9d8);
        }
        .mcard-body { padding: 12px; display: flex; flex: 1; flex-direction: column; }
        .mcard h3 { margin: 0 0 8px; font-size: .92rem; font-weight: 600; line-height: 1.1; color: var(--ink); }
        .lvl {
            position: absolute; top: 8px; right: 8px; z-index: 1;
            min-width: 34px; height: 34px; padding: 0 7px; border-radius: 999px;
            display: grid; place-items: center;
            color: #fff; background: #596071; font-size: .6rem; font-weight: 900; line-height: 1;
            border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, .2);
        }
        .rareza-rara { background: #2467d8; }
        .rareza-epica { background: #8a45d8; }
        .rareza-67 { color: #4a3500; background: linear-gradient(140deg, var(--gold), var(--orange)); }
        .stats { display: flex; gap: 7px; margin-bottom: 8px; }
        .stat {
            flex: 1; padding: 6px 4px; border-radius: 10px; text-align: center;
            background: #f2f7ff; border: 1.5px solid var(--line);
            font-size: .62rem; font-weight: 900; color: var(--muted);
        }
        .stat b { display: block; color: var(--sky-1); font-size: .85rem; font-family: Fredoka, sans-serif; }
        .effect { margin: 0 0 10px; color: var(--muted); font-size: .62rem; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .play-card {
            margin-top: auto; width: 100%; padding: 10px 8px; font-size: .72rem;
        }

        @media (max-width: 980px) {
            .cards { display: flex; overflow-x: auto; overscroll-behavior-x: contain; scroll-snap-type: x mandatory; padding: 6px 2px 14px; }
            .mcard { flex: 0 0 180px; min-width: 0; scroll-snap-align: start; }
        }
        @media (max-width: 760px) {
            .opponent { padding-top: 14px; padding-bottom: 14px; }
            .opponent-inner { grid-template-columns: 52px 1fr; gap: 12px; padding: 12px 14px; }
            .avatar { width: 52px; height: 52px; border-width: 3px; }
            .aura-panel { grid-column: 1 / -1; min-width: 0; }
            .board { padding-top: 14px; padding-bottom: 14px; }
            .board-inner { grid-template-columns: 1fr; }
            .stage { min-height: 160px; }
            .feed { display: none; }
            .hand-head { grid-template-columns: 1fr auto; gap: 12px; }
            .player-aura { grid-column: 1 / -1; grid-row: 2; }
        }
        @media (max-width: 480px) {
            .hand-head { grid-template-columns: minmax(0, 1fr); align-items: stretch; }
            .player-aura { grid-column: 1; grid-row: auto; }
            .wallet { white-space: normal; text-align: center; }
        }
    </style>
</head>
<body>
<main class="arena">
    <section class="opponent">
        <div class="opponent-inner">
            <img class="avatar" src="/img/cartas/bot.gif" alt="">
            <div class="identity">
                <small>RIVAL MICHI</small>
                <h1><?= esc($duelo['bot_nombre']) ?></h1>
            </div>
            <div class="aura-panel">
                <div class="aura-label">
                    <span>AURA DEL RIVAL</span>
                    <span><b id="opponent-aura"><?= esc($duelo['aura_oponente']) ?></b> / <?= esc($duelo['aura_max_bot']) ?></span>
                </div>
                <div class="bar"><div id="opponent-bar" class="bar-fill" style="width: <?= calcular_barra_aura((int) $duelo['aura_oponente'], (int) $duelo['aura_max_bot']) ?>%"></div></div>
            </div>
        </div>
    </section>

    <section class="board">
        <div class="board-inner">
            <div id="stage" class="stage">
                <div class="versus">
                    <strong>VS</strong>
                    <span>DUELO #<?= esc($duelo['id']) ?> · TURNO <b id="turn-number"><?= esc($duelo['turno']) ?></b></span>
                </div>
                <div class="combat" id="combat">
                    <div class="fighter fighter-player" id="fighter-player">
                        <div class="fighter-card"><img alt=""></div>
                        <span class="fighter-name"></span>
                    </div>
                    <div class="combat-vs">VS</div>
                    <div class="fighter fighter-bot" id="fighter-bot">
                        <div class="fighter-card"><img alt=""></div>
                        <span class="fighter-name"></span>
                    </div>
                </div>
                <div class="flash-overlay" id="flash-overlay"></div>
            </div>
            <aside class="feed">
                <div class="feed-title">FEED DEL COMBATE</div>
                <div id="battle-log">
                    <div class="log-entry">Apuesta retenida: <strong><?= esc(formatear_auracoins((int) $duelo['auracoins_apuesta'])) ?></strong>.</div>
                    <div class="log-entry"><?= esc($duelo['bot_nombre']) ?> entró a la arena. Elige una carta para comenzar.</div>
                </div>
            </aside>
        </div>
    </section>

    <section class="hand">
        <div class="hand-inner">
            <div class="hand-head">
                <div class="hand-title">
                    <small>TU MAZO</small>
                    <h2>ELIGE TU PRÓXIMO MOVIMIENTO</h2>
                </div>
                <div class="player-aura">
                    <div class="aura-label">
                        <span>TU AURA</span>
                        <span><b id="player-aura"><?= esc($duelo['aura_retador']) ?></b> / <?= esc($duelo['aura_max_retador']) ?></span>
                    </div>
                    <div class="bar"><div id="player-bar" class="bar-fill" style="width: <?= calcular_barra_aura((int) $duelo['aura_retador'], (int) $duelo['aura_max_retador']) ?>%"></div></div>
                </div>
                <div id="wallet" class="wallet"><?= esc(formatear_auracoins((int) $duelo['auracoins'])) ?></div>
            </div>

            <div class="cards">
                <?php foreach ($mazo as $carta): ?>
                    <article class="mcard">
                        <span class="lvl <?= esc(badge_rareza((int) $carta['rareza_nivel'])) ?>">LVL <?= esc($carta['rareza_nivel']) ?></span>
                        <img class="mcard-image" src="<?= esc($carta['imagen_url']) ?>" alt="" draggable="false">
                        <div class="mcard-body">
                            <h3><?= esc($carta['nombre']) ?></h3>
                            <div class="stats">
                                <span class="stat"><b><?= esc($carta['ataque_aura']) ?></b>⚔️ ATAQUE</span>
                                <span class="stat"><b><?= esc($carta['defensa_cringe']) ?></b>🛡️ ANTI-CRINGE</span>
                            </div>
                            <p class="effect"><?= esc(str_replace('_', ' ', $carta['efecto_especial'])) ?></p>
                            <button class="play-card" type="button" data-card-id="<?= esc($carta['id']) ?>" <?= $duelo['estado'] !== 'BATALLANDO' ? 'disabled' : '' ?>>
                                TIRAR CARTA
                            </button>
                        </div>
                    </article>
                <?php endforeach ?>
            </div>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/js/michi.js"></script>
<script>
const duelId = <?= (int) $duelo['id'] ?>;
const playUrl = <?= json_encode(site_url('arena/jugar'), JSON_UNESCAPED_SLASHES) ?>;
const arenaUrl = <?= json_encode(site_url('arena'), JSON_UNESCAPED_SLASHES) ?>;
const csrfName = <?= json_encode(csrf_token()) ?>;
const csrfHash = <?= json_encode(csrf_hash()) ?>;
const playerMaxAura = <?= (int) $duelo['aura_max_retador'] ?>;
const opponentMaxAura = <?= (int) $duelo['aura_max_bot'] ?>;
const buttons = [...document.querySelectorAll('.play-card')];
const battleLog = document.getElementById('battle-log');
const stage = document.getElementById('stage');
const cardImages = <?= json_encode(array_column($mazo, 'imagen_url', 'nombre'), JSON_UNESCAPED_SLASHES) ?>;
const botCardFallback = <?= json_encode(base_url('img/cartas/bot.gif')) ?>;
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const arenaEl = document.querySelector('.arena');
const flashOverlay = document.getElementById('flash-overlay');
const fighterEls = {
    player: document.getElementById('fighter-player'),
    bot: document.getElementById('fighter-bot'),
};
let requestInFlight = false;
let currentTurn = <?= (int) $duelo['turno'] ?>;
let lastPlayStartedAt = 0;

const formatCoins = (amount) => {
    const sign = amount < 0 ? '-' : amount > 0 ? '+' : '';
    return `💰 ${sign}${Math.abs(amount).toLocaleString('es-CL')} $AURA`;
};

const updateAura = (side, value, max) => {
    document.getElementById(`${side}-aura`).textContent = value;
    const bar = document.getElementById(`${side}-bar`);
    const pct = Math.max(0, Math.min(100, value / max * 100));
    bar.style.width = `${pct}%`;
    bar.classList.toggle('low', pct <= 30);
};

const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const fighterCard = (side) => fighterEls[side].querySelector('.fighter-card');

const centerInStage = (el) => {
    const stageRect = stage.getBoundingClientRect();
    const rect = el.getBoundingClientRect();
    return {
        x: rect.left - stageRect.left + rect.width / 2,
        y: rect.top - stageRect.top + rect.height / 2,
    };
};

const floatText = (side, text, cls = '') => {
    const el = document.createElement('div');
    el.className = `dmg-float ${cls}`.trim();
    el.textContent = text;
    fighterEls[side].appendChild(el);
    el.addEventListener('animationend', () => el.remove());
};

const spawnPaws = (x, y) => {
    if (reducedMotion) {
        return;
    }
    for (let i = 0; i < 5; i++) {
        const paw = document.createElement('span');
        paw.className = 'paw';
        paw.textContent = Math.random() < 0.6 ? '🐾' : '⭐';
        paw.style.left = `${x}px`;
        paw.style.top = `${y}px`;
        stage.appendChild(paw);
        const angle = Math.random() * Math.PI * 2;
        const dist = 34 + Math.random() * 46;
        paw.animate([
            { transform: 'translate(-50%, -50%) scale(.5)', opacity: 1 },
            { transform: `translate(calc(-50% + ${Math.cos(angle) * dist}px), calc(-50% + ${Math.sin(angle) * dist - 20}px)) rotate(${Math.random() * 140 - 70}deg) scale(1)`, opacity: 0 },
        ], { duration: 640, easing: 'ease-out' }).finished.catch(() => {}).finally(() => paw.remove());
    }
};

const spawnBurst = (side) => {
    const center = centerInStage(fighterCard(side));
    const burst = document.createElement('div');
    burst.className = 'impact-burst';
    burst.style.left = `${center.x}px`;
    burst.style.top = `${center.y}px`;
    stage.appendChild(burst);
    burst.addEventListener('animationend', () => burst.remove());
    spawnPaws(center.x, center.y);
};

const flash = (kind) => {
    if (reducedMotion) {
        return;
    }
    flashOverlay.classList.remove('white', 'gold');
    void flashOverlay.offsetWidth;
    flashOverlay.classList.add(kind);
};

const shakeArena = (strong) => {
    if (reducedMotion) {
        return;
    }
    arenaEl.classList.remove('shake', 'shake-hard');
    void arenaEl.offsetWidth;
    arenaEl.classList.add(strong ? 'shake-hard' : 'shake');
};

const setFighter = (side, name, imgUrl) => {
    fighterEls[side].querySelector('img').src = imgUrl;
    fighterEls[side].querySelector('.fighter-name').textContent = name;
};

const enterAnim = (side) => {
    const fighter = fighterEls[side];
    fighter.classList.remove('enter-left', 'enter-right');
    void fighter.offsetWidth;
    fighter.classList.add(side === 'player' ? 'enter-left' : 'enter-right');
};

const flyCard = (sourceImg) => {
    const card = fighterCard('player');
    const from = sourceImg.getBoundingClientRect();
    const to = card.getBoundingClientRect();
    const ghost = sourceImg.cloneNode();
    ghost.className = 'flying-card';
    Object.assign(ghost.style, {
        left: `${from.left}px`, top: `${from.top}px`,
        width: `${from.width}px`, height: `${from.height}px`,
    });
    document.body.appendChild(ghost);
    card.style.opacity = '0';
    return ghost.animate([
        { left: `${from.left}px`, top: `${from.top}px`, width: `${from.width}px`, height: `${from.height}px`, transform: 'rotate(-12deg)', offset: 0 },
        { left: `${to.left - 20}px`, top: `${to.top - 50}px`, width: `${to.width}px`, height: `${to.height}px`, transform: 'rotate(7deg)', offset: .62 },
        { left: `${to.left}px`, top: `${to.top}px`, width: `${to.width}px`, height: `${to.height}px`, transform: 'rotate(0deg)', offset: 1 },
    ], { duration: 400, easing: 'cubic-bezier(.3, .7, .4, 1)' }).finished
        .catch(() => {})
        .then(() => { ghost.remove(); card.style.opacity = ''; });
};

const lunge = (side) => {
    const dir = side === 'player' ? 1 : -1;
    if (reducedMotion) {
        return fighterCard(side).animate(
            [{ opacity: 1 }, { opacity: .45 }, { opacity: 1 }],
            { duration: 260 }
        ).finished.catch(() => {});
    }
    return fighterCard(side).animate([
        { transform: 'translateX(0)' },
        { transform: `translateX(${dir * 34}%) rotate(${dir * 4}deg) scale(1.07)`, offset: .38 },
        { transform: 'translateX(0)' },
    ], { duration: 420, easing: 'cubic-bezier(.3, .7, .3, 1)' }).finished.catch(() => {});
};

const knockback = (side) => {
    const card = fighterCard(side);
    if (reducedMotion) {
        card.animate([{ filter: 'brightness(1.9)' }, { filter: 'none' }], { duration: 260 });
        return;
    }
    const dir = side === 'player' ? -1 : 1;
    card.animate([
        { transform: 'translateX(0)' },
        { transform: `translateX(${dir * 18}px) rotate(${dir * 6}deg)`, offset: .3 },
        { transform: `translateX(${dir * -8}px) rotate(${dir * -2}deg)`, offset: .58 },
        { transform: `translateX(${dir * 4}px)`, offset: .8 },
        { transform: 'translateX(0)' },
    ], { duration: 400, easing: 'ease-out' });
};

const stealOrbs = (fromSide, toSide) => {
    const from = centerInStage(fighterCard(fromSide));
    const to = centerInStage(fighterCard(toSide));
    const flights = [];
    for (let i = 0; i < 4; i++) {
        const orb = document.createElement('div');
        orb.className = 'aura-orb';
        orb.style.left = `${from.x - 8}px`;
        orb.style.top = `${from.y - 8}px`;
        stage.appendChild(orb);
        flights.push(orb.animate([
            { transform: 'translate(0, 0) scale(.5)', opacity: 0 },
            { transform: `translate(${(to.x - from.x) / 2}px, ${(to.y - from.y) / 2 - 46}px) scale(1.15)`, opacity: 1, offset: .5 },
            { transform: `translate(${to.x - from.x}px, ${to.y - from.y}px) scale(.6)`, opacity: 0 },
        ], { duration: reducedMotion ? 320 : 560, delay: i * 80, easing: 'ease-in-out' })
            .finished.catch(() => {}).finally(() => orb.remove()));
    }
    return Promise.all(flights);
};

const shieldUp = (side) => {
    const bubble = document.createElement('div');
    bubble.className = 'shield-bubble';
    fighterCard(side).appendChild(bubble);
    requestAnimationFrame(() => bubble.classList.add('up'));
    return bubble;
};

const shieldPop = (bubble) => {
    bubble.classList.add('pop');
    setTimeout(() => bubble.remove(), 280);
};

const confetti = () => {
    const stageHeight = stage.clientHeight;
    const glyphs = ['🐾', '⭐', '😺', '🎉', '✨', '💚'];
    for (let i = 0; i < (reducedMotion ? 10 : 34); i++) {
        const piece = document.createElement('span');
        piece.className = 'confetti';
        piece.textContent = glyphs[Math.floor(Math.random() * glyphs.length)];
        piece.style.left = `${Math.random() * 96}%`;
        piece.style.fontSize = `${14 + Math.random() * 14}px`;
        stage.appendChild(piece);
        piece.animate([
            { transform: 'translateY(-30px) rotate(0deg)', opacity: 1 },
            { transform: `translateY(${stageHeight + 40}px) rotate(${Math.random() * 720 - 360}deg)`, opacity: .9 },
        ], { duration: 900 + Math.random() * 900, easing: 'ease-in', delay: Math.random() * 350 })
            .finished.catch(() => {}).finally(() => piece.remove());
    }
};

const faintFighter = (side) => {
    fighterEls[side].classList.add('fainted');
    return wait(reducedMotion ? 650 : 1000);
};

const attackSequence = async (attacker, defender, damage, effect) => {
    const isCrit = effect === 'CRITICO_MEME';
    const lungeDone = lunge(attacker);
    await wait(reducedMotion ? 80 : 180);
    knockback(defender);
    spawnBurst(defender);
    floatText(defender, `-${damage}`, isCrit ? 'crit' : '');
    if (isCrit) {
        floatText(defender, '¡CRÍTICO!', 'crit-label');
        flash('gold');
    } else {
        flash('white');
    }
    shakeArena(isCrit);
    await lungeDone;
};

const playTurn = async (data, button) => {
    stage.classList.add('fighting');
    buttons.forEach((b) => { b.textContent = '¡EN COMBATE!'; });

    const sourceImg = button.closest('.mcard')?.querySelector('.mcard-image');
    const playerImgUrl = cardImages[data.carta_jugada] || sourceImg?.src || botCardFallback;
    const botImgUrl = cardImages[data.carta_bot] || botCardFallback;

    setFighter('player', data.carta_jugada, playerImgUrl);
    setFighter('bot', data.carta_bot, botImgUrl);

    const entrance = sourceImg && !reducedMotion
        ? flyCard(sourceImg)
        : (enterAnim('player'), Promise.resolve());
    enterAnim('bot');
    await entrance;
    await wait(reducedMotion ? 80 : 140);

    const botHeal = data.efecto_bot === 'ROBAR_AURA'
        ? Math.floor(Number(data.dano_recibido) * 0.25)
        : 0;
    const opponentAfterHit = Math.max(0, Number(data.aura_oponente) - botHeal);

    await attackSequence('player', 'bot', data.dano_realizado, data.efecto_jugador);
    updateAura('opponent', opponentAfterHit, opponentMaxAura);

    if (data.efecto_jugador === 'ROBAR_AURA' && Number(data.aura_robada) > 0) {
        await stealOrbs('bot', 'player');
        const healed = Math.min(
            playerMaxAura,
            Number(document.getElementById('player-aura').textContent) + Number(data.aura_robada)
        );
        updateAura('player', healed, playerMaxAura);
        floatText('player', `+${data.aura_robada} AURA`, 'heal');
        await wait(reducedMotion ? 80 : 220);
    }

    if (data.estado === 'VICTORIA_RETADOR') {
        updateAura('player', data.aura_retador, playerMaxAura);
        confetti();
        await wait(1200);
        return;
    }

    let shield = null;
    if (data.efecto_jugador === 'ESCUDO_CHILL') {
        shield = shieldUp('player');
        floatText('player', '¡ESCUDO!', 'heal');
        await wait(reducedMotion ? 140 : 300);
    }

    if (shield) {
        setTimeout(() => shieldPop(shield), reducedMotion ? 90 : 200);
    }
    await attackSequence('bot', 'player', data.dano_recibido, data.efecto_bot);
    updateAura('player', data.aura_retador, playerMaxAura);

    if (botHeal > 0) {
        await stealOrbs('player', 'bot');
        updateAura('opponent', data.aura_oponente, opponentMaxAura);
    }

    if (data.estado === 'DERROTA_RETADOR') {
        await faintFighter('player');
        await wait(200);
        return;
    }

    await wait(reducedMotion ? 60 : 200);
};

const addLog = (html, isError = false) => {
    const entry = document.createElement('div');
    entry.className = `log-entry${isError ? ' error' : ''}`;
    entry.innerHTML = html;
    battleLog.appendChild(entry);
    battleLog.scrollTop = battleLog.scrollHeight;
};

const setBusy = (busy) => {
    buttons.forEach((button) => {
        button.disabled = busy;
        button.textContent = busy ? 'CALCULANDO AURA…' : 'TIRAR CARTA';
    });
};

const showResult = (data) => {
    const won = data.estado === 'VICTORIA_RETADOR';
    michiSwal({
        icon: won ? 'success' : 'error',
        title: won ? '¡W ABSOLUTA!' : 'CRINGE TOTAL',
        html: `<p style="margin:0 0 10px">${michiEscapeHtml(data.frase_resultado)}</p>`
            + `<strong style="font-family:Fredoka,sans-serif;font-size:1.1rem;color:${won ? '#4d7a00' : '#c23045'}">`
            + `${won ? 'Pozo cobrado' : 'Apuesta perdida'}: ${michiEscapeHtml(formatCoins(data.auracoins_movimiento))}</strong>`,
        confirmButtonText: 'VOLVER A LA ARENA',
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then(() => { window.location.href = arenaUrl; });
};

buttons.forEach((button) => {
    button.addEventListener('click', async () => {
        const now = performance.now();

        if (requestInFlight || now - lastPlayStartedAt < 500) {
            return;
        }

        requestInFlight = true;
        lastPlayStartedAt = now;
        setBusy(true);

        try {
            const body = new URLSearchParams({
                duelo_id: duelId,
                carta_id: button.dataset.cardId,
                turno_esperado: currentTurn,
            });
            body.set(csrfName, csrfHash);
            const response = await fetch(playUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
                body,
            });
            const data = await response.json();

            if (!response.ok || !data.ok) {
                throw new Error(data.mensaje || 'El servidor sufrió daño crítico.');
            }

            try {
                await playTurn(data, button);
            } catch (animError) {
                updateAura('player', data.aura_retador, playerMaxAura);
                updateAura('opponent', data.aura_oponente, opponentMaxAura);
            }

            currentTurn = Number(data.turno) + 1;
            document.getElementById('turn-number').textContent = currentTurn;
            document.getElementById('wallet').textContent = formatCoins(data.auracoins_saldo).replace('+', '');
            addLog(`<strong>${michiEscapeHtml(data.carta_jugada)}</strong>: ${michiEscapeHtml(String(data.dano_realizado))} de daño. ${michiEscapeHtml(data.frase)}`);

            if (data.estado !== 'BATALLANDO') {
                showResult(data);
                return;
            }

            requestInFlight = false;
            setBusy(false);
        } catch (error) {
            addLog(michiEscapeHtml(error.message), true);
            MichiToast.fire({ icon: 'error', title: error.message });
            requestInFlight = false;
            setBusy(false);
        }
    });
});

<?php if ($duelo['estado'] !== 'BATALLANDO'): ?>
buttons.forEach((button) => button.disabled = true);
<?php endif ?>
</script>
</body>
</html>
