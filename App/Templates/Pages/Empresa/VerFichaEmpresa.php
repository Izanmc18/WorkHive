<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Ficha de Empresa'
]);
?>


<?php $this->start('pageContent') ?>
<div class="fichaEmpresaContenedor">
    <div class="fichaEncabezado">
        <h1>Ficha de empresa: <?= $empresaVer->getNombre() ?></h1>
    </div>
    <div class="fichaForm"> 
        <div class="fichaGrid">
            <div class="fichaLogoNombre">
                <div class="fichaLogo">
                    <img src="<?= $empresaVer->getLogoUrl() 
                    ? 'Assets/Images/Empresa/' . $empresaVer->getLogoUrl() 
                    : 'Assets/Images/Empresa/placeholderUsers.png' 
                    ?>" alt="logo_empresa" />
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
                <label>Descripción</label>
                <textarea readonly rows="4"><?= $empresaVer->getDescripcion() ?></textarea>
            </div>
            <div class="fichaCampo">
                <label>Dirección</label>
                <input type="text" value="<?= $empresaVer->getDireccion() ?>" readonly>
            </div>
            
            <div class="fichaCampo fichaCampoVacio"></div>
            <div class="fichaCampo fichaCampoVacio"></div>
        </div>
        <div class="fichaBotonera">
            <a href="index.php?menu=admin-empresas" class="fichaBtnVolver">Volver</a>
        </div>
    </div>
</div>
<?php $this->stop() ?>