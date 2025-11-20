<?php

namespace App\Repositories;

use App\Repositories\ConexionBD;
use App\Models\Familia;

class RepositorioFamilias {
    private $bd;
    private static $instancia;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioFamilias();
        }
        return self::$instancia;
    }

    public function crear(Familia $familia) {
        $sql = "INSERT INTO familias (nombre) VALUES (:nombre)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':nombre' => $familia->getNombre()]);
        $familia->setIdFamilia($this->bd->lastInsertId());
        return $familia;
    }

    public function leer($idFamilia) {
        $sql = "SELECT * FROM familias WHERE id_familia = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idFamilia]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Familia($fila['id_familia'], $fila['nombre']);
    }

    public function editar(Familia $familia) {
        $sql = "UPDATE familias SET nombre = :nombre WHERE id_familia = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':nombre' => $familia->getNombre(), ':id' => $familia->getIdFamilia()]);
    }

    public function borrar($idFamilia) {
        try {
            $this->bd->beginTransaction();

            
            $sqlCiclos = "DELETE FROM ciclos WHERE id_familia = :id";
            $stmtCiclos = $this->bd->prepare($sqlCiclos);
            $stmtCiclos->execute([':id' => $idFamilia]);

            
            $sql = "DELETE FROM familias WHERE id_familia = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $idFamilia]);

            $this->bd->commit();
            return true;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function listar() {
        $sql = "SELECT * FROM familias";
        $stmt = $this->bd->query($sql);
        $familias = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $familias[] = new Familia($fila['id_familia'], $fila['nombre']);
        }
        return $familias;
    }
}
