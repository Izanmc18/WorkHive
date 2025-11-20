<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Listado Alumnos'
]);
// La variable $alumnos viene del AlumnoController
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
                <?php 
                if (isset($alumnos) && is_array($alumnos)):
                    foreach ($alumnos as $alumno): 
                ?>
                    <tr>
                        <td><?= $alumno->getIdAlumno() ?></td>
                        <td><?= $alumno->getNombre() ?></td>
                        <td><?= $alumno->getApellido1() ?></td>
                        <td><?= $alumno->getApellido2() ?></td>
                        <td><?= $alumno->getCorreo() ?></td> 
                        <td class="columnaAccionesAlumnos">
                            <button type="button" class="action-btn edit-btn" data-id="<?= $alumno->getIdAlumno() ?>">Editar</button>
                            <button type="button" class="action-btn delete-btn" data-id="<?= $alumno->getIdAlumno() ?>">Eliminar</button>
                            <button type="button" class="action-btn ver-ficha-btn" data-id="<?= $alumno->getIdAlumno() ?>">Ver Ficha</button>
                        </td>
                    </tr>
                <?php 
                    endforeach;
                endif; 
                ?>
            </tbody>
        </table>
    </div>
</div>

<div id="fichaModal" class="modal">
    <div class="modal-content-wrapper">
        <span class="close-button" data-modal="fichaModal">&times;</span>
        <div id="modalContent">
           </div>
    </div>
</div>

<div id="editarModal" class="modal">
    <div class="modal-content-wrapper modal-content-large">
        <span class="close-button" data-modal="editarModal">&times;</span>
        <div id="editarContent">
            </div>
    </div>
</div>

<div id="crearModal" class="modal">
    <div class="modal-content-wrapper modal-content-large">
        <span class="close-button" data-modal="crearModal">&times;</span>
        <div id="crearContent">
            <h2 class="text-center">Añadir Nuevo Alumno</h2>
            
            <form id="formCrearAlumno" method="POST" enctype="multipart/form-data" class="editar-alumno-form">
                <p id="mensajeEstadoCrear" style="text-align: center; font-weight: bold; margin-bottom: 15px;"></p>

                <div class="form-grid">
                    <div class="form-column">
                        <h3>Información Personal</h3>
                        <label for="new_nombre">Nombre: *</label>
                        <input type="text" id="new_nombre" name="nombre" required>

                        <label for="new_apellido1">Primer Apellido: *</label>
                        <input type="text" id="new_apellido1" name="apellido1" required>
                        
                        <label for="new_apellido2">Segundo Apellido:</label>
                        <input type="text" id="new_apellido2" name="apellido2">

                        <label for="new_edad">Edad:</label>
                        <input type="number" id="new_edad" name="edad" min="16" max="99">

                        <label for="new_direccion">Dirección:</label>
                        <input type="text" id="new_direccion" name="direccion">
                    </div>

                    <div class="form-column">
                        <h3>Credenciales y Archivos</h3>
                        
                        <label for="new_correo">Correo Electrónico: *</label>
                        <input type="email" id="new_correo" name="correo" required>
                        
                        <label for="new_contrasena">Contraseña: *</label>
                        <input type="password" id="new_contrasena" name="contrasena" required>

                        <label for="new_foto">Foto de Perfil:</label>
                        <input type="file" id="new_foto" name="fotoPerfil" accept="image/*">
                        
                        <label for="new_cv">Curriculum (PDF):</label>
                        <input type="file" id="new_cv" name="curriculum" accept=".pdf">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btnAceptarEditar">Crear Alumno</button>
                    <button type="button" class="btnCancelarEditar" onclick="document.getElementById('crearModal').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->stop() ?>

<?php $this->start('js') ?>
<script src="Assets/Js/ListarAlumnos.js"></script> 
<?php $this->stop() ?>