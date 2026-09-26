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
                        <h1 style="margin:0 0 10px;color:#22304f;font-size:26px;line-height:1.15;">Tu código de recuperación 🐾</h1>
                        <p style="margin:0;color:#7381a3;font-size:14px;line-height:1.65;">
                            Hola <strong style="color:#22304f;"><?= esc($username) ?></strong>, alguien pidió
                            restablecer la clave de tu cuenta en Michi Arena. Si fuiste tú, usa este código:
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:18px 30px 8px;">
                        <div style="display:inline-block;background:#f2f9ff;border:3px solid #41b4d6;border-radius:18px;padding:16px 34px;color:#223010;font-size:34px;font-weight:bold;letter-spacing:12px;"><?= esc($codigo) ?></div>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:14px 30px 6px;">
                        <span style="display:inline-block;background:#ecffd6;border:2px solid #c8f59a;color:#4d7a00;border-radius:999px;padding:7px 16px;font-size:11px;font-weight:bold;letter-spacing:1px;">EXPIRA EN 15 MINUTOS</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 30px 30px;">
                        <p style="margin:0;color:#7381a3;font-size:12px;line-height:1.65;">
                            Si no pediste esto, ignora el correo:
                            tu clave sigue igual y ningún michi fue molestado.
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
