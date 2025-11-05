<?php
class PasswordSecurity {
    public static function encriptar($clave) {
        return password_hash($clave, PASSWORD_BCRYPT);
    }

    public static function verificar($clave, $hashGuardado) {
        return password_verify($clave, $hashGuardado);
    }
}
?>
