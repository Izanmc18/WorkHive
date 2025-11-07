<?php $this->layout('Layout/Layout', [
    'title' => 'Registro Alumno | WorkHive'
]) ?>

<?php $this->start('css') ?>
    <link rel="stylesheet" href="/Public/Css/styles-register.css">
<?php $this->stop() ?>

<?php $this->start('header') ?>
    <header>
        <img src="/Public/Images/unnamed.jpg" alt="WorkHive Logo" style="height:80px;">
        <h1>Registro de Alumno</h1>
    </header>
<?php $this->stop() ?>

<?php $this->start('body') ?>
    <main>
        <form class="register-form" method="post" enctype="multipart/form-data" id="registerAlumno">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" required>
            
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <label for="dni">DNI</label>
            <input type="text" id="dni" name="dni" required>

            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono">

            <label for="ciclo">Ciclo formativo</label>
            <select id="ciclo" name="ciclo" required>
                <option value="">Selecciona tu ciclo</option>
                <!-- Opciones dinámicas vía JS o Plates -->
            </select>

            <label for="foto_perfil">Foto de perfil</label>
            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">

            <button type="submit" class="btn">Registrarse</button>
        </form>
        <section class="links">
            <a href="/login">¿Ya tienes cuenta? Inicia sesión</a>
        </section>
    </main>
<?php $this->stop() ?>
