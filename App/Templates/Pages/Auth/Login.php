<?php
$this->layout('Layout/Layout', ['title' => 'WorkHive - Login']);
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
<section class="loginContainer">
    <h2>Iniciar Sesión</h2>
    <form class="loginForm" action="" method="POST">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" class="btnLogin">Entrar</button>
    </form>
    <p class="registerText">¿No tienes cuenta? <a href="?menu=regRedirect">Regístrate aquí</a></p>
    <p class="volverInicio"><a href="menu=landing" class="btnVolver">Volver al Inicio</a></p>
</section>
<?php
$this->stop();
?>
