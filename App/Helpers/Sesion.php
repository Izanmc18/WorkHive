<?php

namespace App\Helpers;

use App\Models\Alumno;
use App\Models\Empresa;

class Sesion
{

    public static function iniciar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function establecerSesion($usuario)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['id_usuario'] = $usuario['user']->id;
        $_SESSION['nombre_usuario'] = $usuario['user']->username;
        $_SESSION['token'] = $usuario['token'];

        if ($usuario['user'] instanceof Alumno) {
            $_SESSION['rol'] = "ROL_ALUMNO";
        } else if ($usuario['user'] instanceof Empresa) {
            $_SESSION['rol'] = "ROL_EMPRESA";
        } else {
            $_SESSION['rol'] = "ROL_ADMIN";
        }
    }

    public static function obtenerToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['token'] ?? null;
    }

    public static function obtenerNombreUsuario()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['nombre_usuario'] ?? null;
    }

    public static function obtenerRol()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['rol'] ?? null;
    }

    public static function obtenerIdUsuario()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['id_usuario'] ?? null;
    }

    public static function cerrarSesion()
    {
        unset($_SESSION);
        session_destroy();
    }
}