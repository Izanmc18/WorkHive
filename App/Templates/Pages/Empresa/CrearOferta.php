<?php
/**
 * @var array $ciclosDisponibles
 * @var array $familiasDisponibles
 * @var int $idEmpresa
 */
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Crear Oferta'
]);?>

<?php $this->start('menu') ?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=empresa-dashboard">Dashboard</a></li>
        <li><a href="?menu=empresa-ofertas" class="active">Mis Ofertas</a></li> 
        <li><a href="?menu=logout" class="btnLogout">Cerrar Sesión</a></li>
    </ul>
<?php $this->stop() ?>

<?php $this->start('pageContent') ?>
<section class="registerContainer editar-oferta-contenedor"> 
    <h2><span class="resaltado">Nueva Oferta</span> de Empleo</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin: 15px 0; border-radius: 8px; font-weight: bold;">
            ❌ Error: <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form class="registerForm" action="index.php?menu=empresa-procesar-creacion-oferta" method="POST">
        
        <input type="hidden" name="id_empresa" value="<?= $idEmpresa ?>">

        <div class="registerColumns">
            <div class="registerColumn">
                
                <label for="titulo">Título de la Oferta</label>
                <input type="text" id="titulo" name="titulo" required class="form-input-text">
                
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="6" required class="form-input-textarea"></textarea>
                
                <label for="fechainicio">Fecha de Inicio de Reclutamiento</label>
                <input type="date" id="fechainicio" name="fechainicio" value="<?= date('Y-m-d') ?>" required class="form-input-date">

                <label for="fechafin">Fecha Límite de Reclutamiento (Opcional)</label>
                <input type="date" id="fechafin" name="fechafin" class="form-input-date">
                
                <label for="activa">Estado</label>
                <select id="activa" name="activa" class="form-input-select">
                    <option value="1" selected>Activa (Visible)</option>
                    <option value="0">Inactiva (Oculta)</option>
                </select>
            </div>
            
            <div class="registerColumn">
                <h3>Ciclos Formativos Requeridos</h3>
                <p class="ciclos-subtitulo">Selecciona uno o más ciclos:</p>
                
                <div class="checkbox-list ciclos-checkbox-list">
                    <?php if (!empty($ciclosDisponibles)): ?>
                        <select id="ciclos_select" name="ciclos[]" multiple class="select-multiple form-input-select">
                        <?php foreach ($ciclosDisponibles as $ciclo): ?>
                            <option value="<?= $ciclo->getIdCiclo() ?>">
                                <?= $ciclo->getNombre() ?> (<?= $ciclo->getTipo() ?>)
                            </option>
                        <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p>No hay ciclos formativos registrados.</p>
                    <?php endif; ?>
                </div>

                <label for="familia_filter" class="familia-label form-label-spacing">Familia Profesional</label>
                <select id="familia_filter" name="id_familia_seleccionada" class="form-input-select" disabled>
                    <option value="">-- Todas las Familias (Filtro no activo) --</option>
                    <?php 
                    if (!empty($familiasDisponibles)):
                        foreach ($familiasDisponibles as $familia) {
                            echo '<option value="' . $familia->getIdFamilia() . '">' . $familia->getNombre() . '</option>';
                        }
                    endif;
                    ?>
                </select>
                
                <button type="submit" name="btnCrearOferta" class="btnRegister">Crear Oferta</button>
            </div>
        </div>
    </form>
    
    <a href="index.php?menu=empresa-ofertas" class="btnVolver volver-ofertas-btn">Cancelar y Volver</a>
</section>
<?php $this->stop() ?>