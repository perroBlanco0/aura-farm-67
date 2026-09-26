<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#08090d;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#08090d;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="max-width:480px;width:100%;background:linear-gradient(150deg,#191d28,#0c0e14);border:1px solid #262b3a;border-radius:22px;overflow:hidden;">
                <tr>
                    <td style="padding:26px 30px 0;">
                        <table role="presentation" cellpadding="0" cellspacing="0"><tr>
                            <td style="width:36px;height:36px;background:#b8ff36;border-radius:10px;color:#08090d;font-weight:bold;font-size:18px;text-align:center;line-height:36px;">M</td>
                            <td style="padding-left:10px;color:#ffffff;font-weight:bold;letter-spacing:2px;font-size:13px;">MICHI ARENA</td>
                        </tr></table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:26px 30px 10px;">
                        <h1 style="margin:0 0 10px;color:#ffffff;font-size:26px;line-height:1.1;">Tu código de recuperación 🐾</h1>
                        <p style="margin:0;color:#9ca4b7;font-size:14px;line-height:1.6;">
                            Hola <strong style="color:#ffffff;"><?= esc($username) ?></strong>, alguien pidió
                            restablecer la clave de tu cuenta en Michi Arena. Si fuiste tú, usa este código:
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:18px 30px 8px;">
                        <div style="display:inline-block;background:#0d1017;border:2px solid #44eaff;border-radius:16px;padding:16px 34px;color:#b8ff36;font-size:34px;font-weight:bold;letter-spacing:12px;"><?= esc($codigo) ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 30px 30px;">
                        <p style="margin:0;color:#9ca4b7;font-size:12px;line-height:1.6;">
                            El código expira en 15 minutos. Si no pediste esto, ignora el correo:
                            tu clave sigue igual y ningún michi fue molestado.
                        </p>
                        <p style="margin:16px 0 0;color:#596071;font-size:11px;">
                            Michi Arena · duelos de gatos virales · por @perroBlanco0
                        </p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
