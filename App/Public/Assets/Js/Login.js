document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const mensajeError = document.getElementById('mensajeError');

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(loginForm); 

            if (mensajeError) {
                mensajeError.style.display = 'none';
                mensajeError.textContent = '';
            }
            
            try {
                const response = await fetch(loginForm.action, {
                    method: 'POST',
                    body: formData 
                });

                
                const data = await response.json();

                if (response.ok && data.success) {
                    
                    window.location.href = data.redirect; 
                } 
                else {
                    if (mensajeError) {
                        mensajeError.textContent = data.message || 'Error de inicio de sesión';
                        mensajeError.style.display = 'block';
                    }
                }

            } catch (error) {
                console.error('Error de red durante el login:', error);
                if (mensajeError) {
                    mensajeError.textContent = 'No se pudo conectar con el servidor.';
                    mensajeError.style.display = 'block';
                }
            }
        });
    }
});