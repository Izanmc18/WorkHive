<?php
$this->layout('Layout/Layout', ['title' => 'WorkHive - Registro Empresa']);
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
    <h2>Registrarse como Empresa</h2>
    <form class="registerForm" action="" method="POST" enctype="multipart/form-data">
        <div class="registerColums">
            <div class="registerColumn">
                <label for="nombre">Nombre de la empresa</label>
                <input type="text" id="nombre" name="nombre" required>
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
                <label for="logourl">Logo</label>
                <input type="file" id="logourl" name="logourl" accept="image/*">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion">
            </div>
            <div class="registerColumn">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" required>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" class="btnRegister">Crear cuenta de empresa</button>
            </div>
        </div>
        
    </form>
    <p class="loginText">¿Ya tienes cuenta? <a href="?menu=login">Inicia sesión aquí</a></p>
    <p class="volverInicio"><a href="index.php" class="btnVolver">Volver al Inicio</a></p>
</section>
<?php
$this->stop();
?>


