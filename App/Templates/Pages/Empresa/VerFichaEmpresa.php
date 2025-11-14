<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Ficha de Empresa'
]);
?>



<?php $this->start('pageContent') ?>
<div class="fichaEmpresaContenedor">
    <div class="fichaEncabezado">
        <h1>Ficha de empresa</h1>
    </div>
    <form action="" method="post" class="fichaForm">
        <input type="hidden" name="id" value="<?= $empresaVer->getIdempresa() ?>">
        <div class="fichaGrid">
            <div class="fichaLogoNombre">
                <div class="fichaLogo">
                    <img src="<?= $empresaVer->getLogoUrl() 
                    ? '/Public/Assets/Images/Empresa/' . $empresaVer->getLogoUrl() 
                    : '/Public/Assets/Images/placeholderUsers.png' 
                    ?>" alt="logo_empresa" />
                </div>
                <div class="fichaCampo">
                    <label>CIF</label>
                    <input type="text" value="<?= $empresaVer->getCif() ?>" readonly>
                </div>
                <div class="fichaCampo">
                    <label>Nombre de la empresa</label>
                    <input type="text" value="<?= $empresaVer->getNombre() ?>" readonly>
                </div>
                <div class="fichaCampo">
                    <label>Email</label>
                    <input type="text" value="<?= $empresaVer->getCorreo() ?>" readonly>
                </div>
            </div>
            <div class="fichaCampo">
                <label>Teléfono empresa</label>
                <input type="text" value="<?= $empresaVer->getTelefono() ?>" readonly>
            </div>
            <div class="fichaCampo">
                <label>Persona de contacto</label>
                <input type="text" value="<?= $empresaVer->getNombrePersona() ?>" readonly>
            </div>
            <div class="fichaCampo">
                <label>Teléfono persona</label>
                <input type="text" value="<?= $empresaVer->getTelPersona() ?>" readonly>
            </div>
            <div class="fichaCampo">
                <label>Provincia</label>
                <input type="text" value="<?= $empresaVer->getProvincia() ?>" readonly>
            </div>
            <div class="fichaCampo">
                <label>Localidad</label>
                <input type="text" value="<?= $empresaVer->getLocalidad() ?>" readonly>
            </div>
            <div class="fichaCampo">
                <label>Dirección</label>
                <input type="text" value="<?= $empresaVer->getDireccion() ?>" readonly>
            </div>
            <div class="fichaCampo fichaCampoVacio"></div>
        </div>
        <div class="fichaBotonera">
            <input type="submit" class="fichaBtnVolver" name="btnVolver" value="Volver">
        </div>
    </form>
</div>
<?php $this->stop() ?>