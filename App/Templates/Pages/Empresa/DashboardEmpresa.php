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
        <li><a href="?menu=empresa-ofertas">Mis Ofertas</a></li> 
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
    const datosEstado = <?= json_encode($estadoOfertas ?? []) ?>; 
    const datosCiclos = <?= json_encode($ofertasPorCiclo ?? []) ?>;
    const datosTop = <?= json_encode($topOfertas ?? []) ?>;
    const datosTendencia = <?= json_encode($tendenciaMensual ?? []) ?>;
</script>

<script src="Assets/Js/EstadisticasEmpresa.js"></script>
<?php $this->stop() ?>
