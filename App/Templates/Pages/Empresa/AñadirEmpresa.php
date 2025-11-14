<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Añadir Empresas'
]);
?>


<?php $this->start('pageContent') ?>
<section class="registerContainer">
    <h2>Registrar una Empresa</h2>
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
    <p class="volverInicio"><a href="menu=adminEmpresas" class="btnVolver">Volver al listado</a></p>
</section>
<?php $this->stop() ?>