<?php 

require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configura las opciones de Dompdf incluyendo chroot para seguridad y carga de imágenes locales
$options = new Options();
$options->setChroot([realpath(__DIR__ . '/assets/images')]);
$options->setIsRemoteEnabled(true);

$dompdf = new Dompdf($options);

// Ruta absoluta a la imagen con formato file:///
$imagePath = 'file://' . str_replace('\\', '/', realpath(__DIR__ . '/assets/images/unnamed.jpg'));

// HTML del currículum con estilo adaptado al formato A4 y la imagen correcta
$html = '
<html>
<head>
  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f4f4;
    }
    .cv-container {
      background: #fff;
      width: 570px;
      margin: 10px auto;
      box-shadow: 0 0 10px #ccc;
      padding: 40px 35px;
      border-radius: 12px;
      overflow: hidden;
    }
    .foto-perfil {
      display: block;
      margin: 0 auto 16px auto;
      border-radius: 100px;
      border: 4px solid #2187e7;
      width: 110px;
      height: 110px;
      object-fit: cover;
    }
    .nombre {
      color: #2187e7;
      font-size: 2em;
      margin-bottom: 5px;
      font-weight: bold;
      text-align: center;
    }
    .titulo {
      font-size: 1.2em;
      color: #333;
      text-align: center;
      margin-bottom: 5px;
    }
    .datos {
      font-size: 1em;
      color: #555;
      margin-bottom: 13px;
      text-align: center;
    }
    .bloque-titulo {
      color: #2187e7;
      font-size: 1.12em;
      font-weight: bold;
      margin-top: 16px;
      margin-bottom: 5px;
    }
    ul {
      padding-left: 20px;
      color: #555;
      font-size: 1em;
      margin: 0 0 0 5px;
    }
    .cv-footer {
      margin-top: 25px;
      font-size: .9em;
      color: #aaa;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="cv-container">
    <img src="' . $imagePath . '" class="foto-perfil" />
    <div class="nombre">Juan Pérez</div>
    <div class="titulo">Desarrollador Full Stack</div>
    <div class="datos">
        <b>Email:</b> juan.perez@email.com <br>
        <b>Teléfono:</b> 600 000 000 <br>
        <b>Localidad:</b> Madrid
    </div>
    <div class="bloque-titulo">PERFIL PROFESIONAL</div>
      <p>
        Desarrollador web apasionado por la tecnología, con experiencia en proyectos PHP, JavaScript y frameworks modernos. Hábil en el trabajo en equipo, el aprendizaje continuo y la resolución de problemas.
      </p>
    <div class="bloque-titulo">EXPERIENCIA</div>
      <ul>
        <li>Empresa X (2023 - Actual): Desarrollo de aplicaciones PHP y REST API.</li>
        <li>Startup Y (2021 - 2023): Frontend React y maquetación web moderna.</li>
      </ul>
    <div class="bloque-titulo">EDUCACIÓN</div>
      <ul>
        <li>Ingeniería Informática, Universidad Complutense de Madrid (2017-2021)</li>
      </ul>
    <div class="bloque-titulo">HABILIDADES</div>
      <ul>
        <li>PHP, JavaScript, HTML, CSS</li>
        <li>React, Laravel, Docker, Git</li>
        <li>Inglés avanzado</li>
      </ul>
    <div class="cv-footer">
      Currículum generado con DomPDF &copy; 2025
    </div>
  </div>
</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("cv-juan-perez.pdf");
?>
