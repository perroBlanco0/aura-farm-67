<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mantenedor de michis · Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #08090d; --lime: #b8ff36; --cyan: #44eaff; --pink: #ff4fc8;
            --gold: #ffd452; --red: #ff536d; --muted: #9ca4b7;
        }
        * { box-sizing: border-box; }
        html, body { width: 100%; max-width: 100%; overflow-x: hidden; }
        body {
            margin: 0; min-height: 100vh; color: #fff; font-family: Inter, sans-serif;
            background:
                radial-gradient(circle at 15% 15%, rgba(68,234,255,.12), transparent 30rem),
                radial-gradient(circle at 85% 10%, rgba(255,79,200,.13), transparent 26rem),
                var(--bg);
        }
        .shell { width: min(1120px, calc(100% - 32px)); margin: 0 auto; padding: 30px 0 60px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 34px; }
        .brand { display: flex; align-items: center; gap: 12px; font-weight: 800; letter-spacing: .08em; }
        .brand-mark {
            width: 42px; height: 42px; display: grid; place-items: center; border-radius: 12px;
            color: #08090d; background: var(--lime); font-family: "Archivo Black", sans-serif;
            box-shadow: 0 0 28px rgba(184,255,54,.35);
        }
        .nav { display: flex; gap: 10px; flex-wrap: wrap; }
        .nav a {
            padding: 10px 15px; border-radius: 999px; font-size: .8rem; font-weight: 800;
            color: #fff; text-decoration: none; background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
        }
        .nav a:hover { border-color: var(--cyan); }
        h1 { margin: 0 0 6px; font: clamp(1.8rem, 5vw, 3rem)/1 "Archivo Black", sans-serif; letter-spacing: -.03em; }
        h1 span { color: var(--pink); }
        .sub { color: var(--muted); font-size: .85rem; margin-bottom: 24px; }
        .error, .ok { padding: 12px 14px; border-radius: 12px; font-size: .84rem; margin-bottom: 16px; }
        .error { background: rgba(255,79,103,.13); border: 1px solid rgba(255,79,103,.3); color: #ff9bac; }
        .ok { background: rgba(184,255,54,.1); border: 1px solid rgba(184,255,54,.3); color: var(--lime); }
        .panel { border: 1px solid rgba(255,255,255,.1); border-radius: 22px; overflow: hidden; background: rgba(18,21,29,.85); }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 720px; }
        th, td { padding: 13px 14px; text-align: left; font-size: .82rem; border-bottom: 1px solid rgba(255,255,255,.06); }
        th { color: var(--cyan); letter-spacing: .1em; font-size: .68rem; text-transform: uppercase; background: rgba(255,255,255,.03); }
        tr.eliminado td { opacity: .42; }
        .pill { display: inline-block; padding: 4px 9px; border-radius: 999px; font-size: .66rem; font-weight: 800; }
        .pill-admin { background: rgba(255,212,82,.15); color: var(--gold); }
        .pill-user { background: rgba(255,255,255,.07); color: var(--muted); }
        .pill-out { background: rgba(255,83,109,.15); color: var(--red); }
        .acciones { display: flex; gap: 8px; }
        .acciones a, .acciones button {
            padding: 7px 12px; border-radius: 9px; font: 800 .72rem Inter, sans-serif;
            border: 1px solid rgba(255,255,255,.14); background: transparent; color: #fff;
            cursor: pointer; text-decoration: none; white-space: nowrap;
        }
        .acciones a:hover { border-color: var(--cyan); color: var(--cyan); }
        .acciones button:hover { border-color: var(--red); color: var(--red); }
        .email-cell { color: var(--muted); font-size: .76rem; }
        @media (max-width: 640px) {
            .shell { width: calc(100% - 20px); padding-top: 18px; }
            th, td { padding: 10px 9px; font-size: .78rem; }
        }
    </style>
</head>
<body>
<main class="shell">
    <header class="topbar">
        <div class="brand"><span class="brand-mark">M</span><span>MICHI ARENA</span></div>
        <nav class="nav">
            <a href="<?= site_url('arena') ?>">Arena</a>
            <a href="<?= site_url('salir') ?>">Salir</a>
        </nav>
    </header>

    <h1>MANTENEDOR DE <span>MICHIS</span></h1>
    <p class="sub">Listar, editar y eliminar jugadores. El borrado es lógico: el michi desaparece pero sus duelos quedan en el historial.</p>

    <?php if (! empty($error)): ?><p class="error"><?= esc($error) ?></p><?php endif ?>
    <?php if (! empty($ok)): ?><p class="ok"><?= esc($ok) ?></p><?php endif ?>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Usuario</th><th>Correo</th><th>Rol</th>
                        <th>Aura</th><th>$AURA</th><th>W/L</th><th>Estado</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jugadores as $j): ?>
                    <tr class="<?= $j['eliminado_en'] !== null ? 'eliminado' : '' ?>">
                        <td><?= esc($j['id']) ?></td>
                        <td><strong><?= esc($j['username']) ?></strong></td>
                        <td class="email-cell"><?= esc($j['email'] ?? '—') ?></td>
                        <td><span class="pill <?= (int) $j['es_admin'] === 1 ? 'pill-admin' : 'pill-user' ?>"><?= (int) $j['es_admin'] === 1 ? 'ADMIN' : 'MICHY' ?></span></td>
                        <td><?= esc($j['aura_actual']) ?>/<?= esc($j['aura_max']) ?></td>
                        <td><?= esc(formatear_auracoins((int) $j['auracoins'])) ?></td>
                        <td><?= esc($j['victorias']) ?>/<?= esc($j['derrotas']) ?></td>
                        <td>
                            <?php if ($j['eliminado_en'] !== null): ?>
                                <span class="pill pill-out">MIMIDO</span>
                            <?php else: ?>
                                <span class="pill pill-user">ACTIVO</span>
                            <?php endif ?>
                        </td>
                        <td>
                            <?php if ($j['eliminado_en'] === null): ?>
                            <div class="acciones">
                                <a href="<?= site_url('mantenedor/editar/' . $j['id']) ?>">Editar</a>
                                <form action="<?= site_url('mantenedor/eliminar/' . $j['id']) ?>" method="post" onsubmit="return confirm('¿Enviar a <?= esc($j['username']) ?> a mimir para siempre?')">
                                    <?= csrf_field() ?>
                                    <button type="submit">Eliminar</button>
                                </form>
                            </div>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
