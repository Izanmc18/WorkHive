<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Panel de Administración'
]);
?>

<?php $this->start('menu') ?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=admin-dashboard" class="active">Dashboard</a></li>
        <li><a href="?menu=admin-empresas">Gestión Empresas</a></li> 
        <li><a href="?menu=admin-alumnos">Gestión Alumnos</a></li>
        <li><a href="?menu=logout" class="btnLogout">Cerrar Sesión</a></li>
    </ul>
<?php $this->stop() ?>

<?php $this->start('pageContent') ?>
<div class="contenedor-dashboard">
    
    <div class="seccion-cabecera">
        <h1>Panel de <span class="resaltado">Administración</span></h1>
        <p class="subtitulo-dashboard">Resumen global y estadísticas clave del portal.</p>
    </div>

    <div class="grid-kpi">
        
        <div class="tarjeta-kpi kpi-ofertas"> 
            <img class="icono-kpi" src="Assets/Images/solicitudesDash.svg" alt="icono-usuarios">
            <div class="contenido-kpi">
                <p class="titulo-kpi">Usuarios Registrados</p>
                <p class="valor-kpi"><?= $totalUsuarios ?? '0' ?></p>
            </div>
        </div>

        <div class="tarjeta-kpi kpi-ofertas"> 
            <img class="icono-kpi" src="Assets/Images/jobOffer.svg" alt="icono-empresas">
            <div class="contenido-kpi">
                <p class="titulo-kpi">Empresas Totales</p>
                <p class="valor-kpi"><?= $totalEmpresas ?? '0' ?></p>
            </div>
        </div>
        
        <div class="tarjeta-kpi kpi-ofertas"> 
            <img class="icono-kpi" src="Assets/Images/placeholderUsers.png" alt="icono-alumnos">
            <div class="contenido-kpi">
                <p class="titulo-kpi">Alumnos Registrados</p>
                <p class="valor-kpi"><?= $totalAlumnos ?? '0' ?></p>
            </div>
        </div>

        <div class="tarjeta-kpi kpi-solicitudes">
            <img class="icono-kpi" src="Assets/Images/warning.svg" alt="icono-pendientes">
            <div class="contenido-kpi">
                <p class="titulo-kpi">Pendientes Validación</p>
                <p class="valor-kpi" style="color: var(--color-danger-bg);"><?= $pendientesEmpresa ?? '0' ?></p>
            </div>
        </div>
        
        <div class="tarjeta-kpi kpi-solicitudes">
            <img class="icono-kpi" src="Assets/Images/warning.svg" alt="icono-ofertas">
            <div class="contenido-kpi">
                <p class="titulo-kpi">Ofertas Totales</p>
                <p class="valor-kpi"><?= $totalOfertas ?? '0' ?></p>
            </div>
        </div>
    </div>

    <div class="fila-graficos">
        <div class="tarjeta-grafico grafico-barras-v">
            <h3 class="titulo-grafico">Distribución de Usuarios (Empresa/Alumno)</h3>
            <div class="contenedor-canvas">
                <canvas id="chartRoles"></canvas>
            </div>
        </div>
        <div class="tarjeta-grafico grafico-linea">
            <h3 class="titulo-grafico">Crecimiento de Registros (Últimos 6 meses)</h3>
            <div class="contenedor-canvas">
                <canvas id="chartCrecimiento"></canvas>
            </div>
        </div>
    </div>
</div>
<?php $this->stop() ?>

<?php $this->start('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const totalEmpresas = <?= json_encode($totalEmpresas ?? 0) ?>;
    const totalAlumnos = <?= json_encode($totalAlumnos ?? 0) ?>;

    const userDistribution = {
        labels: ['Empresas', 'Alumnos'],
        data: [totalEmpresas, totalAlumnos]
    };
    
    const growthData = {
        labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun"],
        data: [50, 65, 80, 75, 90, 100]
    };
</script>

<script src="Assets/Js/EstadisticasAdmin.js"></script>
<?php $this->stop() ?>