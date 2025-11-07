<?php 

echo 'Renderizando landing...<br>';
?>

<?php $this->layout('Layout/Layout', ['title' => 'WorkHive | Find Your Future']) ?>

<?php $this->start('body') ?>
    <main class="landing-container">
        <section class="landing-hero">
            <h1>Bienvenido a WorkHive</h1>
            <p>Descubre oportunidades y conecta con empresas</p>
            <div class="landing-actions">
                <a href="?menu=login" class="btn">Iniciar sesión</a>
                <a href="?menu=regAlumno" class="btn">Registrarse como alumno</a>
                <a href="?menu=regEmpresa" class="btn">Registrarse como empresa</a>
            </div>
        </section>

        <section class="info-section">
            <h2>¿Qué ofrece WorkHive?</h2>
            <ul>
                <li>Postula a ofertas de trabajo, prácticas y becas.</li>
                <li>Empresas pueden buscar y contactar alumnos.</li>
                <li>Gestión de solicitudes y entrevistas.</li>
            </ul>
        </section>

        <section class="perfil-empresa">
            <h2>¿Eres empresa?</h2>
            <p>Encuentra alumnos con talento, publica tus ofertas y forma el equipo perfecto.</p>
            <a href="?menu=regEmpresa" class="btn">Registrar empresa</a>
        </section>

        <section class="perfil-alumno">
            <h2>¿Eres alumno?</h2>
            <p>Accede a oportunidades laborales, prácticas y conecta con empresas líderes.</p>
            <a href="?menu=regAlumno" class="btn">Registrar alumno</a>
        </section>

        <section class="empresas-populares">
            <h2>Empresas más Populares</h2>
            <div class="empresa">
                <h3>Amazon AWS</h3>
                <p>Servicios en la nube para transformar tu empresa y potenciar el crecimiento digital. La tecnología que evoluciona contigo.</p>
            </div>
            <div class="empresa">
                <h3>Nter</h3>
                <p>Consultoría tecnológica nacional. Impulsamos el talento joven y la sostenibilidad para un futuro más inteligente.</p>
            </div>
            <!-- Puedes añadir más empresas destacadas aquí -->
        </section>
    </main>
<?php $this->stop() ?>
