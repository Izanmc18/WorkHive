<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Dashboard Empresa'
]);

// Variables que vienen del controlador:
// $totalOfertas (int)
// $totalSolicitudes (int)
// $estadoOfertas (array)
// $ofertasPorCiclo (array)
// $topOfertas (array)
// $tendenciaMensual (array)
?>

<?php $this->start('menu') ?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=empresa-dashboard" class="active">Dashboard</a></li>
        <li><a href="#">Mis Ofertas</a></li> 
        <li><a href="?menu=logout" class="btnLogout">Cerrar Sesión</a></li>
    </ul>
<?php $this->stop() ?>

<?php $this->start('pageContent') ?>
<div class="contenedor-dashboard">
    
    <div class="seccion-cabecera">
        <h1>Dashboard de <span class="resaltado">Estadísticas</span></h1>
        <p class="subtitulo-dashboard">Visualiza el rendimiento de tus ofertas y la actividad de las solicitudes.</p>
    </div>

    <div class="grid-kpi">
        <div class="tarjeta-kpi kpi-ofertas">
            <img class="icono-kpi" src="Assets/Images/jobOffer.svg" alt="icono-ofertas" onerror="this.src='Assets/Images/placeholderUsers.png'">
            <div class="contenido-kpi">
                <p class="titulo-kpi">Ofertas Publicadas</p>
                <p class="valor-kpi"><?= $totalOfertas ?? 0 ?></p>
            </div>
        </div>

        <div class="tarjeta-kpi kpi-solicitudes">
            <img class="icono-kpi" src="Assets/Images/solicitudesDash.svg" alt="icono-solicitudes" onerror="this.src='Assets/Images/placeholderUsers.png'">
            <div class="contenido-kpi">
                <p class="titulo-kpi">Solicitudes Recibidas</p>
                <p class="valor-kpi"><?= $totalSolicitudes ?? 0 ?></p>
            </div>
        </div>
    </div>

    <div class="fila-graficos">
        <div class="tarjeta-grafico grafico-donut">
            <h3 class="titulo-grafico">Estado de las Ofertas</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoEstadoOfertas"></canvas>
            </div>
        </div>

        <div class="tarjeta-grafico grafico-barras-v">
            <h3 class="titulo-grafico">Distribución por Ciclo Formativo</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoCiclos"></canvas>
            </div>
        </div>
    </div>

    <div class="fila-graficos">
        <div class="tarjeta-grafico grafico-barras-h">
            <h3 class="titulo-grafico">Top 5 Ofertas Más Solicitadas</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoTopOfertas"></canvas>
            </div>
        </div>

        <div class="tarjeta-grafico grafico-linea">
            <h3 class="titulo-grafico">Tendencia Mensual de Solicitudes</h3>
            <div class="contenedor-canvas">
                <canvas id="graficoTendenciaMensual"></canvas>
            </div>
        </div>
    </div>

</div>
<?php $this->stop() ?>

<?php $this->start('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Recibimos los datos de PHP y los convertimos a objetos JS
    const datosEstado = <?= json_encode($estadoOfertas ?? []) ?>; 
    const datosCiclos = <?= json_encode($ofertasPorCiclo ?? []) ?>;
    const datosTop = <?= json_encode($topOfertas ?? []) ?>;
    const datosTendencia = <?= json_encode($tendenciaMensual ?? []) ?>;

    document.addEventListener('DOMContentLoaded', () => {
        
        // 1. GRÁFICO DE DONUT (Estado)
        // El array viene como ['1' => 5, '0' => 2], necesitamos transformarlo
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

        // 2. GRÁFICO DE BARRAS VERTICALES (Ciclos)
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
            type: 'bar', // Chart.js v3+ usa 'bar' con indexAxis: 'y' para horizontal
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

        // 4. GRÁFICO DE LÍNEA (Tendencia)
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
</script>
<?php $this->stop() ?>