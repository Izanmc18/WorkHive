document.addEventListener('DOMContentLoaded', () => {
    
    const estiloComputado = getComputedStyle(document.documentElement);
    
    const colorEmpresa = estiloComputado.getPropertyValue('--color-empresa-border').trim();
    const colorAlumno = estiloComputado.getPropertyValue('--color-alumno-border').trim();

    
    if (typeof userDistribution === 'undefined' || !document.getElementById('chartRoles')) {
        return;
    }

    // --- 1. GRÁFICO DE DISTRIBUCIÓN DE ROLES (BARRAS VERTICALES) ---
    new Chart(document.getElementById('chartRoles'), {
        type: 'bar',
        data: {
            labels: userDistribution.labels,
            datasets: [{
                label: 'Usuarios Activos',
                data: userDistribution.data,
                backgroundColor: [colorEmpresa, colorAlumno], 
                borderRadius: 5
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // --- 2. GRÁFICO DE CRECIMIENTO MENSUAL (LÍNEA) ---
    if (typeof growthData !== 'undefined' && document.getElementById('chartCrecimiento')) {
        new Chart(document.getElementById('chartCrecimiento'), {
            type: 'line',
            data: {
                labels: growthData.labels,
                datasets: [{
                    label: 'Registros Totales',
                    data: growthData.data,
                    borderColor: '#28a745', 
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});