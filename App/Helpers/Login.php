<?php

namespace App\Helpers;

use App\Helpers\Sesion;

class Login
{

    public static function iniciarSesion($usuario)
    {
        Sesion::establecerSesion($usuario);
    }

    public static function cerrarSesion()
    {
        Sesion::iniciar();
        if (self::estaLogueado()) {
            Sesion::cerrarSesion();
        }
    }

    public static function estaLogueado()
    {
        Sesion::iniciar();

        $logueado = false;

        if (isset($_SESSION['id_usuario'])) {
            $logueado = true;
        }

        return $logueado;
    }
}