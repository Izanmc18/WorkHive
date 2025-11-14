<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Lista de Empresas'
]);
?>

<?php $this->start('pageContent') ?>
<div class="listaContenedor">
    <div class="seccionEncabezado">
        <form action="" method="post">
            <h1>Listado de Empresas</h1>
            <div class="seccionBusqueda">
                <input type="text" name="buscar" id="buscar" placeholder="Buscar empresa...">
                <button type="submit" id="botonBuscar" class="botonBusqueda">Buscar</button>
            </div>
        </form>
    </div>
    <div class="contenedorBotonAgregar">
        <form action="" method="post">
            <input type="submit" id="agregar" name="btnAgregar" value="AÑADIR EMPRESA">
        </form>
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
                            <form action="" method="post">
                                <button type="submit" class="botonAccion botonEditar" name="btnEditarEmpresa" value="<?= $empresa->getIdempresa() ?>">Editar</button>
                                <button type="submit" class="botonAccion botonEliminar" name="btnEliminarEmpresa" value="<?= $empresa->getIdempresa() ?>">Eliminar</button>
                                <button type="submit" class="botonAccion botonVerFicha" name="btnVerFichaEmpresa" value="<?= $empresa->getIdempresa() ?>">Ver Ficha</button>
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
                            <form action="#" method="post">
                                <button type="submit" class="botonAccion botonConfirmar" name="btnConfirmarEmpresa" value="<?= $empresa->getIdempresa() ?>">Confirmar</button>
                                <button type="submit" class="botonAccion botonEliminar" name="btnRechazarEmpresa" value="<?= $empresa->getIdempresa() ?>">Rechazar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->stop() ?>
