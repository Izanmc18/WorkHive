<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Añadir Empresas'
]);
?>


<?php $this->start('pageContent') ?>
<div class="registerContainer"> 
    <h2>Añadir Nueva Empresa</h2>

    <form class="registerForm" action="index.php?menu=admin-empresas" method="POST" enctype="multipart/form-data">
        
        <div class="registerColumns">
            
            <div class="registerColumn">
                
                <label for="nombre">Nombre de la Empresa</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej: Tech Solutions">

                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required placeholder="email@empresa.com">

                <label for="clave">Contraseña</label>
                <input type="password" id="clave" name="clave" required>
                
                <label for="logo">Logo de la Empresa</label>
                <input type="file" id="logo" name="logo" accept="image/*">
                
            </div>

            <div class="registerColumn">
                
                <label for="direccion">Dirección Física</label>
                <input type="text" id="direccion" name="direccion" placeholder="Calle, Número, Ciudad">

                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="5" placeholder="Breve descripción de la empresa..."></textarea>
                
                
                <button type="submit" name="btnGuardarEmpresa" class="btnRegister">Guardar Empresa</button>
                <a href="index.php?menu=admin-empresas" class="btnVolver" style="margin-top: 1.5em;">Cancelar</a>
            </div>
        </div>

        
        
        
    </form>
</div>
<?php $this->stop() ?>