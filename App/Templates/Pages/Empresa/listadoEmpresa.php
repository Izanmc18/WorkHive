<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Lista de Empresas'
]);
?>

<?php $this->start('pageContent') ?>

<?php if (isset($message)): ?>
    <div class="alert-message"><?= $this->e($message) ?></div>
<?php endif; ?>

<div class="listaContenedor">
    <div class="seccionEncabezado">
        <form action="index.php?menu=admin-empresas" method="get">
            <h1>Listado de Empresas</h1>
            <div class="seccionBusqueda">
                <input type="hidden" name="menu" value="admin-empresas">
                <input type="text" name="buscar" id="buscar" placeholder="Buscar empresa..." value="<?= $_GET['buscar'] ?? '' ?>">
                <button type="submit" id="botonBuscar" class="botonBusqueda">Buscar</button>
            </div>
        </form>
    </div>
    <div class="contenedorBotonAgregar">
        <a href="index.php?menu=admin-empresas&action=add" id="agregar" class="botonBusqueda">AÑADIR EMPRESA</a>
    </div>
    <div class="contenedorTabla">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Correo</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th class="columnaAcciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empresasTotal as $empresa): ?>
                    <tr>
                        <td><?= $empresa->getIdempresa() ?></td>
                        <td><?= $empresa->getCorreo() ?></td>
                        <td><?= $empresa->getNombre() ?></td>
                        <td><?= $empresa->getDireccion() ?></td>
                        <td class="columnaAcciones">
                            
                            <a href="index.php?menu=admin-empresas&action=view&id=<?= $empresa->getIdempresa() ?>" class="botonAccion botonVerFicha">Ver Ficha</a>
                            <a href="index.php?menu=admin-empresas&action=edit&id=<?= $empresa->getIdempresa() ?>" class="botonAccion botonEditar">Editar</a>
                            
                            <form action="index.php?menu=admin-empresas" method="post" style="display:inline;">
                                <input type="hidden" name="id_empresa" value="<?= $empresa->getIdempresa() ?>">
                                <button type="submit" class="botonAccion botonEliminar" name="btnEliminarEmpresa">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="listaContenedor">
    <div class="seccionEncabezado">
        <h1 class="tituloPendiente">Pendiente de confirmación</h1>
    </div>
    <div class="contenedorTabla">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Correo</th>
                    <th>Nombre</th>
                    <th class="columnaAccionesPendientes">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendientes as $empresa): ?>
                    <tr>
                        <td><?= $empresa->getIdempresa() ?></td>
                        <td><?= $empresa->getCorreo() ?></td>
                        <td><?= $empresa->getNombre() ?></td>
                        <td class="columnaAccionesPendientes">
                            <form action="index.php?menu=admin-empresas" method="post" style="display:inline;">
                                <input type="hidden" name="id_empresa" value="<?= $empresa->getIdempresa() ?>">
                                <button type="submit" class="botonAccion botonConfirmar" name="btnConfirmarEmpresa">Confirmar</button>
                                <button type="submit" class="botonAccion botonEliminar" name="btnRechazarEmpresa">Rechazar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->stop() ?>