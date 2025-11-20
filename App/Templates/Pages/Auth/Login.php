<?php
$this->layout('Layout/Layout', ['title' => 'WorkHive - Login']);
?>
<?php
// Obtener mensaje de error de la sesión si existe y luego limpiarlo
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<?php
$this->start('menu');
?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=regRedirect">Registrarse</a></li>
    </ul>
<?php
$this->stop();
?>

<?php
$this->start('pageContent');
?>
<section id="login-seccion">
    <div class="loginContainer">
        <h2>Inicia Sesión</h2>

        <!-- Mensaje de error de PHP -->
        <?php if ($error): ?>
            <p id="mensajeError" style="color: #d32f2f; font-weight: bold; margin-bottom: 1.5em; display: block;"><?= $error ?></p>
        <?php endif; ?>

        <!--
            El formulario se envía de forma tradicional (POST)
        -->
        <form id="loginForm" class="loginForm" action="index.php?menu=login" method="POST">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" placeholder="ejemplo@portal.com" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" placeholder="**" required>

            <button type="submit" class="btnLogin">ACCEDER</button>
        </form>

        <div class="registerText">
            ¿Aún no tienes cuenta? 
            <a href="index.php?menu=regRedirect">Regístrate aquí</a>
        </div>
        <div class="volverInicio">
            <a href="index.php?menu=landing" class="btnVolver">Volver a Inicio</a>
        </div>
    </div>
</section>
<?php
$this->stop();
?>

<?php
$this->start('js');
?>
<script src="Assets/Js/Login.js"></script>
<?php
$this->stop();
?>