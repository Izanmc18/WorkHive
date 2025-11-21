<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        $this->mail->isSMTP();
        $this->mail->Host = 'mailhog'; 
        $this->mail->SMTPAuth = false; 
        $this->mail->Username = ''; 
        $this->mail->Password = ''; 
        $this->mail->SMTPSecure = ''; 
        $this->mail->Port = 1025;
        
        $this->mail->CharSet = 'UTF-8';
        
        $this->mail->setFrom('no-responder@workhive.com', 'WorkHive Portal');
    }

    /**
     * Envía una notificación al alumno sobre el estado de su candidatura.
     * 
     */
    public function enviarEstadoSolicitud($correoDestinatario, $nombreDestinatario, $tituloOferta, $estado) {
        
        $esAceptada = ($estado === 'aceptada');
        
        $subject = $esAceptada ? '🎉 ¡Enhorabuena! Candidatura Aceptada' : 'Actualización: Candidatura Rechazada';
        
        $messageHeader = $esAceptada ? "Felicidades" : "Agradecimiento";
        $messageBody = $esAceptada 
            ? "Nos complace informarte que la empresa ha aceptado tu candidatura para el puesto '$tituloOferta'. Se pondrán en contacto contigo pronto para continuar con el proceso."
            : "Lamentamos informarte que la empresa ha decidido no avanzar con tu candidatura para el puesto '$tituloOferta' en esta ocasión. Gracias por tu interés y esfuerzo.";

        
        
        $colorPrincipal = '#1d3557'; 
        $colorSecundario = '#f9b233'; 
        $colorExito = '#28a745';
        $colorFondo = '#f6f8fa'; 
        
        $bodyHtml = "
            <div style='font-family: Arial, sans-serif; background-color: {$colorFondo}; padding: 20px; border-radius: 8px;'>
                <table style='width: 100%; max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; overflow: hidden; border: 1px solid #ddd;'>
                    
                    <tr>
                        <td style='background-color: {$colorPrincipal}; padding: 20px; text-align: center;'>
                            <h1 style='color: white; font-size: 1.5em; margin: 0;'>
                                Candidatura ". ($esAceptada ? "ACEPTADA" : "ACTUALIZADA") ."
                            </h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style='padding: 30px;'>
                            <h2 style='color: {$colorPrincipal}; font-size: 1.2em; margin-top: 0;'>
                                $messageHeader, $nombreDestinatario.
                            </h2>
                            
                            <p style='color: #444; font-size: 1em; line-height: 1.5;'>$messageBody</p>
                            
                            <div style='margin-top: 25px; padding: 15px; border-left: 5px solid ". ($esAceptada ? $colorExito : $colorSecundario) ."; background-color: #f8f8f8;'>
                                <p style='margin: 0; font-weight: bold; color: {$colorPrincipal};'>Puesto: $tituloOferta</p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style='padding: 20px; text-align: center; background-color: {$colorFondo}; color: #888; font-size: 0.8em; border-top: 1px solid #eee;'>
                            <p style='margin: 0;'>Saludos cordiales, el equipo de WorkHive.</p>
                        </td>
                    </tr>
                </table>
            </div>
        ";
        


        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($correoDestinatario, $nombreDestinatario);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $bodyHtml;
            $this->mail->AltBody = strip_tags($messageBody); 

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar correo a $correoDestinatario: {$this->mail->ErrorInfo}");
            return false;
        }
    }
    
    // Si tienes otros métodos, deberías renombrarlos también aquí (ej: enviarRegistro, enviarTokenRecuperacion)
    
}