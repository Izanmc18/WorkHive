<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'WorkHive') ?></title>
    <link rel="icon" type="image/x-icon" href="Assets/Images/favicon.ico"> 
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    
    <!-- 🛑 RUTA CSS CORREGIDA (Quitamos la barra inicial / para que funcione como relativa) -->
    <link rel="stylesheet" type="text/css" href="Assets/Css/style.css"/>
    
</head>
<body>
    <header>
        <!-- Corregida la ruta de imagen para ser relativa al index.php -->
        <img src="Assets/Images/unnamed3.png" alt="Logo WorkHive" class="logoImg">
        <nav class="menuNav">
            <?= $this->section('menu') ?>
        </nav>
    </header>
    <main>
        <!-- El contenido específico de la página se inserta aquí -->
        <?= $this->section('pageContent') ?>
    </main>
    <footer class="pie">
        <div class="contenedorFooter">
            <div class="divLogoFooter">
                <img class="logoPie" src="Assets/Images/unnamed3.png" width="500px" height="500px">
            </div>
            <div class="contenidoFooter">
                <div class="footerRRSS">
                    <div class="tituloFooter">
                        Redes Sociales
                    </div>

                    <ul class="social-links">
                        <li>
                            <a href="https://www.instagram.com/WorkHive" target="_blank" class="social-link">Instagram</a>
                        </li>
                        <li>
                            <a href="https://www.x.com/WorkHive" target="_blank" class="social-link">X</a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/WorkHive" target="_blank" class="social-link">Facebook</a>
                        </li>
                    </ul>
                </div>
                <div class="footerContacto">
                    <div class="tituloFooter">
                        Contactanos
                    </div>

                    <ul class="linkContacto">
                        <li>
                            <a href="tel:+34600111222" target="_blank" class="about-link">Contacto</a>
                        </li>
                        <li>
                            <!-- Corregida la ruta del enlace WhatsApp -->
                            <a href="https://wa.me/34600111222" target="_blank" class="about-link">Chat</a>
                        </li>
                        <li>
                            <a href="mailto:contacto@workhive.com" target="_blank" class="about-link">Email</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div>
            <p>&copy; 2025 WorkHive. Todos los derechos reservados.</p>
        </div>
    </footer>
    
    <?= $this->section('js') ?>
</body>
</html>

