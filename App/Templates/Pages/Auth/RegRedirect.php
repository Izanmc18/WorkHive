<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Registro',
    ]);
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

<?php $this->start('pageContent') ?>
<section id="registro-redireccion">
    <div class="loginContainer">

        <div class="redirecion-header">
            <h2>Registro de Usuarios</h2>
            <p class="subtitulo-redirect">Selecciona el tipo de usuario con el que deseas crear tu cuenta:</p>
        </div>

        <div class="redireccion-elegir">
            <a href="?menu=regAlumno" class="btnRegistro btnAlumno">Registrarme como Alumno</a>
            <a href="?menu=regEmpresa" class="btnRegistro">Registrarme como Empresa</a>
        </div>
        <div>
            <a href="?menu=landing" class="btnVolver">Volver al inicio</a>
        </div>
        
        
    </div>
</section>
<?php $this->stop() ?>