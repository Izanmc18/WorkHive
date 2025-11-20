<?php
// Archivo: Public/arreglar.php

// Rutas que necesitamos que existan y sean escribibles
$carpetas = [
    __DIR__ . '/Assets',
    __DIR__ . '/Assets/Images',
    __DIR__ . '/Assets/Images/Empresa', // Para logos
    __DIR__ . '/Assets/Images/Alumno',  // Para fotos perfil
    __DIR__ . '/../Data'                // Para currículums
];

echo "<h1>🛠️ Reparando sistema de archivos...</h1>";

foreach ($carpetas as $ruta) {
    // 1. Si no existe, la creamos
    if (!file_exists($ruta)) {
        if (mkdir($ruta, 0777, true)) {
            echo "<p style='color:green'>✅ Carpeta creada: $ruta</p>";
        } else {
            echo "<p style='color:red'>❌ Error al crear carpeta: $ruta (Revisa permisos de la carpeta padre)</p>";
        }
    } else {
        echo "<p style='color:blue'>ℹ️ La carpeta ya existe: $ruta</p>";
    }

    // 2. Intentamos forzar permisos 777 (Lectura/Escritura total)
    try {
        chmod($ruta, 0777);
        echo "<p style='color:green'>Unlock 🔓 Permisos 777 aplicados a: $ruta</p>";
    } catch (Exception $e) {
        echo "<p style='color:orange'>⚠️ No se pudo hacer chmod (quizás ya tiene permisos o eres Windows): $ruta</p>";
    }
}

echo "<h2>Listo. Intenta registrar la empresa de nuevo.</h2>";
?>