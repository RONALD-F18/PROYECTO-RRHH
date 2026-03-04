<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

/**
 * CAPA: Service (Application Service Layer)
 *
 * MailService tiene UNA sola responsabilidad: enviar correos.
 * Encapsula toda la configuración y uso de PHPMailer.
 *
 * Principio aplicado: Single Responsibility Principle (SRP).
 *
 * Al inyectarlo en otros servicios, el resto de la aplicación
 * no necesita saber CÓMO se envían los correos, solo llama sendPasswordReset().
 *
 * Configuración SMTP: se lee desde config/mail.php que a su vez
 * lee el .env — nunca hardcodeamos credenciales aquí.
 */
class MailService
{
    protected PHPMailer $mailer;

    public function __construct()
    {
        // true = activar excepciones (en lugar de retornar false silenciosamente)
        $this->mailer = new PHPMailer(true);

        // ── Configuración del servidor SMTP ──────────────────────────────────

        // Usar transporte SMTP (en vez del mail() de PHP)
        $this->mailer->isSMTP();

        // Host del servidor (ej: smtp.gmail.com)
        $this->mailer->Host = config('mail.mailers.smtp.host');

        // Activar autenticación con usuario y contraseña
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = config('mail.mailers.smtp.username');
        $this->mailer->Password = config('mail.mailers.smtp.password');

        // Tipo de cifrado: STARTTLS (puerto 587) o SMTPS (puerto 465)
        $encryption = config('mail.mailers.smtp.encryption');
        $this->mailer->SMTPSecure = ($encryption === 'ssl')
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;

        // Puerto SMTP (587 para TLS, 465 para SSL)
        $this->mailer->Port = config('mail.mailers.smtp.port');

        // Charset UTF-8 para soportar tildes y caracteres especiales
        $this->mailer->CharSet = 'UTF-8';

        // Remitente del correo (el "De:" que ve el destinatario)
        $this->mailer->setFrom(
            config('mail.from.address'),
            config('mail.from.name')
        );
    }

    /**
     * Envía el correo de recuperación de contraseña.
     *
     * @param string $toEmail  Email del destinatario
     * @param string $toName   Nombre del destinatario (para personalizar el correo)
     * @param string $resetUrl URL completa con el token para resetear la contraseña
     *
     * @throws \RuntimeException Si el envío falla
     */
    public function sendPasswordReset(string $toEmail, string $toName, string $resetUrl): void
    {
        try {
            // Limpiar destinatarios anteriores (por si el objeto se reutiliza)
            $this->mailer->clearAddresses();

            // Agregar destinatario
            $this->mailer->addAddress($toEmail, $toName);

            // Activar modo HTML
            $this->mailer->isHTML(true);

            // Asunto del correo
            $this->mailer->Subject = 'Recuperación de contraseña — Sistema RRHH';

            // Cuerpo HTML (visual, para clientes que soportan HTML)
            $this->mailer->Body = $this->buildResetEmailHtml($toName, $resetUrl);

            // Cuerpo de texto plano (fallback para clientes que no soportan HTML)
            $this->mailer->AltBody =
                "Hola $toName, usa este enlace para restablecer tu contraseña: " .
                "$resetUrl (Expira en 30 minutos). " .
                "Si no solicitaste este cambio, ignora este correo.";

            $this->mailer->send();

        } catch (MailerException $e) {
            // Relanzamos como RuntimeException genérica para no exponer
            // detalles de PHPMailer fuera de este servicio
            throw new \RuntimeException(
                'Error al enviar el correo: ' . $this->mailer->ErrorInfo
            );
        }
    }

    /**
     * Genera el HTML del correo de recuperación.
     * Separado en su propio método para mantener sendPasswordReset() limpio.
     *
     * @param string $nombre Nombre del usuario
     * @param string $url    URL de reset
     * @return string HTML del correo
     */
    private function buildResetEmailHtml(string $nombre, string $url): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
                <tr>
                    <td align="center">
                        <table width="520" cellpadding="0" cellspacing="0"
                               style="background:#ffffff; border-radius:10px;
                                      box-shadow:0 4px 12px rgba(0,0,0,0.08); overflow:hidden;">

                            <!-- Header -->
                            <tr>
                                <td style="background:#4F46E5; padding:28px 40px;">
                                    <h1 style="margin:0; color:#ffffff; font-size:20px; font-weight:600;">
                                        🔐 Sistema RRHH
                                    </h1>
                                </td>
                            </tr>

                            <!-- Body -->
                            <tr>
                                <td style="padding:36px 40px;">
                                    <h2 style="color:#1a1a2e; font-size:22px; margin:0 0 16px;">
                                        Recuperación de contraseña
                                    </h2>
                                    <p style="color:#555; font-size:15px; line-height:1.6; margin:0 0 24px;">
                                        Hola <strong>{$nombre}</strong>, recibimos una solicitud para
                                        restablecer la contraseña de tu cuenta.
                                        Haz clic en el botón para continuar:
                                    </p>

                                    <!-- Botón CTA -->
                                    <div style="text-align:center; margin: 32px 0;">
                                        <a href="{$url}"
                                           style="display:inline-block; background:#4F46E5; color:#ffffff;
                                                  padding:14px 36px; border-radius:8px; font-size:15px;
                                                  font-weight:bold; text-decoration:none;
                                                  letter-spacing:0.3px;">
                                            Restablecer contraseña
                                        </a>
                                    </div>

                                    <p style="color:#888; font-size:13px; line-height:1.6; margin:0 0 8px;">
                                        ⚠️ Este enlace expira en <strong>30 minutos</strong>.
                                    </p>
                                    <p style="color:#888; font-size:13px; line-height:1.6; margin:0;">
                                        Si no solicitaste este cambio, puedes ignorar este correo
                                        con seguridad — tu contraseña no cambiará.
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style="background:#f9f9fb; padding:20px 40px; border-top:1px solid #eee;">
                                    <p style="color:#bbb; font-size:12px; margin:0; text-align:center;">
                                        Este es un correo automático, no respondas a este mensaje.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }
}