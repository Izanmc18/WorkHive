<?php
namespace App\Helpers\Security;
class PasswordSecurity {
    public static function encriptar($clave) {
        return password_hash($clave, PASSWORD_BCRYPT);
    }

    public static function verificar($clave, $hashGuardado) {
        //var_dump(password_verify($clave, $hashGuardado));
        return password_verify($clave, $hashGuardado);
    }
}
?>