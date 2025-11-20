// Archivo: ListarAlumnos.js
// Gestiona: Listado, Ficha, Edición y Creación de Alumnos interactuando con ApiAlumno.php

// --- CONSTANTES DEL DOM ---
const fichaModal = document.getElementById('fichaModal'); 
const editarModal = document.getElementById('editarModal'); 
const crearModal = document.getElementById('crearModal'); 
const modalContent = document.getElementById('modalContent');
const editarContent = document.getElementById('editarContent');
const tablaBody = document.querySelector('.tablaAlumnos tbody');
const btnAddAlumno = document.getElementById('addAlumno'); 

// --- INICIO DEL SCRIPT ---
document.addEventListener('DOMContentLoaded', iniciarScriptListado);

function iniciarScriptListado() {
    configurarListenersModales();
    configurarListenerTabla();
    configurarListenerCrear(); 
}

// --- CONFIGURACIÓN DE MODALES (CIERRE) ---
function configurarListenersModales() {
    document.addEventListener('click', function(evento) {
        // Cierra al pulsar la "X"
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

function cerrarModal(modalId) {
    const targetModal = document.getElementById(modalId);
    if (targetModal) {
        targetModal.style.display = 'none';
    }
}

// --- LÓGICA DE LA TABLA (BOTONES ACCIÓN) ---
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

// ---  LÓGICA DE CREACIÓN DE ALUMNO ---

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

// --- LÓGICA DE BORRADO (ApiAlumno.php -> DELETE) ---

async function borrarAlumno(id) {
    try {
        
        const response = await fetch('index.php?menu=alumno-ficha-api', {
            method: 'DELETE',
            body: JSON.stringify({ idAlumno: id }),
            headers: { 'Content-Type': 'application/json' }
        });
        
        const data = await response.json();
        
        if(data.success) {
            alert('Alumno eliminado correctamente.');
            window.location.reload();
        } else {
            alert('Error al eliminar: ' + (data.error || 'Desconocido'));
        }
    } catch(e) {
        console.error('Error borrando:', e);
        alert('Error de conexión al intentar borrar.');
    }
}


// --- LÓGICA DE FICHA (Lectura) ---

async function mostrarFichaAlumno(id) {
    modalContent.innerHTML = '<p class="text-center p-4">Cargando datos del alumno...</p>';
    fichaModal.style.display = 'flex';

    try {
        const urlApi = 'index.php?menu=alumno-ficha-api&id=' + id;
        const response = await fetch(urlApi);

        if (!response.ok) throw new Error('Error HTTP: ' + response.status);

        const data = await response.json();

        if (data.success) {
            const alumno = data.alumno; 
            modalContent.innerHTML = generarHTMLFicha(alumno, data.estudios);
        } else {
            modalContent.innerHTML = '<p class="text-error">Error: ' + (data.error || 'Desconocido') + '</p>';
        }
    } catch (error) {
        console.error('Error cargar ficha:', error);
        modalContent.innerHTML = '<p class="text-error">Error de red. (' + error.message + ')</p>';
    }
}

function generarHTMLFicha(alumno, estudios) {
    const fotoUrl = alumno.fotoperfil && alumno.fotoperfil.includes('/') 
        ? alumno.fotoperfil 
        : (alumno.fotoperfil ? '/Assets/Images/' + alumno.fotoperfil : '/Assets/Images/placeholderUsers.png');

    const cvUrl = alumno.curriculumurl ? '/Data/' + alumno.curriculumurl : '#';

    let estudiosHtml = '';
    if (estudios && estudios.length > 0) {
        estudios.forEach(estudio => {
            estudiosHtml += `<span class="tag-estudio">${estudio}</span>`;
        });
    } else {
        estudiosHtml = '<p>Ningún ciclo formativo asociado.</p>';
    }

    return `
        <div class="ficha-alumno-header">
            <img src="${fotoUrl}" alt="Foto" class="modal-foto-perfil" onerror="this.src='/Assets/Images/placeholderUsers.png'">
            <h2>${alumno.nombre || ''} ${alumno.apellido1 || ''} ${alumno.apellido2 || ''}</h2>
            <p class="modal-correo">${alumno.correo || ''}</p>
        </div>
        <div class="ficha-alumno-body">
            <h3>Detalles Personales</h3>
            <div class="modal-info-grid">
                <div class="info-item"><strong>ID:</strong> <span>${alumno.idalumno}</span></div>
                <div class="info-item"><strong>Edad:</strong> <span>${alumno.edad || '-'}</span></div>
                <div class="info-item full-width"><strong>Dirección:</strong> <span>${alumno.direccion || '-'}</span></div>
            </div>
            
            <h3>Estudios Asociados</h3>
            <div class="modal-estudios">${estudiosHtml}</div>

            <h3>Documentos</h3>
            <div class="modal-documentos">
                ${alumno.curriculumurl 
                    ? `<a href="${cvUrl}" target="_blank" class="btn-documento">Ver Curriculum (PDF)</a>`
                    : `<span class="btn-documento disabled">Sin Curriculum</span>`
                }
            </div>
        </div>
    `;
}


// --- LÓGICA DE EDICIÓN ---

async function abrirModalEdicion(id) {
    editarContent.innerHTML = '<p class="text-center p-4">Cargando datos...</p>';
    editarModal.style.display = 'flex'; 

    try {
        const urlApi = 'index.php?menu=alumno-ficha-api&id=' + id;
        const response = await fetch(urlApi);

        if (!response.ok) throw new Error('Error HTTP: ' + response.status);

        const data = await response.json();

        if (data.success) {
            editarContent.innerHTML = generarFormularioEdicion(data.alumno, data.estudios);
            
            const formEdicion = document.getElementById('formEdicionAlumno');
            formEdicion.addEventListener('submit', manejarEnvioEdicion);
        } else {
            editarContent.innerHTML = '<p class="text-error">Error: ' + (data.error || 'Desconocido') + '</p>';
        }
    } catch (error) {
        console.error('Error cargar edición:', error);
        editarContent.innerHTML = '<p class="text-error">Error de red. (' + error.message + ')</p>';
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
            mensaje.textContent = 'Cambios guardados.';
            mensaje.style.color = 'green';
            setTimeout(function() {
                cerrarModal('editarModal');
                window.location.reload(); 
            }, 1000);
        } else {
            mensaje.textContent = 'Error: ' + (data.error || 'Desconocido');
            mensaje.style.color = 'red';
        }
    } catch (error) {
        console.error('Error editar:', error);
        mensaje.textContent = 'Error de conexión.';
        mensaje.style.color = 'red';
    }
}

function generarFormularioEdicion(alumno, estudios) {
    
    const fotoUrl = alumno.fotoperfil && alumno.fotoperfil.includes('/') 
        ? alumno.fotoperfil 
        : (alumno.fotoperfil ? '/Assets/Images/' + alumno.fotoperfil : '/Assets/Images/placeholderUsers.png');

    return `
        <h2 class="text-center">Editar Perfil de Alumno</h2>
        <form id="formEdicionAlumno" method="POST" enctype="multipart/form-data" class="editar-alumno-form">
            <input type="hidden" name="idAlumno" value="${alumno.idalumno}">
            <input type="hidden" name="idUsuario" value="${alumno.iduser || ''}"> 
            
            <p id="mensajeEstado" style="text-align: center; font-weight: bold; margin-bottom: 15px;"></p>

            <div class="form-grid">
                <div class="form-column">
                    <h3>Información Personal</h3>
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="${alumno.nombre || ''}" required>

                    <label>Primer Apellido:</label>
                    <input type="text" name="apellido1" value="${alumno.apellido1 || ''}" required>
                    
                    <label>Segundo Apellido:</label>
                    <input type="text" name="apellido2" value="${alumno.apellido2 || ''}">

                    <label>Edad:</label>
                    <input type="number" name="edad" value="${alumno.edad || ''}" min="16" max="99">

                    <label>Dirección:</label>
                    <input type="text" name="direccion" value="${alumno.direccion || ''}">
                </div>

                <div class="form-column">
                    <h3>Archivos y Contacto</h3>
                    <div class="foto-preview">
                        <img src="${fotoUrl}" alt="Foto actual" class="modal-foto-preview">
                        <label>Cambiar Foto:</label>
                        <input type="file" name="fotoPerfil" accept="image/*">
                    </div>
                    
                    <label>Cambiar CV (PDF):</label>
                    <input type="file" name="curriculum" accept=".pdf">
                    
                    <label>Correo:</label>
                    <input type="email" name="correo" value="${alumno.correo || ''}" required>
                    
                    <label>Nueva Contraseña (Opcional):</label>
                    <input type="password" name="contrasena" placeholder="Dejar vacío para mantener">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btnAceptarEditar">Guardar Cambios</button>
                <button type="button" class="btnCancelarEditar" onclick="cerrarModal('editarModal')">Cancelar</button>
            </div>
        </form>
    `;
}