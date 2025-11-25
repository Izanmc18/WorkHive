// Archivo: ListarAlumnos.js
// Gestiona el CRUD, la búsqueda, carga masiva y modales para la gestión de alumnos.

const fichaModal = document.getElementById('fichaModal'); 
const editarModal = document.getElementById('editarModal'); 
const crearModal = document.getElementById('crearModal'); 
const cargaMasivaModal = document.getElementById('cargaMasivaModal');
const eliminarModal = document.getElementById('eliminarModal'); 

const modalContent = document.getElementById('modalContent');
const editarContent = document.getElementById('editarContent');
const tablaBody = document.querySelector('.tablaAlumnos tbody');

const btnAddAlumno = document.getElementById('addAlumno'); 
const btnCargaMasiva = document.getElementById('cargaMasivaAlumnos'); 
const inputBuscar = document.getElementById('buscar');
const btnBuscar = document.getElementById('buscar-btn');

let idAlumnoParaEliminar = null;

document.addEventListener('DOMContentLoaded', iniciarScriptListado);

function iniciarScriptListado() {
    configurarListenersModales();
    configurarListenerTabla(); 
    configurarListenerCrear(); 
    configurarListenerBusqueda();
    configurarListenerCargaMasiva();
    configurarListenerEliminar();
}

// --- HELPER PARA CABECERAS DE AUTENTICACIÓN ---
function getAuthHeaders() {
    const token = localStorage.getItem('user_token');
    return {
        'Authorization': 'Bearer ' + token
        // Nota: No ponemos 'Content-Type' aquí por defecto porque FormData lo pone solo
    };
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
        if (evento.target === cargaMasivaModal) cerrarModal('cargaMasivaModal');
        if (evento.target === eliminarModal) cerrarModal('eliminarModal');
    });
}

// --- BÚSQUEDA ---

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
        
        const response = await fetch(urlApi, {
            headers: getAuthHeaders() // Añadido header Auth
        });

        if (response.status === 401 || response.status === 403) {
            alert("Sesión expirada o sin permisos.");
            window.location.href = "index.php?menu=logout";
            return;
        }

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
                        <button type="button" class="action-btn delete-btn" data-id="${alumno.idalumno}" data-nombre="${alumno.nombre} ${alumno.apellido1}">Eliminar</button>
                        <button type="button" class="action-btn ver-ficha-btn" data-id="${alumno.idalumno}">Ver Ficha</button>
                    </td>
                </tr>
            `;
        });
    }
    tablaBody.innerHTML = html;
}

// --- ACCIONES TABLA ---

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
    } 
    else if (target.classList.contains('edit-btn')) {
        abrirModalEdicion(idAlumno);
    } 
    else if (target.classList.contains('delete-btn')) {
        const nombreAlumno = target.getAttribute('data-nombre') || 'este alumno';
        abrirModalEliminar(idAlumno, nombreAlumno);
    }
}

// --- ELIMINAR ---

function configurarListenerEliminar() {
    const btnConfirmar = document.getElementById('btnConfirmarEliminar');
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', procesarEliminacion);
    }
}

function abrirModalEliminar(id, nombre) {
    idAlumnoParaEliminar = id;
    const msg = document.getElementById('mensajeEstadoEliminar');
    if (msg) msg.textContent = '';
    
    const spanNombre = document.getElementById('nombreAlumnoEliminar');
    if (spanNombre) spanNombre.textContent = nombre;

    eliminarModal.style.display = 'flex';
}

async function procesarEliminacion() {
    if (!idAlumnoParaEliminar) return;

    const btn = document.getElementById('btnConfirmarEliminar');
    const msg = document.getElementById('mensajeEstadoEliminar');
    
    btn.disabled = true;
    btn.textContent = "Eliminando...";
    msg.textContent = "";

    try {
        const response = await fetch('index.php?menu=alumno-ficha-api', { 
            method: 'DELETE',
            body: JSON.stringify({ idAlumno: idAlumnoParaEliminar }), 
            headers: { 
                'Content-Type': 'application/json',
                ...getAuthHeaders() // Añadimos Bearer
            }
        });

        if (response.status === 401 || response.status === 403) {
            msg.textContent = "Sin permisos.";
            msg.style.color = 'red';
            btn.disabled = false;
            return;
        }

        const data = await response.json();
        
        if (response.ok && data.success) {
            msg.textContent = 'Eliminado con éxito.';
            msg.style.color = 'green';
            setTimeout(() => {
                cerrarModal('eliminarModal');
                window.location.reload(); 
            }, 1000);
        } else {
            msg.textContent = 'Error: ' + (data.error || 'Fallo desconocido.');
            msg.style.color = 'red';
            btn.disabled = false;
            btn.textContent = "Sí, Eliminar";
        }
    } catch(e) {
        console.error('Error de red durante el borrado:', e);
        msg.textContent = 'Error de conexión.';
        msg.style.color = 'red';
        btn.disabled = false;
        btn.textContent = "Sí, Eliminar";
    }
}

// --- CREAR ALUMNO ---

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
        // FormData no necesita Content-Type manual, pero sí Authorization
        const response = await fetch('index.php?menu=alumno-ficha-api', {
            method: 'POST',
            body: formData,
            headers: getAuthHeaders() 
        });
        
        if (response.status === 401 || response.status === 403) {
            mensaje.textContent = "Error: No tienes permiso para crear alumnos.";
            mensaje.style.color = 'red';
            return;
        }

        const data = await response.json();

        if (response.ok && data.success) {
            mensaje.textContent = 'Alumno creado con éxito.';
            mensaje.style.color = 'green';
            setTimeout(function() {
                cerrarModal('crearModal');
                window.location.reload(); 
            }, 1000);
        } else {
            mensaje.textContent = 'Error: ' + (data.error || 'Error desconocido.');
            mensaje.style.color = 'red';
        }
    } catch (error) {
        console.error('Error API Creación:', error);
        mensaje.textContent = 'Error de conexión al servidor.';
        mensaje.style.color = 'red';
    }
}

// --- FICHA ALUMNO ---

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
        const response = await fetch(urlApi, {
            headers: getAuthHeaders() // Auth header
        });

        if (response.status === 401 || response.status === 403) {
            modalContent.innerHTML = '<p class="text-error">No tienes permisos para ver esta ficha.</p>';
            return;
        }

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

// --- EDICIÓN ALUMNO ---

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
        const response = await fetch(urlApi, { headers: getAuthHeaders() });

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
            body: formData,
            headers: getAuthHeaders()
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

// --- CARGA MASIVA ---

function configurarListenerCargaMasiva() {
    if (btnCargaMasiva) {
        btnCargaMasiva.addEventListener('click', () => {
            document.getElementById('archivoCSV').value = '';
            document.getElementById('previewCsvContainer').style.display = 'none';
            document.getElementById('accionesMasivas').style.display = 'none';
            document.getElementById('mensajeEstadoMasivo').textContent = '';
            document.querySelector('#tablaPreview tbody').innerHTML = '';
            cargaMasivaModal.style.display = 'flex';
        });
    }

    const inputCSV = document.getElementById('archivoCSV');
    if (inputCSV) {
        inputCSV.addEventListener('change', procesarArchivoCSV);
    }

    const checkTodos = document.getElementById('checkTodos');
    if (checkTodos) {
        checkTodos.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.check-alumno-csv');
            checkboxes.forEach(chk => chk.checked = this.checked);
        });
    }

    const btnProcesar = document.getElementById('btnProcesarCarga');
    if (btnProcesar) {
        btnProcesar.addEventListener('click', enviarCargaMasiva);
    }
}

function procesarArchivoCSV(evento) {
    const archivo = evento.target.files[0];
    if (!archivo) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const texto = e.target.result;
        mostrarPreviewCSV(texto);
    };
    reader.readAsText(archivo);
}

function mostrarPreviewCSV(csvTexto) {
    const lineas = csvTexto.split(/\r\n|\n/);
    const tbody = document.querySelector('#tablaPreview tbody');
    tbody.innerHTML = '';

    if (lineas.length < 2) {
        alert("El archivo CSV parece estar vacío o solo tiene cabeceras.");
        return;
    }

    const separador = lineas[0].includes(';') ? ';' : ',';
    let contadorValidos = 0;

    for (let i = 1; i < lineas.length; i++) {
        const linea = lineas[i].trim();
        if (!linea) continue;

        const columnas = linea.split(separador);
        if (columnas.length < 4) continue; 

        const nombre = columnas[0] || '';
        const ap1 = columnas[1] || '';
        const ap2 = columnas[2] || '';
        const correo = columnas[3] || '';
        const pass = columnas[4] || '123456'; 
        const edad = columnas[5] || '';
        const dir = columnas[6] || '';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="checkbox" class="check-alumno-csv" checked></td>
            <td>${nombre}</td>
            <td>${ap1} ${ap2}</td>
            <td>${correo}</td>
            <td>${pass}</td>
            <td>${edad}</td>
            <input type="hidden" class="data-nombre" value="${nombre}">
            <input type="hidden" class="data-ap1" value="${ap1}">
            <input type="hidden" class="data-ap2" value="${ap2}">
            <input type="hidden" class="data-correo" value="${correo}">
            <input type="hidden" class="data-pass" value="${pass}">
            <input type="hidden" class="data-edad" value="${edad}">
            <input type="hidden" class="data-dir" value="${dir}">
        `;
        tbody.appendChild(tr);
        contadorValidos++;
    }

    if (contadorValidos > 0) {
        document.getElementById('previewCsvContainer').style.display = 'block';
        document.getElementById('accionesMasivas').style.display = 'flex';
    } else {
        alert("No se encontraron filas válidas en el CSV.");
    }
}

async function enviarCargaMasiva() {
    const filas = document.querySelectorAll('#tablaPreview tbody tr');
    const alumnosParaEnviar = [];

    filas.forEach(tr => {
        const checkbox = tr.querySelector('.check-alumno-csv');
        if (checkbox && checkbox.checked) {
            alumnosParaEnviar.push({
                nombre: tr.querySelector('.data-nombre').value,
                apellido1: tr.querySelector('.data-ap1').value,
                apellido2: tr.querySelector('.data-ap2').value,
                correo: tr.querySelector('.data-correo').value,
                contrasena: tr.querySelector('.data-pass').value,
                edad: tr.querySelector('.data-edad').value,
                direccion: tr.querySelector('.data-dir').value
            });
        }
    });

    if (alumnosParaEnviar.length === 0) {
        alert("No has seleccionado ningún alumno.");
        return;
    }

    const mensaje = document.getElementById('mensajeEstadoMasivo');
    mensaje.textContent = `Enviando ${alumnosParaEnviar.length} alumnos...`;
    mensaje.style.color = 'blue';

    try {
        const response = await fetch('index.php?menu=alumno-ficha-api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...getAuthHeaders() // Auth
            },
            body: JSON.stringify(alumnosParaEnviar) 
        });

        if (response.status === 401 || response.status === 403) {
            mensaje.textContent = "Sin permisos para carga masiva.";
            mensaje.style.color = 'red';
            return;
        }

        const data = await response.json();

        if (response.ok && data.success) {
            mensaje.textContent = data.message || 'Carga masiva completada con éxito.';
            mensaje.style.color = 'green';
            setTimeout(() => {
                cerrarModal('cargaMasivaModal');
                window.location.reload();
            }, 1500);
        } else {
            mensaje.textContent = 'Error: ' + (data.error || 'Ocurrió un error en el servidor.');
            mensaje.style.color = 'red';
            console.error(data.detalles || 'Sin detalles');
        }

    } catch (error) {
        console.error('Error Carga Masiva:', error);
        mensaje.textContent = 'Error de conexión.';
        mensaje.style.color = 'red';
    }
}