<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Editor de Empresas'
]);
?>

<?php $this->start('pageContent') ?>

<div class="editarEmpresaContenedor">
    <div class="editarEncabezado">
        <h1>Editar empresa</h1>
    </div>
    <form class="editarForm" action="index.php?menu=admin-empresas" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_empresa" value="<?= $empresaEdit->getIdEmpresa() ?>">
        <input type="hidden" name="id_user" value="<?= $empresaEdit->getIdUsuario() ?>">
        
        <div class="editarGrid">
            <div class="editarLogo">
                <img src="/Assets/Images/Empresa/<?= $empresaEdit->getLogoUrl() ?>" alt="logo_empresa" />
                <label for="nuevoLogo" class="labelLogo">Cambiar logo</label>
                <input type="file" name="nuevoLogo" id="nuevoLogo" accept="image/*">
            </div>
            <div class="editarCampo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?= $empresaEdit->getNombre() ?>">
            </div>
            <div class="editarCampo editarCampoGrande">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"><?= $empresaEdit->getDescripcion() ?></textarea>
            </div>
            <div class="editarCampo">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="<?= $empresaEdit->getDireccion() ?>">
            </div>
            <div class="editarCampo">
                <label for="correo">Correo</label>
                <input type="text" id="correo" name="correo" value="<?= $empresaEdit->getCorreo() ?>" readonly>
            </div>
        </div>
        <div class="editarBotonera">
            <input type="submit" name="btnAceptar" class="btnAceptarEditar" value="Aceptar">
            <a href="index.php?menu=admin-empresas" class="btnCancelarEditar">Cancelar</a>
        </div>
    </form>
</div>
<?php $this->stop() ?>