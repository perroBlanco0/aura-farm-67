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
let requestInFlight = false;
let currentTurn = <?= (int) $duelo['turno'] ?>;
let lastPlayStartedAt = 0;

const formatCoins = (amount) => {
    const sign = amount < 0 ? '-' : amount > 0 ? '+' : '';
    return `💰 ${sign}${Math.abs(amount).toLocaleString('es-CL')} $AURA`;
};

const updateAura = (side, value, max) => {
    document.getElementById(`${side}-aura`).textContent = value;
    document.getElementById(`${side}-bar`).style.width = `${Math.max(0, Math.min(100, value / max * 100))}%`;
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

            updateAura('player', data.aura_retador, playerMaxAura);
            updateAura('opponent', data.aura_oponente, opponentMaxAura);
            currentTurn = Number(data.turno) + 1;
            document.getElementById('turn-number').textContent = currentTurn;
            document.getElementById('wallet').textContent = formatCoins(data.auracoins_saldo).replace('+', '');
            addLog(`<strong>${michiEscapeHtml(data.carta_jugada)}</strong>: ${michiEscapeHtml(String(data.dano_realizado))} de daño. ${michiEscapeHtml(data.frase)}`);

            stage.classList.remove('hit');
            void stage.offsetWidth;
            stage.classList.add('hit');

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
