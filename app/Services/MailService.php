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
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Recuperación de contraseña — Sistema RRHH';

            // Renderiza el Blade template pasándole las variables
            $this->mailer->Body    = view('emails.password-reset', [
                'nombre'   => $toName,
                'resetUrl' => $resetUrl,
            ])->render();

            $this->mailer->AltBody =
                "Hola $toName, usa este enlace para restablecer tu contraseña: " .
                "$resetUrl (Expira en 30 minutos). " .
                "Si no solicitaste este cambio, ignora este correo.";

            $this->mailer->send();

        } catch (MailerException $e) {
            throw new \RuntimeException('Error al enviar el correo: ' . $this->mailer->ErrorInfo);
        }
    }
}