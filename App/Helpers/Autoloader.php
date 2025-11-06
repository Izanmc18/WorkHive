<?php
//namespace App\Helpers;

spl_autoload_register(function ($clase) {

    $clase = str_replace('App\\', '', $clase);
    $clase = str_replace('\\', '/', $clase);

    $fichero = __DIR__ . '/../' . $clase . '.php';

    if (file_exists($fichero)) {
        require_once($fichero);
    } else {
        try {
            throw new \Exception("Clase no encontrada: Se buscó el archivo '$fichero' para la clase '$clase'");
        } catch (\Exception $e) {
            error_log("AUTOLOAD ERROR: " . $e->getMessage());
        }
    }
});