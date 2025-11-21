<?php
namespace App\Repositories;

use App\Repositories\ConexionBD;
use App\Models\Token;

class RepositorioTokens {
    
    private static $instancia = null;
    private $bd;

    private function __construct()
    {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia()
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    public function crear(Token $token) {
        $sql = "INSERT INTO tokens (id_user, token, fecha_creacion, fecha_expiracion) VALUES (:id_user, :token, :fecha_creacion, :fecha_expiracion)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':id_user' => $token->getIdUsuario(),
            ':token' => $token->getToken(),
            ':fecha_creacion' => $token->getfechacreacion(),
            ':fecha_expiracion' => $token->getfechaexpiracion()
        ]);
        $token->setIdToken($this->bd->lastInsertId());
        return $token;
    }

    public function leer($idToken) {
        $sql = "SELECT * FROM tokens WHERE idtoken = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idToken]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Token(
            $fila['idtoken'], $fila['id_user'], $fila['token'], $fila['fecha_creacion'], $fila['fecha_expiracion']
        );
    }

    
    public function leerPorToken($tokenString) {
        $sql = "SELECT * FROM tokens WHERE token = :token";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':token' => $tokenString]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$fila) return null;
        
        return new Token(
            $fila['idtoken'], 
            $fila['id_user'], 
            $fila['token'], 
            $fila['fecha_creacion'], 
            $fila['fecha_expiracion']
        );
    }

    public function editar(Token $token) {
        $sql = "UPDATE tokens SET id_user = :id_user, token = :token, fecha_creacion = :fecha_creacion, fecha_expiracion = :fecha_expiracion WHERE idtoken = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':id_user' => $token->getIdUsuario(),
            ':token' => $token->getToken(),
            ':fecha_creacion' => $token->getfechacreacion(),
            ':fecha_expiracion' => $token->getfechaexpiracion(),
            ':id' => $token->getIdToken()
        ]);
    }

    public function borrar($idToken) {
        $sql = "DELETE FROM tokens WHERE idtoken = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idToken]);
    }

    public function borrarPorToken($tokenString) {
        $sql = "DELETE FROM tokens WHERE token = :token";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':token' => $tokenString]);
    }

    public function listar() {
        $sql = "SELECT * FROM tokens";
        $stmt = $this->bd->query($sql);
        $tokens = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tokens[] = new Token(
                $fila['idtoken'], $fila['id_user'], $fila['token'], $fila['fecha_creacion'], $fila['fecha_expiracion']
            );
        }
        return $tokens;
    }
}