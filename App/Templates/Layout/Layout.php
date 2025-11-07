
<?php $this->start('css') ?>
    <link rel="stylesheet" href="/Public/Css/styles.css"> <!-- Cambia al path real de tu css -->
<?php $this->stop() ?>

<?php $this->start('stack', 'head') ?>
    <!-- Aquí puedes hacer push/unshift para js, meta extra, favicon, etc. -->
<?php $this->stop() ?>

<?php $this->start('header') ?>
    <header>
        <img src="/Public/Images/unnamed.jpg" alt="WorkHive Logo" style="height:80px;">
        <h1>WorkHive</h1>
        <p>Find Your Future</p>
    </header>
<?php $this->stop() ?>

<?php $this->start('body') ?>
    <main>
        <section class="hero">
            <h2>Descubre oportunidades y conecta con empresas</h2>
            <a href="/login" class="btn">Iniciar sesión</a>
            <a href="/register-alumno" class="btn">Registrarse como alumno</a>
            <a href="/register-empresa" class="btn">Registrarse como empresa</a>
        </section>
        <section class="info">
            <h3>¿Qué ofrece WorkHive?</h3>
            <ul>
                <li>Postula a ofertas de trabajo.</li>
                <li>Empresas pueden buscar y contactar alumnos.</li>
                <li>Gestión de solicitudes y entrevistas.</li>
            </ul>
        </section>
    </main>
<?php $this->stop() ?>

<?php $this->start('footer') ?>
    <footer>
        <small>© 2025 WorkHive | Find Your Future</small>
    </footer>
<?php $this->stop() ?>
