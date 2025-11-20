<?php
$currentMenu = $_GET['menu'] ?? 'landing';
?>

<aside id="navLateral">
    <div class="headerNavLateral">
        <h3>Opciones del Administrador</h3>
    </div>
    <nav class="menuLateral">
        <ul>
            <li class="itemMenu <?= ($currentMenu === 'admin-dashboard') ? 'active' : '' ?>">
                <a href="?menu=admin-dashboard">
                    Estadisticas
                </a>
            </li>
            <li class="itemMenu <?= ($currentMenu === 'admin-alumnos') ? 'active' : '' ?>">
                <a href="?menu=adminAlumnos">
                    Gestión de Alumnos
                </a>
            </li>
            <li class="itemMenu <?= ($currentMenu === 'admin-empresas' || $currentMenu === 'admin-empresas-add' || $currentMenu === 'admin-empresas-edit' || $currentMenu === 'admin-empresas-ver') ? 'active' : '' ?>">
                <a href="?menu=adminEmpresas">
                    Gestión de Empresas
                </a>
            </li>
        </ul>
    </nav>
</aside>