<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Nuevo contacto — Talent Sphere</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f3f8; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f3f8; padding: 48px 16px;">
        <tr>
            <td align="center">

                <table width="560" cellpadding="0" cellspacing="0" border="0"
                       style="background:#ffffff; border-radius:12px;
                              box-shadow: 0 2px 4px rgba(0,0,0,0.04), 0 8px 24px rgba(79,70,229,0.10);
                              overflow:hidden; max-width:560px; width:100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#4F46E5 0%,#6366f1 100%); padding: 24px 40px;">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td style="padding-right: 12px; vertical-align: middle; width: 48px;">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="background: rgba(255,255,255,0.18); border-radius: 8px; width:36px; height:36px; text-align:center; vertical-align:middle; font-size: 13px; color:#fff; font-weight:700; letter-spacing:-0.5px;">
                                                    TS
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="color:#ffffff; font-size:16px; font-weight:700; letter-spacing:0.2px; display:block;">
                                            Talent Sphere
                                        </span>
                                        <span style="color:rgba(255,255,255,0.75); font-size:11.5px; font-weight:500; display:block; margin-top:2px;">
                                            Formulario de contacto web
                                        </span>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <span style="display:inline-block; background:rgba(255,255,255,0.15); color:#fff; font-size:10px; font-weight:600; letter-spacing:0.6px; text-transform:uppercase; padding:6px 10px; border-radius:20px;">
                                            Nuevo
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="height:3px; background: linear-gradient(90deg, #4F46E5 0%, #818cf8 100%); font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 36px 40px 28px;">

                            <h2 style="margin: 0 0 6px; color:#1a1a2e; font-size:21px; font-weight:700; letter-spacing:-0.3px; line-height:1.3;">
                                Alguien escribió desde la landing
                            </h2>
                            <p style="margin: 0 0 28px; color:#6b7280; font-size:13px; font-weight:400; letter-spacing:0.3px;">
                                Recibido el {{ now()->timezone('America/Bogota')->translatedFormat('d \d\e F \d\e Y') }} a las {{ now()->timezone('America/Bogota')->format('g:i A') }} (Bogotá)
                            </p>

                            {{-- Asunto destacado --}}
                            <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="background:linear-gradient(135deg,#eef2ff 0%,#f5f3ff 100%); border-radius:10px; border:1px solid #e0e7ff; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0 0 4px; color:#6366f1; font-size:11px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase;">
                                            Asunto
                                        </p>
                                        <p style="margin:0; color:#1e1b4b; font-size:16px; font-weight:600; line-height:1.4;">
                                            {{ $asunto }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Datos del remitente --}}
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:20px;">
                                <tr>
                                    <td width="50%" style="padding-right:8px; vertical-align:top;">
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                               style="background:#f9fafb; border-radius:10px; border:1px solid #e5e7eb;">
                                            <tr>
                                                <td style="padding:14px 16px;">
                                                    <p style="margin:0 0 4px; color:#9ca3af; font-size:10px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase;">
                                                        Nombre
                                                    </p>
                                                    <p style="margin:0; color:#111827; font-size:14px; font-weight:600;">
                                                        {{ $nombre }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding-left:8px; vertical-align:top;">
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                               style="background:#f9fafb; border-radius:10px; border:1px solid #e5e7eb;">
                                            <tr>
                                                <td style="padding:14px 16px;">
                                                    <p style="margin:0 0 4px; color:#9ca3af; font-size:10px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase;">
                                                        Correo
                                                    </p>
                                                    <p style="margin:0;">
                                                        <a href="mailto:{{ $email }}" style="color:#4F46E5; font-size:14px; font-weight:600; text-decoration:none; word-break:break-all;">
                                                            {{ $email }}
                                                        </a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Mensaje --}}
                            <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="background:#ffffff; border-radius:10px; border:1px solid #e5e7eb; margin-bottom:24px;">
                                <tr>
                                    <td style="padding:0;">
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tr>
                                                <td style="background:#f3f4f6; padding:10px 16px; border-bottom:1px solid #e5e7eb;">
                                                    <p style="margin:0; color:#374151; font-size:11px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase;">
                                                        Mensaje
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:18px 20px;">
                                                    <p style="margin:0; color:#374151; font-size:15px; line-height:1.75;">
                                                        {!! $mensaje !!}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA responder --}}
                            <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 20px;">
                                <tr>
                                    <td align="center" style="border-radius:9px; background:#4F46E5;
                                        box-shadow: 0 4px 14px rgba(79,70,229,0.35);">
                                        <a href="mailto:{{ $email }}?subject=Re: {{ rawurlencode($asunto) }}"
                                           style="display:inline-block; background:#4F46E5; color:#ffffff;
                                                  padding:13px 32px; border-radius:9px; font-size:14px;
                                                  font-weight:600; text-decoration:none; letter-spacing:0.3px;
                                                  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                            Responder a {{ $nombre }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="background:#eff6ff; border-radius:8px; border-left:3px solid #4F46E5;">
                                <tr>
                                    <td style="padding:12px 16px;">
                                        <p style="margin:0; color:#1e40af; font-size:13px; line-height:1.5;">
                                            Al responder, el destinatario verá tu correo como remitente y podrá contestar directamente a <strong>{{ $email }}</strong>.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f9f9fb; padding:18px 40px; border-top:1px solid #e5e7eb;">
                            <p style="color:#9ca3af; font-size:12px; margin:0 0 6px; text-align:center; line-height:1.5;">
                                Este mensaje llegó desde el formulario de contacto de
                                <a href="https://talentsphere.cloud" style="color:#4F46E5; text-decoration:none; font-weight:600;">talentsphere.cloud</a>
                            </p>
                            <p style="color:#c0c8d8; font-size:11px; margin:0; text-align:center; font-family: 'Courier New', monospace; letter-spacing:0.3px;">
                                Talent Sphere — correo automático del sistema RRHH
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
