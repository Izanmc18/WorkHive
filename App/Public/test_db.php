<?php


// 1. Activamos todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Iniciando Prueba de Base de Datos...</h1>";

try {
    // 🛑 CAMBIO AQUÍ: Cargamos TU autoloader manual, no el de Composer
    // (Asegúrate de que el archivo Helpers/Autoloader.php existe y tiene el código que te di antes)
    if (file_exists(__DIR__ . '/../Helpers/Autoloader.php')) {
        require_once __DIR__ . '/../Helpers/Autoloader.php';
        echo "✅ Autoloader manual cargado correctamente.<br>";
    } else {
        throw new Exception("No se encuentra el archivo Helpers/Autoloader.php");
    }

    // 3. Probamos conexión a BD
    // El autoloader ahora buscará: ../Repositories/ConexionBD.php
    $conexion = \App\Repositories\ConexionBD::getInstancia()->getConexion();
    echo "✅ Conexión a BD exitosa.<br>";

    // 4. Intentamos crear datos dummy
    $repoEmpresas = \App\Repositories\RepositorioEmpresas::getInstancia();
    
    echo "🔄 Intentando crear objetos...<br>";

    // Creamos un usuario de prueba
    $usuario = new \App\Models\Usuario(
        null, 
        'test_v2'.uniqid().'@prueba.com', 
        '1234', 
        false, 
        false
    );

    // Creamos una empresa de prueba
    $empresa = new \App\Models\Empresa(
        null, 
        null, 
        $usuario->getCorreo(), 
        'Empresa Test 2 ' . uniqid(), 
        'Descripción de prueba directa 2', 
        '', 
        'Calle Falsa 2123'
    );

    echo "🔄 Llamando a repositorio->crear()...<br>";

    // 5. Ejecutamos la inserción
    // Pasamos null en el logo porque es una prueba de texto
    $resultado = $repoEmpresas->crear($empresa, $usuario, null);

    if ($resultado) {
        echo "<h2 style='color:green'>✅ ¡ÉXITO TOTAL!</h2>";
        echo "La empresa se ha guardado con ID: " . $resultado->getIdEmpresa();
        echo "<br>El usuario se ha guardado con ID: " . $resultado->getIdUsuario();
    } else {
        echo "<h2 style='color:red'>❌ Falló pero devolvió false/null</h2>";
    }

} catch (PDOException $e) {
    echo "<h2 style='color:red'>❌ ERROR DE BASE DE DATOS (PDO):</h2>";
    echo "<strong>Mensaje:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Código:</strong> " . $e->getCode() . "<br>";
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ ERROR GENERAL:</h2>";
    echo "<strong>Mensaje:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Archivo:</strong> " . $e->getFile() . " en línea " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
