<?php
class TokenSecurity {
    private $duracion; 

    public function __construct($duracion = 60) {
        $this->duracion = $duracion; 
    }

    public function generarToken($idUsuario) {
        $token = bin2hex(random_bytes(32));
        $fechaGeneracion = date('Y-m-d H:i:s');
        $fechaExpiracion = date('Y-m-d H:i:s', strtotime("+" . $this->duracion . " minutes"));
        return [
            'idUsuario' => $idUsuario,
            'token' => $token,
            'fechaGeneracion' => $fechaGeneracion,
            'fechaExpiracion' => $fechaExpiracion
        ];
    }

    public function verificarToken($tokenBD, $tokenRecibido) {
        $tokenValido = $tokenBD['token'] === $tokenRecibido;
        $ahora = date('Y-m-d H:i:s');
        $noExpirado = $tokenBD['fechaExpiracion'] > $ahora;
        return $tokenValido && $noExpirado;
    }
}
?>
