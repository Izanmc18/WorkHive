document.addEventListener('DOMContentLoaded', () => {
    
    if (typeof datosEstado === 'undefined' || !document.getElementById('graficoEstadoOfertas')) {
        return;
    }

    // 1. GRÁFICO DE DONUT 
    const activas = datosEstado['1'] || 0;
    const inactivas = datosEstado['0'] || 0;

    new Chart(document.getElementById('graficoEstadoOfertas'), {
        type: 'doughnut',
        data: {
            labels: ['Activas', 'Inactivas/Expiradas'],
            datasets: [{
                data: [activas, inactivas],
                backgroundColor: ['#28a745', '#dc3545'],
                hoverOffset: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. GRÁFICO DE BARRAS VERTICALES (Ciclos más Solicitados)
    const labelsCiclos = datosCiclos.map(item => item.nombre);
    const valoresCiclos = datosCiclos.map(item => item.total);

    new Chart(document.getElementById('graficoCiclos'), {
        type: 'bar',
        data: {
            labels: labelsCiclos,
            datasets: [{
                label: 'Solicitudes',
                data: valoresCiclos,
                backgroundColor: '#007bff',
                borderRadius: 5
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 3. GRÁFICO DE BARRAS HORIZONTALES (Top Ofertas)
    const labelsTop = datosTop.map(item => item.titulo);
    const valoresTop = datosTop.map(item => item.total_solicitudes);

    new Chart(document.getElementById('graficoTopOfertas'), {
        type: 'bar', 
        data: {
            labels: labelsTop,
            datasets: [{
                label: 'Nº Solicitudes',
                data: valoresTop,
                backgroundColor: '#17a2b8',
                borderRadius: 5
            }]
        },
        options: { 
            indexAxis: 'y', 
            responsive: true, 
            maintainAspectRatio: false 
        }
    });

    // 4. GRÁFICO DE LÍNEA (Tendencia Mensual)
    const labelsMeses = datosTendencia.map(item => item.mes);
    const valoresMeses = datosTendencia.map(item => item.total);

    new Chart(document.getElementById('graficoTendenciaMensual'), {
        type: 'line',
        data: {
            labels: labelsMeses,
            datasets: [{
                label: 'Solicitudes por mes',
                data: valoresMeses,
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});