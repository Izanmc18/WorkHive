<?php
    $this->layout('Layout/Layout', [
    'title' => 'WorkHive - Editar Oferta'
]);?>

<?php $this->start('menu') ?>


<?php 
// 🛑 BLOQUE DE VISUALIZACIÓN DE MENSAJES DE SESIÓN 🛑
if (isset($_SESSION['error'])): ?>
    <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin: 20px auto; max-width: 1200px; border-radius: 8px; font-weight: bold;">
        ❌ Error al guardar: <?= $_SESSION['error']; ?>
    </div>
<?php 
    // Es crucial limpiar la variable de sesión para que no se muestre en futuras cargas
    unset($_SESSION['error']);
endif; 

if (isset($_SESSION['exito'])): ?>
    <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; margin: 20px auto; max-width: 1200px; border-radius: 8px; font-weight: bold;">
        ✅ Éxito: <?= $_SESSION['exito']; ?>
    </div>
<?php 
    unset($_SESSION['exito']);
endif; 
// 🛑 FIN BLOQUE DE MENSAJES 🛑
?>


    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=empresa-dashboard">Dashboard</a></li>
        <li><a href="?menu=empresa-ofertas" class="active">Mis Ofertas</a></li> 
        <li><a href="?menu=logout" class="btnLogout">Cerrar Sesión</a></li>
    </ul>
<?php $this->stop() ?>

<?php $this->start('pageContent') ?>
<section class="registerContainer editar-oferta-contenedor"> 
    <h2>Editar Oferta: <span class="resaltado"><?= $oferta->getTitulo() ?></span></h2>
    
    <form class="registerForm" action="index.php?menu=empresa-procesar-edicion-oferta" method="POST">
        
        <input type="hidden" name="id_oferta" value="<?= $oferta->getIdOferta() ?>">
        <input type="hidden" name="id_empresa" value="<?= $oferta->getIdEmpresa() ?>">

        <div class="registerColumns">
            <div class="registerColumn">
                
                <label for="titulo">Título de la Oferta</label>
                <input type="text" id="titulo" name="titulo" value="<?= $oferta->getTitulo() ?>" required class="form-input-text">
                
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="6" required class="form-input-textarea"><?= $oferta->getDescripcion() ?></textarea>
                
                <label for="fechainicio">Fecha de Inicio de Reclutamiento</label>
                <input type="date" id="fechainicio" name="fechainicio" value="<?= $oferta->getFechaInicio() ?>" required class="form-input-date">

                <label for="fechafin">Fecha Límite de Reclutamiento</label>
                <input type="date" id="fechafin" name="fechafin" value="<?= $oferta->getFechaFin() ?>" class="form-input-date">
                
                <label for="activa">Estado (Activa)</label>
                <select id="activa" name="activa" class="form-input-select">
                    <option value="1" <?= $oferta->getActiva() ? 'selected' : '' ?>>Sí (Visible)</option>
                    <option value="0" <?= !$oferta->getActiva() ? 'selected' : '' ?>>No (Inactiva)</option>
                </select>
            </div>
            
            <div class="registerColumn">
                <h3>Ciclos Formativos Requeridos</h3>
                <p class="ciclos-subtitulo">Selecciona uno o más ciclos:</p>
                
                <div class="checkbox-list ciclos-checkbox-list">
                    <?php if (!empty($ciclosDisponibles)): ?>
                        <select id="ciclos_select" name="ciclos[]" multiple class="select-multiple form-input-select">
                        <?php foreach ($ciclosDisponibles as $ciclo): ?>
                            <?php 
                            $cicloId = $ciclo->getIdCiclo();
                            $selected = in_array($cicloId, $ciclosAsociadosIds) ? 'selected' : '';
                            ?>
                            <option value="<?= $cicloId ?>" <?= $selected ?>>
                                <?= $ciclo->getNombre() ?> (<?= $ciclo->getTipo() ?>)
                            </option>
                        <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p>No hay ciclos formativos registrados.</p>
                    <?php endif; ?>
                </div>

                <label for="familia_filter" class="familia-label form-label-spacing">Familia Profesional</label>
                <select id="familia_filter" name="id_familia_seleccionada" class="form-input-select">
                    <option value="">-- Todas las Familias --</option>
                    <?php 
                    if (!empty($familiasDisponibles)):
                        foreach ($familiasDisponibles as $familia) {
                            echo '<option value="' . $familia->getIdFamilia() . '">' . $familia->getNombre() . '</option>';
                        }
                    endif;
                    ?>
                </select>
                
                <button type="submit" name="btnGuardarEdicion" class="btnRegister">Guardar Cambios</button>
            </div>
        </div>
    </form>
    
    <a href="index.php?menu=empresa-ofertas" class="btnVolver volver-ofertas-btn">Cancelar y Volver</a>
</section>
<?php $this->stop() ?>