<?php

namespace App\Helpers\Security;

use App\Repositories\RepositorioUsuarios;
use App\Repositories\RepositorioEmpresas; 
use App\Helpers\Security\PasswordSecurity;

class Validator
{
    /**
     * Valida credenciales de usuario (Login)
     */
    public static function validarUsuario($correo, $contrasena)
    {
        $repoUsuarios = RepositorioUsuarios::getInstancia();
        $usuario = $repoUsuarios->findByEmail($correo);

        if (!$usuario) {
            error_log("DEBUG AUTH: Correo no encontrado: " . $correo);
            return false; 
        }

        $coincide = PasswordSecurity::verificar($contrasena, $usuario->getClave());
        
        error_log("DEBUG AUTH: Verificación para " . $correo . " resultó: " . ($coincide ? 'EXITOSA' : 'FALLIDA'));

        if ($coincide) {
            return $usuario;
        }

        return false; 
    }

    /**
     *  Valida una empresa pendiente mediante su ID.
     */
    public static function validarEmpresa(int $idEmpresa)
    {
        $repoEmpresas = RepositorioEmpresas::getInstancia();
        
        try {
            return $repoEmpresas->aprobarEmpresa($idEmpresa);
        } catch (\Exception $e) {
            error_log("Error Validator al validar empresa ID $idEmpresa: " . $e->getMessage());
            return false;
        }
    }
}