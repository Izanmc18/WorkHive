<?php $this->layout('Layout/Layout', [
    'title' => 'Login | WorkHive'
]) ?>

<?php $this->start('css') ?>
    <link rel="stylesheet" href="/Public/Css/styles-login.css">
<?php $this->stop() ?>

<?php $this->start('stack', 'head') ?>
    <!-- Puedes hacer push/unshift aquí para scripts específicos si lo necesitas -->
<?php $this->stop() ?>

<?php $this->start('header') ?>
    <header>
        <img src="/Public/Images/unnamed.jpg" alt="WorkHive Logo" style="height:80px;">
        <h1>Accede a WorkHive</h1>
        <p>Encuentra tu futuro hoy</p>
    </header>
<?php $this->stop() ?>

<?php $this->start('body') ?>
    <main>
        <form class="login-form" method="post" action="/login">
            <label for="usuario">Usuario o Email</label>
            <input type="text" id="usuario" name="usuario" required>
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="btn">Iniciar sesión</button>
        </form>
        <section class="links">
            <a href="RegisterAlumno.php">¿No tienes cuenta? Regístrate como alumno</a>
            <a href="RegisterEmpresa.php">¿Eres empresa? Regístrate aquí</a>
        </section>
    </main>
<?php $this->stop() ?>

<?php $this->start('footer') ?>
    <footer>
        <small>© 2025 WorkHive</small>
    </footer>
<?php $this->stop() ?>

<?php $this->start('stack', 'scripts') ?>
    <script src="/Public/Js/login.js"></script>
<?php $this->stop() ?>
