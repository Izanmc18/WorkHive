<?php
$this->layout('Layout/Layout', ['title' => 'WorkHive - Registro Alumno']);
?>

<?php
$this->start('menu');
?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=login">Iniciar sesión</a></li>
    </ul>
<?php
$this->stop();
?>

<?php
$this->start('pageContent');
?>
<section class="registerContainer">
    <h2>Registrarse como Alumno</h2>

    
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red; border: 1px solid red; padding: 10px;">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </p>
    <?php endif; ?>

    
    <form class="registerForm" action="index.php?menu=regAlumno" method="POST" enctype="multipart/form-data">
        <div class="registerColumns">
            <div class="registerColumn">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
                
                <label for="apellido1">Primer apellido</label>
                <input type="text" id="apellido1" name="apellido1" required>
                
                <label for="apellido2">Segundo apellido</label>
                <input type="text" id="apellido2" name="apellido2">
                
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion">
                
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" min="16" max="99">
            </div>
            
            <div class="registerColumn">
                
                <label for="curriculumurl">Curriculum (PDF)</label>
                <input type="file" id="curriculumurl" name="curriculum" accept=".pdf">
                
        
                <label for="fotoperfil">Foto de perfil</label>
                <input type="file" id="fotoperfil" name="fotoPerfil" accept="image/*">
                
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" required>
                
                
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required>
                
                <button type="submit" class="btnRegister">Crear cuenta de alumno</button>
            </div>
        </div>
    </form>
    
    <p class="loginText">¿Ya tienes cuenta? <a href="?menu=login">Inicia sesión aquí</a></p>
    <a href="index.php" class="btnVolver">Volver al Inicio</a>
</section>
<?php
$this->stop();
?>
