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

    /**
     * Establece las variables de sesión usando los datos verificados 
     * y el rol específico proporcionado por el AuthController.
     * @param array $datosSesion Array con las claves 'usuario', 'token' y 'rol'.
     */
    public static function establecerSesion($datosSesion)
    {
        
        self::iniciar();

        
        $_SESSION = array(); 
        
        
        $_SESSION['id_usuario'] = $datosSesion['usuario']->getId();
        
        
        $_SESSION['nombre_usuario'] = $datosSesion['usuario']->getCorreo(); 
        
        $_SESSION['token'] = $datosSesion['token'];

        
        $rolBase = $datosSesion['rol'] ?? 'admin';
        $_SESSION['rol'] = strtoupper($rolBase); 
    }

    public static function cerrarSesion()
    {
        self::iniciar();
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function obtenerToken()
    {
        self::iniciar();
        return $_SESSION['token'] ?? null;
    }

    public static function obtenerNombreUsuario()
    {
        self::iniciar();
        return $_SESSION['nombre_usuario'] ?? null;
    }

    public static function obtenerRol()
    {
        self::iniciar();
        
        return $_SESSION['rol'] ?? null; 
    }

    public static function obtenerIdUsuario()
    {
        self::iniciar();
        return $_SESSION['id_usuario'] ?? null;
    }
}