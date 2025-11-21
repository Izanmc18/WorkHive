// Archivo: ListarAlumnos.js
// Gestiona el CRUD, la búsqueda, y la interacción con modales para la gestión de alumnos.

const fichaModal = document.getElementById('fichaModal'); 
const editarModal = document.getElementById('editarModal'); 
const crearModal = document.getElementById('crearModal'); 
const modalContent = document.getElementById('modalContent');
const editarContent = document.getElementById('editarContent');
const tablaBody = document.querySelector('.tablaAlumnos tbody');
const btnAddAlumno = document.getElementById('addAlumno'); 
const inputBuscar = document.getElementById('buscar');
const btnBuscar = document.getElementById('buscar-btn');

document.addEventListener('DOMContentLoaded', iniciarScriptListado);

function iniciarScriptListado() {
    configurarListenersModales();
    configurarListenerTabla(); 
    configurarListenerCrear(); 
    configurarListenerBusqueda();
}

// --- UTILIDADES DEL MODAL ---

function cerrarModal(modalId) {
    const targetModal = document.getElementById(modalId);
    if (targetModal) {
        targetModal.style.display = 'none';
    }
}
function configurarListenersModales() {
    document.addEventListener('click', function(evento) {
        if (evento.target.classList.contains('close-button')) {
            const targetModalId = evento.target.getAttribute('data-modal');
            cerrarModal(targetModalId);
            return;
        }
        if (evento.target === fichaModal) cerrarModal('fichaModal');
        if (evento.target === editarModal) cerrarModal('editarModal');
        if (evento.target === crearModal) cerrarModal('crearModal'); 
    });
}




function configurarListenerBusqueda() {
    if (btnBuscar && inputBuscar) {
        btnBuscar.addEventListener('click', () => {
            ejecutarBusqueda(inputBuscar.value);
        });
        inputBuscar.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                ejecutarBusqueda(inputBuscar.value);
            }
        });
    }
}

async function ejecutarBusqueda(texto) {
    const textoTrim = texto.trim();
    
    
    if (textoTrim === "") {
        window.location.reload();
        return;
    }
    
    
    const urlApi = 'index.php?menu=alumno-ficha-api&buscar=' + encodeURIComponent(textoTrim);

    try {
        tablaBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 1em;">Buscando alumnos...</td></tr>';
        
        const response = await fetch(urlApi);
        if (!response.ok) throw new Error('Error al buscar alumnos.');

        const alumnos = await response.json();
        cargarAlumnosEnTabla(alumnos); 
        
    } catch (error) {
        console.error('Fallo en la búsqueda:', error);
        tablaBody.innerHTML = '<tr><td colspan="6" style="color: red; text-align: center; padding: 1em;">Error de comunicación con la API.</td></tr>';
    }
}


function cargarAlumnosEnTabla(alumnos) {
    if (!tablaBody) return;

    let html = '';
    if (alumnos.length === 0) {
        html = '<tr><td colspan="6" style="text-align: center; padding: 2em;">No se encontraron alumnos con ese criterio.</td></tr>';
    } else {
        alumnos.forEach(alumno => {
            html += `
                <tr>
                    <td>${alumno.idalumno}</td>
                    <td>${alumno.nombre}</td>
                    <td>${alumno.apellido1}</td>
                    <td>${alumno.apellido2}</td>
                    <td>${alumno.correo}</td>
                    <td class="columnaAccionesAlumnos">
                        <button type="button" class="action-btn edit-btn" data-id="${alumno.idalumno}">Editar</button>
                        <button type="button" class="action-btn delete-btn" data-id="${alumno.idalumno}">Eliminar</button>
                        <button type="button" class="action-btn ver-ficha-btn" data-id="${alumno.idalumno}">Ver Ficha</button>
                    </td>
                </tr>
            `;
        });
    }
    tablaBody.innerHTML = html;
}



function configurarListenerTabla() {
    if (tablaBody) {
        tablaBody.addEventListener('click', manejarClickBoton);
    }
}

function manejarClickBoton(evento) {
    const target = evento.target;
    const idAlumno = target.getAttribute('data-id');
    if (!idAlumno) return;

    if (target.classList.contains('ver-ficha-btn')) {
        mostrarFichaAlumno(idAlumno);
    } else if (target.classList.contains('edit-btn')) {
        abrirModalEdicion(idAlumno);
    } else if (target.classList.contains('delete-btn')) {
        if (confirm('¿Estás seguro de que deseas eliminar al alumno con ID: ' + idAlumno + '?')) {
            borrarAlumno(idAlumno);
        }
    }
}

async function borrarAlumno(id) {
    try {
        const response = await fetch('index.php?menu=alumno-ficha-api', { 
            method: 'DELETE',
            body: JSON.stringify({ idAlumno: id }), 
            headers: { 'Content-Type': 'application/json' }
        });
        const data = await response.json();
        
        if (response.ok && data.success) {
            alert('Alumno eliminado con éxito.');
            window.location.reload(); 
        } else {
            alert('Error al eliminar alumno: ' + (data.error || 'Fallo desconocido.'));
        }
    } catch(e) {
        console.error('Error de red durante el borrado:', e);
        alert('Error de conexión o de red.');
    }
}


function configurarListenerCrear() {
    if (btnAddAlumno) {
        btnAddAlumno.addEventListener('click', () => {
            const form = document.getElementById('formCrearAlumno');
            if(form) form.reset();
            const msg = document.getElementById('mensajeEstadoCrear');
            if(msg) msg.textContent = '';
            
            crearModal.style.display = 'flex';
        });
    }

    const formCrear = document.getElementById('formCrearAlumno');
    if (formCrear) {
        
        formCrear.addEventListener('submit', manejarEnvioCreacion);
    }
}

async function manejarEnvioCreacion(evento) {
    evento.preventDefault();

    const form = evento.target;
    const formData = new FormData(form); 
    const mensaje = document.getElementById('mensajeEstadoCrear');
    mensaje.textContent = 'Creando alumno...';
    mensaje.style.color = 'orange';

    try {
        const response = await fetch('index.php?menu=alumno-ficha-api', {
            method: 'POST',
            body: formData 
        });
        
        const data = await response.json();

        if (response.ok && data.success) {
            mensaje.textContent = 'Alumno creado con éxito.';
            mensaje.style.color = 'green';
            setTimeout(function() {
                cerrarModal('crearModal');
                window.location.reload(); 
            }, 1000);
        } else {
            let errorMsg = data.error || 'Error desconocido.';
            mensaje.textContent = 'Error: ' + errorMsg;
            mensaje.style.color = 'red';
        }
    } catch (error) {
        console.error('Error API Creación:', error);
        mensaje.textContent = 'Error de conexión al servidor.';
        mensaje.style.color = 'red';
    }
}



function generarFichaHTML(alumno) {
    return `
        
       <span class="close-button" data-modal="fichaModal">&times;</span>
           <h2 class="text-xl font-bold mb-4">Ficha del Alumno: ${alumno.nombre} ${alumno.apellido1}</h2>
           
           <div class="modal-body-content">
               
               <div class="columna">
                   <h3>Información Personal</h3>
                   <div class="form-group">
                       <label>Nombre:</label>
                       <p><strong>${alumno.nombre} ${alumno.apellido1} ${alumno.apellido2 || ''}</strong></p>
                   </div>
                   <div class="form-group">
                       <label>Edad:</label>
                       <p>${alumno.edad || 'N/A'}</p>
                   </div>
                   <div class="form-group">
                       <label>Dirección:</label>
                       <p>${alumno.direccion || 'N/A'}</p>
                   </div>
               </div>
               
               <div class="columna">
                   <h3>Contacto y Archivos</h3>
                   <div class="form-group">
                       <label>Correo Electrónico:</label>
                       <p><strong>${alumno.correo}</strong></p>
                   </div>
                   <div class="form-group">
                       <label>Foto de Perfil:</label>
                       <div class="mt-2">
                           <img src="${alumno.fotoperfil}" alt="Foto de Perfil" style="max-width: 100px; border-radius: 50%; border: 2px solid #ccc;"
                                onerror="this.onerror=null;this.src='Assets/Images/placeholderUsers.png';">
                       </div>
                   </div>
                   <div class="form-group">
                       <label>Currículum (CV):</label>
                       <p>
                           ${alumno.curriculumurl ? 
                               `<a href="Assets/Docs/${alumno.curriculumurl}" target="_blank" class="text-blue-500 hover:underline">Ver Documento</a>` : 
                               'No disponible'}
                       </p>
                   </div>
               </div>
           </div>       
   
    `;
}


async function mostrarFichaAlumno(id) {
    modalContent.innerHTML = '<p class="text-center p-4">Cargando ficha del alumno...</p>';
    fichaModal.style.display = 'flex';

    try {
        const urlApi = 'index.php?menu=alumno-ficha-api&id=' + id;
        const response = await fetch(urlApi);

        if (!response.ok) throw new Error('Error HTTP: ' + response.status);

        const data = await response.json();

        if (data.success && data.alumno) {
            modalContent.innerHTML = generarFichaHTML(data.alumno); 
        } else {
            let mensaje = data.error || 'Fallo al obtener datos.';
            modalContent.innerHTML = '<p class="text-error">Error al cargar la ficha: ' + mensaje + '</p>';
        }

    } catch (error) {
        console.error('Error al cargar la ficha:', error);
        modalContent.innerHTML = '<p class="text-error">Error de red o servidor al obtener la ficha.</p>';
    }
}


function generarFormularioEdicion(alumno, estudios) {
    const estudiosHtml = (estudios && estudios.length > 0) 
        ? estudios.map(estudio => `<span class="badge badge-info">${estudio.nombre}</span>`).join(' ')
        : 'Sin estudios asociados.';
    
    return `
        
        <span class="close-button" data-modal="editarModal">&times;</span>
        <form id="formEdicionAlumno" enctype="multipart/form-data" style="padding: 20px 0;">
            <input type="hidden" name="idAlumno" value="${alumno.idalumno}">
            <input type="hidden" name="idUsuario" value="${alumno.iduser}">
            <input type="hidden" name="_method" value="PUT"> 
            
            <h2>Editar Alumno: ${alumno.nombre} ${alumno.apellido1}</h2>
            <div class="modal-body-content">
                
                <div class="columna">
                    <h3>Información Personal</h3>
                    <div class="form-group">
                        <label for="edit_nombre">Nombre: *</label>
                        <input type="text" id="edit_nombre" name="nombre" value="${alumno.nombre}" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_apellido1">Primer Apellido: *</label>
                        <input type="text" id="edit_apellido1" name="apellido1" value="${alumno.apellido1}" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_apellido2">Segundo Apellido:</label>
                        <input type="text" id="edit_apellido2" name="apellido2" value="${alumno.apellido2}">
                    </div>
                    <div class="form-group">
                        <label for="edit_edad">Edad:</label>
                        <input type="number" id="edit_edad" name="edad" value="${alumno.edad}">
                    </div>
                    <div class="form-group">
                        <label for="edit_direccion">Dirección:</label>
                        <input type="text" id="edit_direccion" name="direccion" value="${alumno.direccion}">
                    </div>
                </div>
                
                <div class="columna">
                    <h3>Credenciales y Archivos</h3>
                    <div class="form-group">
                        <label for="edit_correo">Correo Electrónico: *</label>
                        <input type="email" id="edit_correo" name="correo" value="${alumno.correo}" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_contrasena">Nueva Contraseña:</label>
                        <input type="password" id="edit_contrasena" name="contrasena" placeholder="Dejar vacío para no cambiar">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_fotoPerfil">Foto de Perfil:</label>
                        <input type="file" id="edit_fotoPerfil" name="fotoPerfil" accept="image/*">
                        ${alumno.fotoperfil ? `<small class="text-muted">Actual: <a href="${alumno.fotoperfil}" target="_blank">Ver</a></small>` : ''}
                    </div>
                    <div class="form-group">
                        <label for="edit_curriculum">Currículum (PDF):</label>
                        <input type="file" id="edit_curriculum" name="curriculum" accept=".pdf,.doc,.docx">
                        ${alumno.curriculumurl ? `<small class="text-muted">Actual: ${alumno.curriculumurl}</small>` : ''}
                    </div>
                </div>
            </div>
            
            <div id="mensajeEstado" class="mt-4"></div>
            <div class="mt-4 p-2 border-t">
                <strong>Estudios asociados:</strong> ${estudiosHtml}
            </div>
            
            <div class="botones-finales" style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn-enviar">Guardar Cambios</button>
            </div>
        </form>
    
    `;
}

async function abrirModalEdicion(id) {
    editarContent.innerHTML = '<p class="text-center p-4">Cargando datos para editar...</p>';
    editarModal.style.display = 'flex'; 

    try {
        const urlApi = 'index.php?menu=alumno-ficha-api&id=' + id;
        const response = await fetch(urlApi);

        if (!response.ok) throw new Error('Error HTTP: ' + response.status);

        const data = await response.json();

        if (data.success) {
            const alumno = data.alumno;
            editarContent.innerHTML = generarFormularioEdicion(alumno, data.estudios); 
            
            const formEdicion = document.getElementById('formEdicionAlumno');
            formEdicion.addEventListener('submit', manejarEnvioEdicion);
        } else {
            let mensaje = data.error || 'Desconocido';
            editarContent.innerHTML = '<p class="text-error">Error al cargar datos de edición: ' + mensaje + '</p>';
        }

    } catch (error) {
        console.error('Error al cargar la ficha para edición:', error);
        editarContent.innerHTML = '<p class="text-error">Error de red o servidor al obtener la ficha. (' + error.message + ')</p>';
    }
}

async function manejarEnvioEdicion(evento) {
    evento.preventDefault();

    const form = evento.target;
    const formData = new FormData(form);
    
    
    formData.append('_method', 'PUT');

    const mensaje = form.querySelector('#mensajeEstado');
    mensaje.textContent = 'Guardando cambios...';
    mensaje.style.color = 'orange';

    try {
        
        const response = await fetch('index.php?menu=alumno-editar-api', {
            method: 'POST', 
            body: formData 
        });
        
        const data = await response.json();

        if (response.ok && data.success) {
            mensaje.textContent = 'Cambios guardados con éxito.';
            mensaje.style.color = 'green';
            setTimeout(function() {
                cerrarModal('editarModal');
                window.location.reload(); 
            }, 1000);
        } else {
            let errorMsg = data.error || 'Error desconocido al guardar.';
            mensaje.textContent = 'Error al guardar: ' + errorMsg;
            mensaje.style.color = 'red';
        }

    } catch (error) {
        console.error('Error en la llamada a la API de edición:', error);
        mensaje.textContent = 'Error de conexión al servidor.';
        mensaje.style.color = 'red';
    }
}