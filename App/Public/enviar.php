<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP (MailHog)
    $mail->isSMTP();
    $mail->Host = getenv('MAIL_HOST') ?: 'mailhog';
    $mail->Port = getenv('MAIL_PORT') ?: 1025;
    $mail->SMTPAuth = false;
    $mail->SMTPDebug = 2;
    // Remitente y destinatario
    $mail->setFrom('nomegusta@comocazalape.rra', 'Mangurrinos');
    $mail->addAddress($_POST['destinatario'] ?? 'test@ejemplo.com');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'No me funes con el correo';
    $mail->Body    = 'Este correo fue enviado desde Mangurrinos para demostrar que España va bien.';

    $mail->send();
    echo "✅ Correo enviado correctamente. Ver MailHog en <a href='http://localhost:8080/Public/'>localhost:8025</a>";
} catch (Exception $e) {
    echo "❌ Error al enviar el correo: {$mail->ErrorInfo}";
}