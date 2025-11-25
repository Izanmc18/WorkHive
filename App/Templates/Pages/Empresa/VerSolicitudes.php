<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Solicitudes'
]);
?>

<?php $this->start('menu') ?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=empresa-dashboard">Dashboard</a></li>
        <li><a href="?menu=empresa-ofertas" class="active">Mis Ofertas</a></li> 
        <li><a href="?menu=logout" class="btnLogout">Cerrar Sesión</a></li>
    </ul>
<?php $this->stop() ?>

<?php $this->start('pageContent') ?>
<div class="contenedor-dashboard">
    
    <div class="header-solicitudes">
        <h1>Candidatos para: <span class="resaltado"><?= $oferta->getTitulo() ?></span></h1>
        
        <a href="index.php?menu=empresa-generar-pdf&id=<?= $oferta->getIdOferta() ?>" 
        class="btn-volver-ofertas btn-pdf-reporte">
            Crear Reporte PDF
        </a>
        
        <a href="?menu=empresa-ofertas" class="btn-volver-ofertas">← Volver a Ofertas</a>
    </div>

    <div class="listaContenedor">
        <?php if (empty($solicitudes)): ?>
            <p class="mensaje-vacio">Aún no hay candidatos para esta oferta.</p>
        <?php else: ?>
            <div class="grid-candidatos">
                <?php foreach ($solicitudes as $solicitud): ?>
                    
                    <?php 
                    $estadoActual = $solicitud['estado'];
                    $esRechazada = ($estadoActual === 'rechazada');
                    ?>
                    
                    <div class="tarjeta-candidato <?= $esRechazada ? 'solicitud-rechazada' : '' ?>">
                        
                        <img src="<?= $solicitud['foto_perfil'] ? 'Assets/Images/'.$solicitud['foto_perfil'] : 'Assets/Images/placeholderUsers.png' ?>" 
                             class="foto-candidato" 
                             alt="Foto de <?= $solicitud['nombre'] ?>">
                        
                        <div class="info-candidato">
                            <p class="nombre-candidato">
                                <?= $solicitud['nombre'] . ' ' . $solicitud['apellido1'] . ' ' . $solicitud['apellido2'] ?>
                            </p>
                            <p class="correo-candidato">
                                <?= $solicitud['correo'] ?>
                            </p>
                            
                            <span class="badge badge-<?= $estadoActual ?>">
                                 <?= strtoupper($estadoActual) ?>
                            </span>

                            <?php if ($solicitud['curriculum_url']): ?>
                                <a href="/Data/<?= $solicitud['curriculum_url'] ?>" target="_blank" class="btn-cv" style="margin-top: 15px;">
                                     Ver Curriculum
                                </a>
                            <?php else: ?>
                                <span class="sin-cv" style="margin-top: 15px;">Sin CV adjunto</span>
                            <?php endif; ?>
                            
                            <?php if ($estadoActual !== 'aceptada' && $estadoActual !== 'rechazada'): ?>
                                <div class="acciones-solicitud">
                                    <form action="index.php?menu=empresa-procesar-solicitud" method="POST" class="inline-form">
                                        <input type="hidden" name="id_solicitud" value="<?= $solicitud['id_solicitud'] ?>">
                                        <input type="hidden" name="id_oferta" value="<?= $oferta->getIdOferta() ?>">
                                        
                                        <button type="submit" name="action" value="aceptar" class="action-btn badge-active" 
                                            onclick="return confirm('¿Aceptar y notificar a este candidato?');">Aceptar</button>
                                            
                                        <button type="submit" name="action" value="rechazar" class="action-btn delete-btn" 
                                            onclick="return confirm('¿Rechazar y ocultar esta solicitud?');">Rechazar</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $this->stop() ?>