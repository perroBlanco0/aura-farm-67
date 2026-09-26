<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mantenedor de michis · Michi Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/michi.css">
    <style>
        .shell { width: min(1120px, calc(100% - 32px)); margin: 0 auto; padding: 30px 0 60px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 30px; }
        h1 { margin: 0 0 6px; color: #fff; font-size: clamp(1.8rem, 5vw, 2.8rem); font-weight: 700; letter-spacing: -.01em; text-shadow: 0 3px 0 rgba(30, 60, 120, .3); }
        h1 span { color: var(--lime); }
        .sub { color: #eaf6ff; font-weight: 700; font-size: .85rem; margin-bottom: 24px; text-shadow: 0 2px 8px rgba(20, 50, 100, .4); }

        .panel { overflow: hidden; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 720px; }
        th, td { padding: 13px 14px; text-align: left; font-size: .83rem; font-weight: 700; color: var(--ink-soft); border-bottom: 1px solid var(--line); }
        th { color: var(--sky-1); letter-spacing: .1em; font-size: .66rem; font-weight: 900; text-transform: uppercase; background: #f4f8ff; }
        tbody tr:last-child td { border-bottom: 0; }
        tr.eliminado td { opacity: .45; }
        .cell-user { display: flex; align-items: center; gap: 10px; }
        .cell-avatar {
            width: 38px; height: 38px; flex: 0 0 38px; border-radius: 50%;
            display: grid; place-items: center; font-size: 1.1rem;
            background: linear-gradient(140deg, #d8f2ff, #e6f9d8);
            border: 2px solid var(--line);
        }
        .email-cell { color: var(--muted); font-size: .78rem; }
        .acciones { display: flex; gap: 8px; }
        .acciones form { display: contents; }

        /* móvil: tabla -> tarjetas apiladas estilo PoGO */
        @media (max-width: 760px) {
            table, thead, tbody, tr, td { display: block; min-width: 0; }
            thead { display: none; }
            tbody { display: grid; gap: 12px; padding: 14px; }
            tbody tr {
                border: 2px solid var(--line); border-radius: 20px;
                background: #fff; box-shadow: var(--shadow-sm); padding: 14px;
            }
            tbody td { border: 0; padding: 5px 0; font-size: .85rem; }
            tbody td::before {
                content: attr(data-label);
                display: inline-block; min-width: 86px;
                color: var(--sky-1); font-size: .64rem; font-weight: 900;
                letter-spacing: .08em; text-transform: uppercase;
                vertical-align: middle;
            }
            td.cell-usuario { padding-bottom: 10px; }
            td.cell-usuario::before { display: block; margin-bottom: 6px; }
            td.cell-acciones { padding-top: 10px; }
            td.cell-acciones::before { display: none; }
            .acciones { flex-wrap: wrap; }
            .acciones a, .acciones button { flex: 1; }
        }
        @media (max-width: 640px) {
            .shell { width: calc(100% - 20px); padding-top: 18px; }
        }
    </style>
</head>
<body>
<main class="shell">
    <header class="topbar">
        <div class="brand"><span class="brand-mark">M</span><span>MICHI ARENA</span></div>
        <nav class="nav">
            <a href="<?= site_url('arena') ?>">Arena</a>
            <a href="<?= site_url('mapa') ?>">Mapa</a>
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
                        <th>Aura perm.</th><th>$MICHI</th><th>W/L</th><th>Estado</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jugadores as $j): ?>
                    <tr class="<?= $j['eliminado_en'] !== null ? 'eliminado' : '' ?>">
                        <td data-label="#">#<?= esc($j['id']) ?></td>
                        <td data-label="Usuario" class="cell-usuario">
                            <span class="cell-user"><span class="cell-avatar">🐱</span><strong><?= esc($j['username']) ?></strong></span>
                        </td>
                        <td data-label="Correo" class="email-cell"><?= esc($j['email'] ?? '—') ?></td>
                        <td data-label="Rol"><span class="pill <?= (int) $j['es_admin'] === 1 ? 'pill-admin' : 'pill-user' ?>"><?= (int) $j['es_admin'] === 1 ? 'ADMIN' : 'MICHY' ?></span></td>
                        <td data-label="Aura perm."><?= esc($j['aura_actual']) ?>/<?= esc($j['aura_max']) ?></td>
                        <td data-label="$MICHI"><?= esc(str_replace('$AURA', '$MICHI', formatear_auracoins((int) $j['auracoins']))) ?></td>
                        <td data-label="W/L"><?= esc($j['victorias']) ?>/<?= esc($j['derrotas']) ?></td>
                        <td data-label="Estado">
                            <?php if ($j['eliminado_en'] !== null): ?>
                                <span class="pill pill-out">ELIMINADO</span>
                            <?php else: ?>
                                <span class="pill pill-user">ACTIVO</span>
                            <?php endif ?>
                        </td>
                        <td class="cell-acciones">
                            <?php if ($j['eliminado_en'] === null): ?>
                            <div class="acciones">
                                <a class="btn btn-secondary btn-sm" href="<?= site_url('mantenedor/editar/' . $j['id']) ?>">Editar</a>
                                <form class="form-eliminar" data-username="<?= esc($j['username']) ?>" action="<?= site_url('mantenedor/eliminar/' . $j['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/js/michi.js"></script>
<script>
<?php if (! empty($error)): ?>
MichiToast.fire({ icon: 'error', title: <?= json_encode((string) $error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> });
<?php endif ?>
<?php if (! empty($ok)): ?>
MichiToast.fire({ icon: 'success', title: <?= json_encode((string) $ok, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> });
<?php endif ?>

document.querySelectorAll('.form-eliminar').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const result = await michiSwal({
            icon: 'warning',
            title: '¿A mimir para siempre?',
            text: `¿Enviar a ${form.dataset.username} a mimir? El borrado es lógico: sus duelos quedan en el historial.`,
            showCancelButton: true,
            confirmButtonText: 'SÍ, A MIMIR',
            cancelButtonText: 'CANCELAR',
        });
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
</body>
</html>
