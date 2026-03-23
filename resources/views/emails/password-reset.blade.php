<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Recuperación de contraseña</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f3f8; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f3f8; padding: 48px 16px;">
        <tr>
            <!-- Encabezado accesible para cumplir la regla de tablas de Sonar -->
            <th scope="col" style="height:0; padding:0; font-size:0; line-height:0;" aria-hidden="true"></th>
        </tr>
        <tr>
            <td align="center">

                <!-- Contenedor principal -->
                <table width="520" cellpadding="0" cellspacing="0" border="0"
                       style="background:#ffffff; border-radius:12px;
                              box-shadow: 0 2px 4px rgba(0,0,0,0.04), 0 8px 24px rgba(79,70,229,0.10);
                              overflow:hidden; max-width:520px; width:100%;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#4F46E5; padding: 24px 40px;">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding-right: 10px; vertical-align: middle;">
                                        <!-- Icono candado inline SVG como imagen base64 fallback: usamos tabla con borde -->
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="background: rgba(255,255,255,0.18); border-radius: 8px; width:34px; height:34px; text-align:center; vertical-align:middle; font-size: 16px; color:#fff; font-weight:bold; letter-spacing:0;">
                                                    RH
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="color:#ffffff; font-size:15px; font-weight:600; letter-spacing:0.2px;">
                                            Sistema RRHH
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Divider accent -->
                    <tr>
                        <td style="height:3px; background: linear-gradient(90deg, #4F46E5 0%, #818cf8 100%); font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 40px 32px;">

                            <!-- Titulo -->
                            <h2 style="margin: 0 0 6px; color:#1a1a2e; font-size:21px; font-weight:700; letter-spacing:-0.3px; line-height:1.3;">
                                Recuperacion de contrasena
                            </h2>
                            <p style="margin: 0 0 28px; color:#6b7280; font-size:13px; font-weight:400; letter-spacing:0.3px; text-transform:uppercase;">
                                Solicitud de restablecimiento
                            </p>

                            <!-- Mensaje -->
                            <p style="color:#374151; font-size:15px; line-height:1.7; margin: 0 0 28px;">
                                Hola <strong style="color:#1a1a2e;">{{ $nombre }}</strong>, recibimos una solicitud para restablecer la contrasena de tu cuenta. Si fuiste tu, haz clic en el boton para continuar.
                            </p>

                            <!-- Boton CTA -->
                            <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 32px;">
                                <tr>
                                    <td align="center" style="border-radius:9px; background:#4F46E5;
                                        box-shadow: 0 4px 14px rgba(79,70,229,0.35);">
                                        <a href="{{ $resetUrl }}"
                                           style="display:inline-block; background:#4F46E5; color:#ffffff;
                                                  padding:14px 40px; border-radius:9px; font-size:14.5px;
                                                  font-weight:600; text-decoration:none; letter-spacing:0.3px;
                                                  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                            Restablecer contrasena
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Separador -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:24px;">
                                <tr>
                                    <td style="border-top:1px solid #e5e7eb; height:1px; font-size:0; line-height:0;"></td>
                                </tr>
                            </table>

                            <!-- Aviso expiracion -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="background:#fffbeb; border-radius:8px; border-left:3px solid #d97706; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:12px 16px;">
                                        <p style="margin:0; color:#92400e; font-size:13px; line-height:1.5;">
                                            Este enlace expira en <strong>30 minutos</strong> desde que fue generado.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Aviso seguridad -->
                            <p style="color:#9ca3af; font-size:13px; line-height:1.6; margin:0;">
                                Si no solicitaste este cambio, puedes ignorar este correo con seguridad. Tu contrasena no sera modificada.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9f9fb; padding:18px 40px; border-top:1px solid #e5e7eb;">
                            <p style="color:#c0c8d8; font-size:11.5px; margin:0; text-align:center; font-family: 'Courier New', monospace; letter-spacing:0.3px;">
                                Correo automatico &mdash; no respondas este mensaje
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Contenedor -->

            </td>
        </tr>
    </table>

</body>
</html>