<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Lista de Empresas']);
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
                    <th>CIF</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th class="columnaAcciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empresasTotal as $empresa): ?>
                    <tr>
                        <td><?= $empresa->id ?></td>
                        <td><?= $empresa->cif ?></td>
                        <td><?= $empresa->nombre ?></td>
                        <td><?= $empresa->email ?></td>
                        <td><?= $empresa->telefono ?></td>
                        <td class="columnaAcciones">
                            <form action="" method="post">
                                <button type="submit" class="botonAccion botonEditar" name="btnEditar" value="<?= $empresa->id ?>">Editar</button>
                                <button type="submit" class="botonAccion botonEliminar" name="btnBorrar" value="<?= $empresa->id ?>">Eliminar</button>
                                <button type="submit" class="botonAccion botonEditar" name="btnVerFicha" value="<?= $empresa->id ?>">Ver Ficha</button>
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
                    <th>Nombre</th>
                    <th>Email</th>
                    <th class="columnaAccionesPendientes">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendientes as $empresa): ?>
                    <tr>
                        <td><?= $empresa->id ?></td>
                        <td><?= $empresa->nombre ?></td>
                        <td><?= $empresa->username ?></td>
                        <td class="columnaAccionesPendientes">
                            <form action="#" method="post">
                                <button type="submit" class="botonAccion botonEditar" name="btnConfirmar" value="<?= $empresa->id ?>">Confirmar</button>
                                <button type="submit" class="botonAccion botonEliminar" name="btnRechazar" value="<?= $empresa->id ?>">Rechazar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->stop() ?>