<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Añadir Empresas'
]);
?>


<?php $this->start('pageContent') ?>
<div class="registerContainer"> 
    <h2>Añadir Nueva Empresa</h2>

    <form class="registerForm" action="index.php?menu=admin-empresas" method="POST" enctype="multipart/form-data">

        <label for="nombre">Nombre de la Empresa </label><br>
        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Tech Solutions"><br><br>

        <label for="correo">Correo Electrónico</label><br>
        <input type="email" id="correo" name="correo" required placeholder="email@empresa.com"><br><br>

        <label for="clave">Contraseña </label><br>
        <input type="password" id="clave" name="clave" required><br><br>

        <label for="direccion">Dirección Física</label><br>
        <input type="text" id="direccion" name="direccion"><br><br>

        <label for="descripcion">Descripción</label><br>
        <textarea id="descripcion" name="descripcion" rows="5"></textarea><br><br>

        <label for="logo">Logo de la Empresa</label><br>
        <input type="file" id="logo" name="logo" accept="image/*"><br><br>

        <hr>

        <button type="submit" name="btnGuardarEmpresa" class="btnRegister">Guardar Empresa</button>

        <a href="index.php?menu=admin-empresas" class="btnCancelarEditar">Cancelar</a>
    </form>
</div>
<?php $this->stop() ?>