<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Ofertas de Empleo'
]);
?>

<?php $this->start('menu') ?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=alumno-dashboard">Dashboard</a></li>
        <li><a href="?menu=alumno-ofertas" class="active">Ofertas</a></li> 
        <li><a href="?menu=alumno-candidaturas">Mis Candidaturas</a></li>
        <li><a href="?menu=alumno-perfil">Mi Perfil</a></li>
        <li><a href="?menu=logout" class="btnLogout">Cerrar Sesión</a></li>
    </ul>
<?php $this->stop() ?>

<?php $this->start('pageContent') ?>
<div class="listaAlumnosContenedor">
    <div class="cabeceraAlumnos">
        <h1>💼 Ofertas Disponibles</h1>
        <div class="busquedaAlumnos">
            <input type="text" id="buscadorOfertas" placeholder="Buscar por título o empresa...">
            <button type="button" class="btnBuscarAlumnos">Buscar</button>
        </div>
    </div>

    <div class="ofertas-grid">
        <?php if (isset($ofertas) && count($ofertas) > 0): ?>
            <?php foreach ($ofertas as $oferta): ?>
                <div class="oferta-card">
                    <div class="oferta-header">
                        <h4><?= $oferta->getTitulo() ?></h4>
                        <span class="badge-empresa">Empresa ID: <?= $oferta->getIdEmpresa() ?></span>
                    </div>
                    <p class="oferta-desc"><?= substr($oferta->getDescripcion(), 0, 100) ?>...</p>
                    
                    <div class="oferta-footer">
                        <span class="fecha-oferta">📅 <?= $oferta->getFechaPublicacion() ?></span>
                        <button type="button" class="action-btn edit-btn btn-ver-oferta" data-id="<?= $oferta->getIdOferta() ?>">
                            Ver Detalles
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="mensaje-vacio">No hay ofertas disponibles en este momento.</p>
        <?php endif; ?>
    </div>
</div>

<div id="modalOferta" class="modal">
    <div class="modal-content-wrapper">
        <span class="close-button" data-modal="modalOferta">&times;</span>
        
        <div id="contenidoOfertaLoader" style="text-align:center; padding: 20px;">Cargando...</div>
        
        <div id="contenidoOfertaBody" style="display:none;">
            <div class="modal-oferta-header">
                <h2 id="modalTitulo" class="modal-oferta-titulo"></h2>
                <span id="modalEmpresa" class="modal-oferta-empresa"></span>
                <div style="margin-top: 10px; font-size: 0.9em; color: #666;">Publicado el: <span id="modalFecha"></span></div>
            </div>

            <div class="modal-oferta-body">
                <div class="modal-seccion-info">
                    <span class="modal-label">Descripción:</span>
                    <p id="modalDescripcion"></p>
                </div>

                <div class="modal-seccion-info">
                    <span class="modal-label">Detalles y Requisitos:</span>
                    <ul class="modal-lista-detalles">
                        <li><strong>Duración:</strong> <span id="modalDuracion"></span> meses</li>
                        <li><strong>Remuneración:</strong> <span id="modalImporte"></span> €</li>
                        </ul>
                </div>
            </div>

            <div id="mensajeFeedback" class="mensaje-feedback"></div>

            <div class="modal-oferta-footer">
                <button type="button" id="btnAccionPostular" class="btnAceptarEditar btn-postular">
                    🚀 Postularme ahora
                </button>
                <button type="button" class="btnCancelarEditar" onclick="document.getElementById('modalOferta').style.display='none'">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?php $this->stop() ?>

<?php $this->start('js') ?>
<script src="Assets/Js/OfertasAlumno.js"></script>
<?php $this->stop() ?>