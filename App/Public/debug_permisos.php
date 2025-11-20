<?php
// Archivo: Public/debug_permisos.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>DIAGNÓSTICO DE PERMISOS DE ARCHIVOS</h1>";
echo "<h2>1. Información del Usuario PHP</h2>";

// El usuario que está ejecutando PHP (ej: www-data)
$user = exec('whoami');
echo "<p>Usuario del servidor PHP: <strong>$user</strong></p>";

// Carpeta de destino (donde falló el mkdir)
$directorioBase = dirname(__DIR__); 
$directorioDestino = $directorioBase . '/Public/Assets/Images/Empresa/';
$directorioDatos = $directorioBase . '/Data/';

echo "<h2>2. Intentando Forzar Creación y Permisos (CHMOD/CHOWN)</h2>";

function fix_permissions($path, $user) {
    if (!file_exists($path)) {
        echo "<p style='color:orange'>Intentando crear ruta: $path</p>";
        if (!mkdir($path, 0777, true)) {
            echo "<p style='color:red'>🛑 FALLO CRÍTICO: No se pudo crear la carpeta. Permiso denegado en la ruta padre.</p>";
            return false;
        }
    }

    echo "<p>Intentando cambiar propiedad (chown) a '$user'...</p>";
    $chown_output = shell_exec("chown -R $user:$user $path");
    echo "Resultado CHOWN: <pre>$chown_output</pre>";

    echo "<p>Intentando cambiar permisos (chmod) a 777...</p>";
    $chmod_output = shell_exec("chmod -R 777 $path");
    echo "Resultado CHMOD: <pre>$chmod_output</pre>";
    
    return true;
}

echo "<h3>-> Diagnóstico en PUBLIC/Assets/Images/Empresa:</h3>";
fix_permissions($directorioDestino, $user);

echo "<h3>-> Diagnóstico en DATA/:</h3>";
fix_permissions($directorioDatos, $user);

echo "<hr><h2>3. Prueba Final</h2>";
if (is_writable($directorioDestino) && is_writable($directorioDatos)) {
    echo "<h1 style='color:green'>✅ ¡ÉXITO! La escritura está habilitada.</h1>";
    echo "<p>Vuelve al registro e intenta crear la empresa de nuevo.</p>";
} else {
    echo "<h1 style='color:red'>❌ FALLO: La carpeta $directorioDestino sigue sin ser escribible.</h1>";
    echo "<p>Esto confirma que la configuración de tu volumen de Docker está sobrescribiendo todos los permisos de Linux. Debes revisar tu archivo <code>docker-compose.yml</code>.</p>";
}