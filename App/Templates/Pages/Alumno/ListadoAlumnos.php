<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Listado Alumnos'
]);
?>
<?php $this->start('pageContent') ?>
<div class="listaAlumnosContenedor">
    <div class="cabeceraAlumnos">
        <h1>Listado de alumnos</h1>
        <div class="busquedaAlumnos">
            <input type="text" name="buscar" id="buscar" placeholder="Buscar alumno...">
            <button type="button" id="buscar-btn" class="btnBuscarAlumnos">Buscar</button>
        </div>
    </div>
    <div class="botonesAlumnos">
        <input type="button" id="addAlumno" value="AÑADIR ALUMNO">
        <input type="button" id="cargaMasivaAlumnos" value="CARGA MASIVA">
    </div>
    <div class="tablaAlumnosContenedor">
        <table class="tablaAlumnos">
            <thead>
                <tr>
                    <th>ID <span class="flechaEstatico"></span></th>
                    <th>Nombre <span class="flechaEstatico"></span></th>
                    <th>Primer apellido <span class="flechaEstatico"></span></th>
                    <th>Segundo apellido <span class="flechaEstatico"></span></th>
                    <th>Correo <span class="flechaEstatico"></span></th>
                    <th class="columnaAccionesAlumnos">Acciones</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>
<?php $this->stop() ?>
