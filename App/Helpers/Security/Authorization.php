<?php

namespace App\Helpers\Security;

use App\Helpers\Security\TokenSecurity;
use App\Repositories\RepositorioTokens;
use App\Repositories\RepositorioUsuarios;
use App\Models\Token;

class Authorization
{
    private static $tokenSecurity;

    public static function InicializarToken()
    {
        if (!self::$tokenSecurity) {
            self::$tokenSecurity = new TokenSecurity(60);
        }
    }

    public static function generarToken($usuario)
    {
        self::InicializarToken();

        $datosToken = self::$tokenSecurity->generarToken($usuario->getId());

        $tokenModel = new Token(
            null,
            $datosToken['idUsuario'],
            $datosToken['token'],
            $datosToken['fechaGeneracion'],
            $datosToken['fechaExpiracion']
        );

        $repoTokens = RepositorioTokens::getInstancia();
        $repoTokens->crear($tokenModel);

        return $datosToken['token'];
    }

    public static function validarToken($token)
    {
        self::InicializarToken();

        $repoTokens = RepositorioTokens::getInstancia();
        $tokenBD = $repoTokens->leerPorToken($token);

        if (!$tokenBD) {
            return false;
        }

        return self::$tokenSecurity->verificarToken($tokenBD, $token);
    }

    public static function deleteToken($token)
    {
        $repoTokens = RepositorioTokens::getInstancia();
        return $repoTokens->borrarPorToken($token);
    }

    public static function verificarPermisos($rolesPermitidos = [])
    {
        // 1. Obtener cabeceras
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        // 2. Extraer Token Bearer
        $token = null;
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            self::enviarError(401, 'Token de acceso no proporcionado.');
        }

        // 3. Validar Token en Base de Datos
        $repoTokens = RepositorioTokens::getInstancia();
        $idUsuario = $repoTokens->obtenerUsuarioIdPorToken($token);

        if (!$idUsuario) {
            self::enviarError(401, 'Token inválido o expirado.');
        }

        // 4. Verificar Rol
        $repoUsuarios = RepositorioUsuarios::getInstancia();
        $rolUsuario = $repoUsuarios->obtenerRolUsuario($idUsuario); // Devuelve 'admin', 'alumno', 'empresa'

        // Convertimos a minúsculas para comparar sin problemas
        $rolUsuario = strtolower($rolUsuario);
        $rolesPermitidos = array_map('strtolower', $rolesPermitidos);

        if (!in_array($rolUsuario, $rolesPermitidos)) {
            self::enviarError(403, 'No tienes permisos para realizar esta acción.');
        }

        return $idUsuario;
    }

    private static function enviarError($codigo, $mensaje)
    {
        http_response_code($codigo);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $mensaje]);
        exit; // Detenemos la ejecución aquí mismo
    }
}