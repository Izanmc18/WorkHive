<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Dashboard Alumno'
]);
?>

<?php $this->start('menu') ?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=alumno-dashboard" class="active">Dashboard</a></li>
        <li><a href="?menu=alumno-ofertas">Ofertas</a></li> 
        <li><a href="?menu=alumno-candidaturas">Mis Candidaturas</a></li>
        <li><a href="?menu=alumno-perfil">Mi Perfil</a></li>
        <li><a href="?menu=logout" class="btnLogout">Cerrar Sesión</a></li>
    </ul>
<?php $this->stop() ?>

<?php $this->start('pageContent') ?>
<div class="listaAlumnosContenedor">
    
    <div class="cabeceraAlumnos" style="margin-bottom: 20px;">
        <h1>Hola, <?= isset($alumno) ? $alumno->getNombre() : 'Alumno' ?> 👋</h1>
        <p>Bienvenido a tu panel de control. Aquí tienes un resumen de tu actividad.</p>
    </div>

    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #e3f2fd; color: #1565c0;">📄</div>
            <div class="stat-info">
                <h3><?= $totalCandidaturas ?? 0 ?></h3>
                <p>Candidaturas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #e8f5e9; color: #2e7d32;">👁️</div>
            <div class="stat-info">
                <h3><?= $ofertasDisponibles ?? 0 ?></h3>
                <p>Ofertas Nuevas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #fff3e0; color: #ef6c00;">⚠️</div>
            <div class="stat-info">
                <h3><?= $perfilCompleto ? '100%' : 'Incompleto' ?></h3>
                <p>Estado Perfil</p>
            </div>
        </div>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

    <div class="seccion-dashboard">
        <div class="cabeceraAlumnos">
            <h2>🔥 Ofertas Más Solicitadas</h2>
            <a href="?menu=alumno-ofertas" class="btnBuscarAlumnos" style="text-decoration:none; text-align:center;">Ver Todas</a>
        </div>
        
        <div class="ofertas-grid">
            <?php if (isset($ofertasDestacadas) && count($ofertasDestacadas) > 0): ?>
                <?php foreach ($ofertasDestacadas as $oferta): ?>
                    <div class="oferta-card">
                        <div class="oferta-header">
                            <h4><?= $oferta->getTitulo() ?></h4>
                            <span class="badge-empresa">Empresa ID: <?= $oferta->getIdEmpresa() ?></span>
                        </div>
                        <p class="oferta-desc"><?= substr($oferta->getDescripcion(), 0, 80) ?>...</p>
                        <div class="oferta-footer">
                            <span class="fecha-oferta">📅 <?= $oferta->getFechaPublicacion() ?></span>
                            <a href="?menu=alumno-ver-oferta&id=<?= $oferta->getIdOferta() ?>" class="action-btn edit-btn">Ver</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay ofertas destacadas en este momento.</p>
            <?php endif; ?>
        </div>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

    <div class="seccion-dashboard">
        <h2>📋 Tus Últimas Candidaturas</h2>
        <div class="tablaAlumnosContenedor" style="margin-top: 15px;">
            <table class="tablaAlumnos">
                <thead>
                    <tr>
                        <th>Oferta</th>
                        <th>Fecha Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($ultimasCandidaturas) && count($ultimasCandidaturas) > 0): ?>
                        <?php foreach ($ultimasCandidaturas as $candidatura): ?>
                            <tr>
                                <td><?= $candidatura['titulo_oferta'] ?? 'Oferta #' . $candidatura->getIdOferta() ?></td>
                                <td><?= $candidatura['fecha_solicitud'] ?? date('Y-m-d') ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($candidatura['estado'] ?? 'pendiente') ?>">
                                        <?= $candidatura['estado'] ?? 'Pendiente' ?>
                                    </span>
                                </td>
                                <td class="columnaAccionesAlumnos">
                                    <a href="?menu=alumno-detalle-candidatura&id=<?= $candidatura['id'] ?>" class="action-btn ver-ficha-btn">Detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Aún no te has inscrito a ninguna oferta.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $this->stop() ?>