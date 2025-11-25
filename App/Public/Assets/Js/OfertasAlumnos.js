// Archivo: OfertasAlumno.js

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalOferta');
    const loader = document.getElementById('contenidoOfertaLoader');
    const content = document.getElementById('contenidoOfertaBody');
    

    const closeButtons = document.querySelectorAll('.close-button, .btnCancelarEditar');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    });

    
    const botonesVer = document.querySelectorAll('.btn-ver-oferta');
    botonesVer.forEach(btn => {
        btn.addEventListener('click', function() {
            const idOferta = this.getAttribute('data-id');
            abrirModalOferta(idOferta);
        });
    });

    
    const btnPostular = document.getElementById('btnAccionPostular');
    if(btnPostular) {
        btnPostular.addEventListener('click', postularseOferta);
    }
});

let idOfertaActual = null;

async function abrirModalOferta(id) {
    const modal = document.getElementById('modalOferta');
    const loader = document.getElementById('contenidoOfertaLoader');
    const content = document.getElementById('contenidoOfertaBody');
    const feedback = document.getElementById('mensajeFeedback');
    
    modal.style.display = 'flex';
    loader.style.display = 'block';
    content.style.display = 'none';
    feedback.style.display = 'none';
    
    idOfertaActual = id; 

    try {
        
        const response = await fetch(`index.php?menu=alumno-ofertas-api&id=${id}`);
        const data = await response.json();

        if (data.success) {
            rellenarModal(data.oferta, data.yaInscrito);
            loader.style.display = 'none';
            content.style.display = 'block';
        } else {
            alert("Error al cargar oferta: " + data.error);
            modal.style.display = 'none';
        }
    } catch (error) {
        console.error(error);
        alert("Error de conexión.");
        modal.style.display = 'none';
    }
}

function rellenarModal(oferta, yaInscrito) {
    document.getElementById('modalTitulo').textContent = oferta.titulo;
    document.getElementById('modalEmpresa').textContent = "Empresa ID: " + oferta.idEmpresa;
    document.getElementById('modalFecha').textContent = oferta.fecha;
    
    
    document.getElementById('modalDescripcion').textContent = oferta.descripcion; 
    
    document.getElementById('modalDuracion').textContent = oferta.duracion;
    document.getElementById('modalImporte').textContent = oferta.importe;

    const btnPostular = document.getElementById('btnAccionPostular');
    
    if (yaInscrito) {
        btnPostular.textContent = "✅ Ya estás inscrito";
        btnPostular.disabled = true;
        btnPostular.style.backgroundColor = "#ccc";
    } else {
        btnPostular.textContent = "🚀 Postularme ahora";
        btnPostular.disabled = false;
        btnPostular.style.backgroundColor = "";
    }
}

async function postularseOferta() {
    if (!idOfertaActual) return;

    const btn = document.getElementById('btnAccionPostular');
    const feedback = document.getElementById('mensajeFeedback');
    
    btn.disabled = true;
    btn.textContent = "Procesando...";

    try {
        const response = await fetch('index.php?menu=alumno-ofertas-api&accion=postular', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ idOferta: idOfertaActual })
        });

        const data = await response.json();

        feedback.style.display = 'block';
        
        if (data.success) {
            feedback.className = 'mensaje-feedback feedback-exito';
            feedback.textContent = "✅ ¡Te has postulado correctamente!";
            
            btn.textContent = "Inscrito";
            
            
            setTimeout(() => {
               document.getElementById('modalOferta').style.display = 'none';
            }, 2000);
        } else {
            feedback.className = 'mensaje-feedback feedback-error';
            feedback.textContent = "❌ Error: " + (data.error || 'No se pudo postular');
            btn.disabled = false;
            btn.textContent = "Intentar de nuevo";
        }

    } catch (error) {
        console.error(error);
        feedback.style.display = 'block';
        feedback.className = 'mensaje-feedback feedback-error';
        feedback.textContent = "❌ Error de conexión";
        btn.disabled = false;
    }
}