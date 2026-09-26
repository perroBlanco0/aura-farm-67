<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#e9f4fd;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#e9f4fd;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="max-width:480px;width:100%;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 18px 50px rgba(28,64,128,.18);">
                <tr>
                    <td bgcolor="#4a90d9" style="padding:26px 30px;background:#4a90d9;background:linear-gradient(135deg,#4a90d9,#37bfa9);">
                        <table role="presentation" cellpadding="0" cellspacing="0"><tr>
                            <td style="width:40px;height:40px;background:#b8ff36;border-radius:50%;border:3px solid #ffffff;color:#223010;font-weight:bold;font-size:19px;text-align:center;line-height:36px;">M</td>
                            <td style="padding-left:11px;color:#ffffff;font-weight:bold;letter-spacing:2px;font-size:14px;">MICHI ARENA</td>
                        </tr></table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px 10px;">
                        <h1 style="margin:0 0 10px;color:#22304f;font-size:26px;line-height:1.15;">¡Bienvenido a la arena, <?= esc($username) ?>! 🐾</h1>
                        <p style="margin:0;color:#7381a3;font-size:14px;line-height:1.65;">
                            Tu cuenta ya está lista: <strong style="color:#22304f;">1000 de Aura</strong>,
                            <strong style="color:#22304f;">1500 $MICHI</strong> y tus 6 michis base — el resto se atrapa en el mapa
                            te esperan en el mapa. Y si te sientes valiente… El Michi Supremo anda suelto.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:22px 30px 8px;">
                        <a href="<?= esc($enlace) ?>" style="display:inline-block;background:#223010;background:linear-gradient(100deg,#b8ff36,#eaff78);background-color:#b8ff36;color:#223010;border-radius:16px;padding:15px 38px;font-size:16px;font-weight:bold;text-decoration:none;box-shadow:0 10px 30px rgba(140,200,40,.35);">ENTRAR AL MAPA →</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 30px 30px;">
                        <p style="margin:0;color:#7381a3;font-size:12px;line-height:1.65;">
                            Si el botón no funciona, copia este enlace:<br>
                            <a href="<?= esc($enlace) ?>" style="color:#4a90d9;word-break:break-all;"><?= esc($enlace) ?></a>
                        </p>
                        <p style="margin:16px 0 0;color:#aab6cd;font-size:11px;">
                            Michi Arena · duelos de gatos virales · por @perroBlanco0
                        </p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
