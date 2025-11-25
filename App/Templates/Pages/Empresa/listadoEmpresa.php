<?php
/**
 * @var array $empresasTotal (Validated Companies DTOs)
 * @var array $pendientes (Pending Companies DTOs)
 */
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Gestión de Empresas'
]);
?>

<?php $this->start('menu') ?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=admin-dashboard">Dashboard</a></li>
        <li><a href="?menu=admin-empresas" class="active">Gestión Empresas</a></li> 
        <li><a href="?menu=admin-alumnos">Gestión Alumnos</a></li>
        <li><a href="?menu=logout" class="btnLogout">Cerrar Sesión</a></li>
    </ul>
<?php $this->stop() ?>

<?php $this->start('pageContent') ?>
<div class="contenedor-dashboard">
    
    <div class="seccion-cabecera">
        <h1>Gestión de <span class="resaltado">Empresas</span></h1>
        <p class="subtitulo-dashboard">Listado de empresas validadas y pendientes de aprobación.</p>
    </div>

    <div class="listaContenedor">
        <div class="seccionEncabezado">
            <div class="header-flex">
                <h1>Empresas Validadas</h1>
                <a href="index.php?menu=admin-empresas&action=add" id="agregar">+ AÑADIR EMPRESA</a>
            </div>
        </div>

        <div class="contenedorTabla">
            <table class="tablaAlumnos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                        <th class="col-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($empresasTotal)): ?>
                        <tr>
                            <td colspan="5" class="fila-vacia">No hay empresas validadas en la base de datos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($empresasTotal as $empresa): ?>
                            <tr>
                                <td><?= $empresa->getIdempresa() ?></td>
                                <td class="col-titulo"><?= $empresa->getNombre() ?></td>
                                <td><?= $empresa->getCorreo() ?></td>
                                <td><?= $empresa->getDireccion() ?></td>
                                <td class="columnaAcciones">
                                    <a href="index.php?menu=admin-empresas&action=view&id=<?= $empresa->getIdempresa() ?>" 
                                       class="action-btn view-btn" style="text-decoration: none;">Ver Ficha</a>
                                    
                                    <a href="index.php?menu=admin-empresas&action=edit&id=<?= $empresa->getIdempresa() ?>" 
                                       class="action-btn edit-btn" style="text-decoration: none;">Editar</a>
                                    
                                    <form action="index.php?menu=admin-empresas" method="post" class="inline-form" onsubmit="return confirm('¿Confirmar el borrado de la empresa y todos sus datos?');">
                                        <input type="hidden" name="id_empresa" value="<?= $empresa->getIdempresa() ?>">
                                        <button type="submit" class="action-btn delete-btn" name="btnEliminarEmpresa">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="margin-top: 4em;"></div> 
    
    <div class="listaContenedor">
        <div class="seccionEncabezado">
            <h1 class="tituloPendiente">Pendientes de Validación (<?= count($pendientes) ?>)</h1>
        </div>

        <div class="contenedorTabla">
            <table class="tablaAlumnos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendientes)): ?>
                        <tr>
                            <td colspan="4" class="fila-vacia">No hay solicitudes de empresas pendientes.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendientes as $empresa): ?>
                            <tr>
                                <td><?= $empresa->getIdempresa() ?></td>
                                <td class="col-titulo"><?= $empresa->getNombre() ?></td>
                                <td><?= $empresa->getCorreo() ?></td>
                                <td class="columnaAcciones">
                                    <form action="index.php?menu=admin-empresas" method="post" class="inline-form" onsubmit="return confirm('¿Aprobar la cuenta de <?= $empresa->getNombre() ?>?');">
                                        <input type="hidden" name="id_empresa" value="<?= $empresa->getIdempresa() ?>">
                                        <button type="submit" class="action-btn badge-active" name="btnConfirmarEmpresa">✅ Confirmar</button>
                                    </form>
                                    
                                    <form action="index.php?menu=admin-empresas" method="post" class="inline-form" onsubmit="return confirm('¿Rechazar y eliminar la cuenta de <?= $empresa->getNombre() ?>?');">
                                        <input type="hidden" name="id_empresa" value="<?= $empresa->getIdempresa() ?>">
                                        <button type="submit" class="action-btn delete-btn" name="btnRechazarEmpresa">❌ Rechazar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $this->stop() ?>