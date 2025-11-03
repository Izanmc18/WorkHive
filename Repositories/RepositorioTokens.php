<?php
require_once __DIR__ . '/../ConexionBD.php';
require_once __DIR__ . '/../Models/Token.php';

class RepositorioTokens {
    private $bd;

    public function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public function crear(Token $token) {
        $sql = "INSERT INTO tokens (iduser, token, fechacreacion, fechaexpiracion) VALUES (:iduser, :token, :fechacreacion, :fechaexpiracion)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':iduser' => $token->getIdUsuario(),
            ':token' => $token->getToken(),
            ':fechacreacion' => $token->getFechaCreacion(),
            ':fechaexpiracion' => $token->getFechaExpiracion()
        ]);
        $token->setIdToken($this->bd->lastInsertId());
        return $token;
    }

    public function leer($idToken) {
        $sql = "SELECT * FROM tokens WHERE idtoken = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idToken]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Token(
            $fila['idtoken'], $fila['iduser'], $fila['token'], $fila['fechacreacion'], $fila['fechaexpiracion']
        );
    }

    public function editar(Token $token) {
        $sql = "UPDATE tokens SET iduser = :iduser, token = :token, fechacreacion = :fechacreacion, fechaexpiracion = :fechaexpiracion WHERE idtoken = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':iduser' => $token->getIdUsuario(),
            ':token' => $token->getToken(),
            ':fechacreacion' => $token->getFechaCreacion(),
            ':fechaexpiracion' => $token->getFechaExpiracion(),
            ':id' => $token->getIdToken()
        ]);
    }

    public function borrar($idToken) {
        $sql = "DELETE FROM tokens WHERE idtoken = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idToken]);
    }

    public function listar() {
        $sql = "SELECT * FROM tokens";
        $stmt = $this->bd->query($sql);
        $tokens = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tokens[] = new Token(
                $fila['idtoken'], $fila['iduser'], $fila['token'], $fila['fechacreacion'], $fila['fechaexpiracion']
            );
        }
        return $tokens;
    }
}
