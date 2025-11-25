<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Repositories\ConexionBD;
use App\Helpers\Security\PasswordSecurity; 

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

        $hashPassword = PasswordSecurity::encriptar($usuario->getClave());
        
        $usuario->setClave($hashPassword);

        $sql = "INSERT INTO usuarios (correo, password, es_admin, verificado) 
                VALUES (:correo, :password, :es_admin, :verificado)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':correo' => $usuario->getCorreo(),
            ':password' => $hashPassword, 
            ':es_admin' => $usuario->isAdmin() ? 1 : 0,
            ':verificado' => $usuario->isVerificado() ? 1 : 0
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

        $hashLimpio = trim($fila['password']); 
        
        return new Usuario(
            $fila['id_user'], 
            $fila['correo'], 
            $hashLimpio, 
            (bool)$fila['es_admin'], 
            (bool)$fila['verificado']
        );
    }

    public function editar(Usuario $usuario) {
        
        $clave = $usuario->getClave();
        
        if (!empty($clave)) {
            
            $claveEncriptada = PasswordSecurity::encriptar($clave);
            $sql = "UPDATE usuarios 
                    SET correo = :correo, password = :password, es_admin = :es_admin, verificado = :verificado 
                    WHERE id_user = :id";
            $params = [
                ':correo' => $usuario->getCorreo(),
                ':password' => $claveEncriptada,
                ':es_admin' => $usuario->isAdmin() ? 1 : 0,
                ':verificado' => $usuario->isVerificado() ? 1 : 0,
                ':id' => $usuario->getId()
            ];
        } else {
            
            $sql = "UPDATE usuarios 
                    SET correo = :correo, es_admin = :es_admin, verificado = :verificado 
                    WHERE id_user = :id";
            $params = [
                ':correo' => $usuario->getCorreo(),
                ':es_admin' => $usuario->isAdmin() ? 1 : 0,
                ':verificado' => $usuario->isVerificado() ? 1 : 0,
                ':id' => $usuario->getId()
            ];
        }

        $stmt = $this->bd->prepare($sql);
        return $stmt->execute($params);
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

    public function findByEmail($correo) {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        
        
        
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$fila) {
            return null;
        }
        
        $hashLimpio = trim($fila['password']);
        
        error_log("DEBUG REPO: Hash leído (Limpio y Final) para " . $correo . ": [" . $hashLimpio . "]");

        return new Usuario(
            $fila['id_user'],
            $fila['correo'],
            $hashLimpio, 
            (bool)$fila['es_admin'],
            (bool)$fila['verificado']
        );
    }
    

    public function obtenerRolUsuario($idUsuario)
    {
        
        $sqlAdmin = "SELECT es_admin FROM usuarios WHERE id_user = :id";
        $stmtAdmin = $this->bd->prepare($sqlAdmin);
        $stmtAdmin->execute([':id' => $idUsuario]);
        $result = $stmtAdmin->fetch(\PDO::FETCH_ASSOC);
    
        if ($result && $result['es_admin']) {
            return 'admin';
        }
    
        
        $sqlAlumno = "SELECT 1 FROM alumnos WHERE id_user = :id";
        $stmtAlumno = $this->bd->prepare($sqlAlumno);
        $stmtAlumno->execute([':id' => $idUsuario]);
        if ($stmtAlumno->fetch()) {
            return 'alumno';
        }
    
        
        $sqlEmpresa = "SELECT 1 FROM empresas WHERE id_user = :id";
        $stmtEmpresa = $this->bd->prepare($sqlEmpresa);
        $stmtEmpresa->execute([':id' => $idUsuario]);
        if ($stmtEmpresa->fetch()) {
            return 'empresa';
        }
    
        return null; 
    }

    public function contarTotalUsuarios() {
        $sql = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $this->bd->query($sql);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['total'] : 0;
    }
}