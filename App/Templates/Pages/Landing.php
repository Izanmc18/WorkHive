<?php
$this->layout('Layout/Layout', [
    'title' => 'WorkHive - Encuentra tu futuro']);
?>

<?php
$this->start('menu');
?>
    <ul class="menuLista">
        <li><a href="?menu=landing">Inicio</a></li>
        <li><a href="?menu=Ofertas">Ofertas</a></li>
        <li><a href="?menu=regEmpresa">Solicitudes</a></li>
        <li><a href="?menu=login">Iniciar sesión</a></li>
        <li><a href="?menu=regRedirect">Registrate</a></li>
    </ul>
<?php
$this->stop();
?>


<?php
$this->start('pageContent');
?>
<section class="banner">
    <h1>WorkHive conecta alumnos y empresas</h1>
    <p>Descubre ofertas de trabajo, prácticas y talento. Tu futuro comienza aquí.</p>
</section>
<section class="destacados">
    <div class="caja empresaBox">
        <h2>¿Eres empresa?</h2>
        <p>Encuentra alumnos con talento, publica tus ofertas y forma el equipo perfecto.</p>
        <a href="?menu=regEmpresa" class="btnRegistro">Registrar empresa</a>
    </div>
    <div class="caja alumnoBox">
        <h2>¿Eres alumno?</h2>
        <p>Accede a oportunidades laborales, prácticas y conecta con empresas líderes.</p>
        <a href="?menu=regAlumno" class="btnRegistro btnAlumno">Registrar alumno</a>
    </div>
</section>
<section class="empresasLista">
    <h2>Empresas más Populares</h2>
    <div class="empresasGrid">
        <div class="empresaCard">
            <img src="Assets/Images/Empresa/logo1.jpg" alt="Logo Empresa A" class="empresaLogo">
            <h3>Google</h3>
            <p>Innovación y tecnología al servicio de millones.</p>
        </div>
        <div class="empresaCard">
            <img src="Assets/Images/Empresa/logo2.jpg" alt="Logo Empresa B" class="empresaLogo">
            <h3>Amazon AWS</h3>
            <p>Servicios integrales en la nube para transformar tu empresa y potenciar el crecimiento digital.</p>
        </div>
        <div class="empresaCard">
            <img src="Assets/Images/Empresa/logo3.jpg" alt="Logo Empresa C" class="empresaLogo">
            <h3>Nter</h3>
            <p>Consultora tecnológica de referencia nacional.</p>
        </div>
    </div>
</section>
<?php
$this->stop();
?>
