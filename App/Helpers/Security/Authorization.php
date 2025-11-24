<?php

namespace App\Helpers\Security;

use App\Helpers\Security\TokenSecurity;
use App\Repositories\RepositorioTokens;
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
}