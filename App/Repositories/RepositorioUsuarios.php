<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Repositories\ConexionBD;

class RepositorioUsuarios {
    private $bd;
    private static $instancia;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioUsuarios();
        }
        return self::$instancia;
    }

    public function crear(Usuario $usuario) {
        $sql = "INSERT INTO usuarios (correo, password, es_admin, verificado) 
                VALUES (:correo, :password, :es_admin, :verificado)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':correo' => $usuario->getCorreo(),
            ':password' => $usuario->getClave(),
            ':es_admin' => $usuario->isAdmin(),
            ':verificado' => $usuario->isVerificado()
        ]);
        $usuario->setId($this->bd->lastInsertId());
        return $usuario;
    }

    public function leer($idUsuario) {
        $sql = "SELECT * FROM usuarios WHERE id_user = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idUsuario]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$fila) {
            return null;
        }
        return new Usuario(
            $fila['id_user'], 
            $fila['correo'], 
            $fila['password'], 
            (bool)$fila['es_admin'], 
            (bool)$fila['verificado']
        );
    }

    public function editar(Usuario $usuario) {
        $sql = "UPDATE usuarios 
                SET correo = :correo, password = :password, es_admin = :es_admin, verificado = :verificado 
                WHERE id_user = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':correo' => $usuario->getCorreo(),
            ':password' => $usuario->getClave(),
            ':es_admin' => $usuario->isAdmin(),
            ':verificado' => $usuario->isVerificado(),
            ':id' => $usuario->getId()
        ]);
    }

    public function borrar($idUsuario) {
        $sql = "DELETE FROM usuarios WHERE id_user = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idUsuario]);
    }

    public function listar() {
        $sql = "SELECT * FROM usuarios";
        $stmt = $this->bd->query($sql);
        $usuarios = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario(
                $fila['id_user'], 
                $fila['correo'], 
                $fila['password'], 
                (bool)$fila['es_admin'], 
                (bool)$fila['verificado']
            );
        }
        return $usuarios;
    }
}
