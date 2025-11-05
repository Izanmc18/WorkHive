<?php $this->layout('base', [
    'title' => 'Registro Empresa | WorkHive'
]) ?>

<?php $this->start('css') ?>
    <link rel="stylesheet" href="/Public/Css/styles-register.css">
<?php $this->stop() ?>

<?php $this->start('header') ?>
    <header>
        <img src="/Public/Images/unnamed.jpg" alt="WorkHive Logo" style="height:80px;">
        <h1>Registro de Empresa</h1>
    </header>
<?php $this->stop() ?>

<?php $this->start('body') ?>
    <main>
        <form class="register-form" method="post" enctype="multipart/form-data" id="registerEmpresa">
            <label for="nombre">Nombre de la empresa</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" required>
            
            <label for="cif">CIF</label>
            <input type="text" id="cif" name="cif" required>
            
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono">
            
            <label for="logo">Logo de la empresa</label>
            <input type="file" id="logo" name="logo" accept="image/*">

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion"></textarea>
            
            <button type="submit" class="btn">Registrarse</button>
        </form>
        <section class="links">
            <a href="/login">¿Ya tienes cuenta? Inicia sesión</a>
        </section>
    </main>
<?php $this->stop() ?>
