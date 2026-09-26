<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Duelo #<?= esc($duelo['id']) ?> · Aura Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #07080c;
            --panel: #12151d;
            --panel-2: #191d28;
            --lime: #b8ff36;
            --cyan: #44eaff;
            --pink: #ff4fc8;
            --gold: #ffd452;
            --red: #ff536d;
            --muted: #9ca4b7;
        }
        * { box-sizing: border-box; }
        html, body { width: 100%; max-width: 100%; overflow-x: hidden; }
        body { margin: 0; min-height: 100vh; color: #fff; font-family: Inter, sans-serif; background: var(--bg); }
        button { font: inherit; }
        .arena {
            width: 100%; min-width: 0; max-width: 100%; min-height: 100vh; overflow-x: hidden;
            display: grid; grid-template-rows: auto 1fr auto;
            background:
                radial-gradient(circle at 50% 6%, rgba(255,79,200,.15), transparent 28rem),
                radial-gradient(circle at 50% 68%, rgba(68,234,255,.08), transparent 34rem),
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: auto, auto, 52px 52px, 52px 52px;
        }
        .opponent, .board, .hand { width: 100%; min-width: 0; max-width: 100%; }
        .opponent { padding: 22px clamp(16px, 4vw, 54px); border-bottom: 1px solid rgba(255,255,255,.08); background: rgba(9,10,15,.72); backdrop-filter: blur(18px); }
        .opponent-inner, .board-inner, .hand-inner { width: min(1280px, 100%); min-width: 0; margin: 0 auto; }
        .opponent-inner { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 18px; }
        .avatar {
            width: 70px; height: 70px; border-radius: 18px; background: #242a38;
            border: 2px solid var(--pink); box-shadow: 0 0 30px rgba(255,79,200,.2);
        }
        .identity small { color: var(--pink); font-weight: 800; letter-spacing: .14em; }
        .identity h1 { margin: 4px 0 0; font-size: clamp(1rem, 3vw, 1.45rem); }
        .aura-panel { min-width: min(430px, 42vw); }
        .aura-label { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: .8rem; font-weight: 800; }
        .aura-label span:first-child { color: var(--muted); letter-spacing: .09em; }
        .bar { height: 15px; overflow: hidden; border-radius: 999px; background: #272b36; box-shadow: inset 0 2px 8px rgba(0,0,0,.42); }
        .bar-fill { height: 100%; border-radius: inherit; transition: width .55s cubic-bezier(.2,.8,.2,1); }
        #opponent-bar { background: linear-gradient(90deg, var(--pink), var(--red)); box-shadow: 0 0 18px rgba(255,79,200,.42); }
        #player-bar { background: linear-gradient(90deg, var(--cyan), var(--lime)); box-shadow: 0 0 18px rgba(68,234,255,.35); }
        .board { min-height: 340px; padding: 28px clamp(16px, 4vw, 54px); display: grid; align-items: center; }
        .board-inner { display: grid; grid-template-columns: minmax(0, 1fr) 250px; gap: 24px; }
        .stage {
            min-height: 300px; border: 1px solid rgba(255,255,255,.09); border-radius: 24px;
            display: grid; place-items: center; position: relative; overflow: hidden;
            background: radial-gradient(circle, rgba(184,255,54,.08), transparent 55%), rgba(14,16,23,.72);
        }
        .stage::before {
            content: ""; position: absolute; width: 260px; height: 260px; border-radius: 50%;
            border: 1px solid rgba(184,255,54,.12); box-shadow: 0 0 0 34px rgba(184,255,54,.018), 0 0 0 72px rgba(184,255,54,.012);
        }
        .versus { position: relative; text-align: center; }
        .versus strong { display: block; font: clamp(4rem, 10vw, 8.2rem)/.8 "Archivo Black", sans-serif; color: rgba(255,255,255,.06); letter-spacing: -.08em; }
        .versus span { display: inline-block; margin-top: 22px; color: var(--lime); font-weight: 800; letter-spacing: .18em; }
        .stage.hit { animation: impact .42s ease; }
        @keyframes impact { 35% { transform: scale(.985); filter: saturate(1.8); } 60% { box-shadow: inset 0 0 100px rgba(255,83,109,.18); } }
        .feed { border: 1px solid rgba(255,255,255,.09); border-radius: 20px; background: rgba(18,21,29,.9); overflow: hidden; }
        .feed-title { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,.08); color: var(--cyan); font-size: .74rem; font-weight: 800; letter-spacing: .14em; }
        #battle-log { height: 244px; overflow: auto; padding: 8px 16px 16px; scrollbar-width: thin; }
        .log-entry { padding: 11px 0; border-bottom: 1px solid rgba(255,255,255,.055); color: #c8cede; font-size: .78rem; line-height: 1.55; }
        .log-entry strong { color: var(--lime); }
        .log-entry.error { color: #ff91a3; }
        .hand { overflow: hidden; padding: 22px clamp(16px, 4vw, 54px) 30px; border-top: 1px solid rgba(255,255,255,.08); background: #0c0e14; }
        .hand-inner { overflow: hidden; }
        .hand-head { display: grid; grid-template-columns: minmax(0, 1fr) minmax(260px, 420px) auto; align-items: end; gap: 22px; margin-bottom: 18px; }
        .hand-head > * { min-width: 0; }
        .hand-title small { display: block; color: var(--cyan); letter-spacing: .14em; font-weight: 800; }
        .hand-title h2 { margin: 5px 0 0; font: 1.35rem "Archivo Black", sans-serif; }
        .player-aura .aura-label { margin-bottom: 7px; }
        .wallet { color: var(--gold); font-weight: 800; white-space: nowrap; }
        .cards { display: grid; width: 100%; grid-template-columns: repeat(6, minmax(158px, 1fr)); gap: 13px; min-width: 0; max-width: 100%; }
        .card {
            position: relative; min-height: 255px; display: flex; flex-direction: column; overflow: hidden;
            border: 1px solid rgba(255,255,255,.13); border-radius: 18px; background: linear-gradient(155deg, #202532, #11141c);
            transition: transform .2s, border-color .2s, box-shadow .2s;
        }
        .card:hover { transform: translateY(-7px); border-color: var(--cyan); box-shadow: 0 18px 36px rgba(0,0,0,.34); }
        .card-image { height: 102px; width: 100%; object-fit: contain; background: linear-gradient(135deg, rgba(68,234,255,.11), rgba(255,79,200,.1)); }
        .card-body { padding: 12px; display: flex; flex: 1; flex-direction: column; }
        .card h3 { margin: 0 0 8px; font-size: .9rem; line-height: 1.1; }
        .rarity { position: absolute; top: 9px; right: 9px; z-index: 1; padding: 5px 7px; border-radius: 8px; color: #fff; background: #596071; font-size: .62rem; font-weight: 800; }
        .rareza-rara { background: #2467d8; }
        .rareza-epica { background: #8a45d8; }
        .rareza-67 { color: #1a1300; background: linear-gradient(100deg, #ffd452, #ff944d); box-shadow: 0 0 18px rgba(255,212,82,.45); }
        .stats { display: flex; gap: 7px; margin-bottom: 8px; }
        .stat { flex: 1; padding: 6px; border-radius: 8px; text-align: center; background: rgba(255,255,255,.055); font-size: .68rem; }
        .stat b { display: block; color: var(--lime); font-size: .82rem; }
        .effect { margin: 0 0 10px; color: var(--muted); font-size: .62rem; font-weight: 700; letter-spacing: .06em; }
        .play-card {
            margin-top: auto; width: 100%; border: 0; border-radius: 10px; padding: 9px 8px; cursor: pointer;
            color: #080a0d; background: var(--lime); font-size: .7rem; font-weight: 800; transition: opacity .2s, transform .2s;
        }
        .play-card:hover { transform: scale(1.025); }
        .play-card:disabled { cursor: not-allowed; opacity: .32; transform: none; }
        .result {
            position: fixed; inset: 0; z-index: 10; display: none; place-items: center; padding: 20px;
            background: rgba(4,5,8,.86); backdrop-filter: blur(12px);
        }
        .result.show { display: grid; }
        .result-card { width: min(480px, 100%); padding: 38px; border: 1px solid rgba(255,255,255,.14); border-radius: 26px; text-align: center; background: #151922; box-shadow: 0 30px 100px rgba(0,0,0,.6); }
        .result-card small { color: var(--cyan); letter-spacing: .16em; font-weight: 800; }
        .result-card h2 { margin: 11px 0; font: clamp(2rem, 7vw, 3.5rem)/1 "Archivo Black", sans-serif; }
        .result-card p { color: #c8cede; line-height: 1.6; }
        .result-card a { display: block; margin-top: 20px; padding: 14px; border-radius: 12px; color: #080a0d; background: var(--lime); font-weight: 800; text-decoration: none; }
        @media (max-width: 980px) {
            .cards { display: flex; overflow-x: auto; overscroll-behavior-x: contain; scroll-snap-type: x mandatory; padding: 6px 2px 14px; }
            .card { flex: 0 0 180px; min-width: 0; scroll-snap-align: start; }
        }
        @media (max-width: 760px) {
            .opponent { padding-top: 15px; padding-bottom: 15px; }
            .opponent-inner { grid-template-columns: 52px 1fr; gap: 12px; }
            .avatar { width: 52px; height: 52px; border-radius: 14px; }
            .aura-panel { grid-column: 1 / -1; min-width: 0; }
            .board { padding-top: 18px; padding-bottom: 18px; }
            .board-inner { grid-template-columns: 1fr; }
            .stage { min-height: 170px; }
            .feed { display: none; }
            .hand-head { grid-template-columns: 1fr auto; gap: 12px; }
            .player-aura { grid-column: 1 / -1; grid-row: 2; }
            .hand { padding-top: 17px; }
        }
        @media (max-width: 480px) {
            .hand-head { grid-template-columns: minmax(0, 1fr); align-items: stretch; }
            .player-aura { grid-column: 1; grid-row: auto; }
            .wallet { white-space: normal; }
        }
    </style>
</head>
<body>
<main class="arena">
    <section class="opponent">
        <div class="opponent-inner">
            <img class="avatar" src="https://api.dicebear.com/9.x/bottts-neutral/svg?seed=TheRizzlerBot&backgroundColor=ff4fc8" alt="">
            <div class="identity">
                <small>RIVAL MEME</small>
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
                    <div class="log-entry">The Rizzler Bot entró a la arena. Elige una carta para comenzar.</div>
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
                    <article class="card">
                        <span class="rarity <?= esc(badge_rareza((int) $carta['rareza_nivel'])) ?>">LVL <?= esc($carta['rareza_nivel']) ?></span>
                        <img class="card-image" src="<?= esc($carta['imagen_url']) ?>" alt="" draggable="false">
                        <div class="card-body">
                            <h3><?= esc($carta['nombre']) ?></h3>
                            <div class="stats">
                                <span class="stat"><b><?= esc($carta['ataque_aura']) ?></b>ATAQUE</span>
                                <span class="stat"><b><?= esc($carta['defensa_cringe']) ?></b>ANTI-CRINGE</span>
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

<div id="result" class="result" role="dialog" aria-modal="true">
    <div class="result-card">
        <small id="result-label">DUELO TERMINADO</small>
        <h2 id="result-title"></h2>
        <p id="result-copy"></p>
        <strong id="result-coins"></strong>
        <a href="<?= site_url('arena') ?>">VOLVER A LA ARENA</a>
    </div>
</div>

<script>
const duelId = <?= (int) $duelo['id'] ?>;
const playUrl = <?= json_encode(site_url('arena/jugar'), JSON_UNESCAPED_SLASHES) ?>;
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
    document.getElementById('result-title').textContent = won ? 'W ABSOLUTA' : 'CRINGE TOTAL';
    document.getElementById('result-title').style.color = won ? 'var(--lime)' : 'var(--red)';
    document.getElementById('result-copy').textContent = data.frase_resultado;
    document.getElementById('result-coins').textContent = won
        ? `Pozo cobrado: ${formatCoins(data.auracoins_movimiento)}`
        : `Apuesta perdida: ${formatCoins(data.auracoins_movimiento)}`;
    document.getElementById('result').classList.add('show');
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
            addLog(`<strong>${data.carta_jugada}</strong>: ${data.dano_realizado} de daño. ${data.frase}`);

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
            addLog(error.message, true);
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
