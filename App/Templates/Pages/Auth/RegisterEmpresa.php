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

<section class="">
    <div class="loginContainer" style="max-width: 450px;">
        <h2>Registrarse como Empresa</h2>

        
        <form action="index.php?menu=regEmpresa" method="POST" enctype="multipart/form-data" class="loginForm">

            <label for="nombre" style="margin-top: 0;">Nombre de la Empresa</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Ej: Tech Solutions">

            <label for="correo">Correo Electrónico</label>
            <input type="email" id="correo" name="correo" required placeholder="email@empresa.com">

            <label for="clave">Contraseña</label>
            <input type="password" id="clave" name="clave" required>

            <label for="direccion">Dirección Física</label>
            <input type="text" id="direccion" name="direccion" placeholder="Calle, Número, Ciudad">

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3" style="width: 100%; box-sizing: border-box;"></textarea>

            <label for="logo">Logo de la Empresa</label>
            
            <input type="file" id="logo" name="logo" accept="image/*" style="width: 100%; box-sizing: border-box;">

            <button type="submit" name="btnGuardarEmpresa" class="btnLogin" style="margin-top: 0;">
                Guardar Empresa
            </button>
            <p class="volverInicio"><a href="index.php" class="btnVolver">Volver al Inicio</a></p>
            
        </form>
        
        <p class="loginText" style="margin-top: 15px;">¿Ya tienes cuenta? <a href="?menu=login">Inicia sesión aquí</a></p>
        
    </div>
    
    
<?php
$this->stop();
?>