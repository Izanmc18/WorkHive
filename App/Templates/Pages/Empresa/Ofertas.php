<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Mis Ofertas'
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
    
    <div class="seccion-cabecera">
        <h1>Gestión de <span class="resaltado">Ofertas</span></h1>
        <p class="subtitulo-dashboard">Consulta el impacto de tus ofertas y gestiona las vacantes activas.</p>
    </div>

    <?php if (!empty($topOfertas)): ?>
    <div class="listaContenedor top-five-container">
        <h2>Top 5 Ofertas Más Populares</h2>
        <div class="grid-kpi">
            <?php foreach ($topOfertas as $index => $oferta): ?>
                <div class="tarjeta-kpi kpi-solicitudes top-card">
                    <div class="ranking-number">#<?= $index + 1 ?></div>
                    <div class="contenido-kpi">
                        <p class="titulo-kpi titulo-top"><?= $oferta['titulo'] ?></p>
                        <p class="valor-kpi valor-top">
                            <?= $oferta['total_solicitudes'] ?> 
                            <span class="solicitudes-label">solicitudes</span>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="listaContenedor">
        <div class="seccionEncabezado">
            <div class="header-flex">
                <h1>Listado Completo</h1>
                <a href="?menu=empresa-crear-oferta" id="agregar">+ NUEVA OFERTA</a>
            </div>
        </div>

        <div class="contenedorTabla">
            <table class="tablaAlumnos">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha Publicación</th>
                        <th>Estado</th>
                        <th>Solicitudes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($todasOfertas) && $page == 1): ?>
                        <tr>
                            <td colspan="5" class="fila-vacia">
                                No has publicado ninguna oferta todavía.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($todasOfertas as $oferta): ?>
                            <tr>
                                <td class="col-titulo">
                                    <?= $oferta['titulo'] ?>
                                </td>
                                <td class="col-fecha">
                                    <?= date('d/m/Y', strtotime($oferta['fecha_inicio'])) ?>
                                </td>
                                <td>
                                    <?php if ($oferta['activa']): ?>
                                        <span class="badge badge-active">Activa</span>
                                    <?php else: ?>
                                        <span class="badge badge-inactive">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td class="">
                                    <div class="solicitudes-flex">
                                        <span class="num-solicitudes">
                                            <?= $oferta['num_solicitudes'] ?>
                                        </span>
                                        
                                        <?php if ($oferta['num_solicitudes'] > 0): ?>
                                            <a href="index.php?menu=empresa-ver-solicitudes&id=<?= $oferta['id_oferta'] ?>" 
                                            class="action-btn view-btn" title="Ver listado de alumnos">
                                            Ver
                                            </a>
                                        <?php else: ?>
                                            <span class="action-btn view-btn disabled" title="Sin solicitudes">Ver</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="columnaAcciones">
                                    <a href="index.php?menu=empresa-editar-oferta&id=<?= $oferta['id_oferta'] ?>" 
                                        class="action-btn edit-btn">
                                        Editar
                                    </a>
                                    
                                    <form action="index.php?menu=empresa-eliminar-oferta" method="POST" class="inline-form">
                                        <input type="hidden" name="id_oferta" value="<?= $oferta['id_oferta'] ?>">
                                        <button type="submit" 
                                                class="action-btn delete-btn"
                                                onclick="return confirm('¿Estás seguro de que quieres borrar esta oferta?');">
                                            Borrar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="paginacion-contenedor">
            <div class="paginacion-info">
                Pag: <strong><?= $page ?></strong> de <strong><?= $totalPages ?></strong> (Total: <?= $totalCount ?> ofertas)
            </div>
            
            <div class="paginacion-botones">
                <?php if ($page > 1): ?>
                    <a href="?menu=empresa-ofertas&page=<?= $page - 1 ?>&limit=<?= $limit ?>" class="btn-paginacion">← Anterior</a>
                <?php else: ?>
                    <span class="btn-paginacion disabled">← Anterior</span>
                <?php endif; ?>

                <form action="index.php" method="GET" class="inline-form limit-control">
                    <input type="hidden" name="menu" value="empresa-ofertas">
                    <label for="limit">Mostrar:</label>
                    <select name="limit" id="limit" onchange="this.form.submit()">
                        <?php $limits = [5, 10, 20]; ?>
                        <?php foreach ($limits as $l): ?>
                            <option value="<?= $l ?>" <?= $limit == $l ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="page" value="1">
                </form>

                <?php if ($page < $totalPages): ?>
                    <a href="?menu=empresa-ofertas&page=<?= $page + 1 ?>&limit=<?= $limit ?>" class="btn-paginacion">Siguiente →</a>
                <?php else: ?>
                    <span class="btn-paginacion disabled">Siguiente →</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>
<?php $this->stop() ?>