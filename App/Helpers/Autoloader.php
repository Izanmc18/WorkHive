<?php
namespace App\Helpers;

spl_autoload_register(function ($clase) {
    
    $prefijo = 'App\\';

    $directorio_base = dirname(__DIR__) . '/';

    $longitud = strlen($prefijo);
    if (strncmp($prefijo, $clase, $longitud) !== 0) {
        return;
    }

    $clase_relativa = substr($clase, $longitud);

    $archivo = $directorio_base . str_replace('\\', '/', $clase_relativa) . '.php';

    if (file_exists($archivo)) {
        require $archivo;
    }
});