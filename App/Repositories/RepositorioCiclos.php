<?php

namespace App\Repositories;

use App\Models\Ciclo;
use PDO;

class RepositorioCiclos {
    private $bd;
    private static $instancia;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioCiclos();
        }
        return self::$instancia;
    }

    public function crear(Ciclo $ciclo) {
        $sql = "INSERT INTO ciclos (nombre, descripcion, id_familia)
                VALUES (:nombre, :descripcion, :id_familia)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':nombre' => $ciclo->getNombre(),
            ':descripcion' => $ciclo->getDescripcion(),
            ':id_familia' => $ciclo->getIdFamilia()
        ]);
        $ciclo->setIdCiclo($this->bd->lastInsertId());
        return $ciclo;
    }

    public function leer($idCiclo) {
        $sql = "SELECT * FROM ciclos WHERE id_ciclo = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idCiclo]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Ciclo($fila['id_ciclo'], $fila['nombre'], $fila['descripcion'], $fila['id_familia']);
    }

    public function editar(Ciclo $ciclo) {
        $sql = "UPDATE ciclos SET nombre = :nombre, descripcion = :descripcion, id_familia = :id_familia WHERE id_ciclo = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':nombre'    => $ciclo->getNombre(),
            ':descripcion' => $ciclo->getDescripcion(),
            ':id_familia' => $ciclo->getIdFamilia(),
            ':id'         => $ciclo->getIdCiclo()
        ]);
    }

    // Método de borrado robusto con transacciones
    public function borrar($idCiclo) {
        try {
            $this->bd->beginTransaction();

            // Elimina relaciones many-to-many (solo si NO tienes ON DELETE CASCADE en BD)
            $sql = "DELETE FROM oferta_ciclo WHERE idciclo = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $idCiclo]);

            $sql2 = "DELETE FROM alumno_ciclo WHERE idciclo = :id";
            $stmt2 = $this->bd->prepare($sql2);
            $stmt2->execute([':id' => $idCiclo]);

            // Borra el ciclo principal
            $sql3 = "DELETE FROM ciclos WHERE id_ciclo = :id";
            $stmt3 = $this->bd->prepare($sql3);
            $stmt3->execute([':id' => $idCiclo]);

            $this->bd->commit();
            return true;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function listar() {
        $sql = "SELECT * FROM ciclos";
        $stmt = $this->bd->query($sql);
        $ciclos = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ciclos[] = new Ciclo($fila['id_ciclo'], $fila['nombre'], $fila['descripcion'], $fila['id_familia']);
        }
        return $ciclos;
    }

    public function findById(int $idCiclo) {
        $sql = "SELECT * FROM ciclos WHERE idciclo = ?";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([$idCiclo]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$fila) {
            return null;
        }

        return new Ciclo(
            $fila['idciclo'],
            $fila['nombre'],
            $fila['descripcion'],
            $fila['idfamilia']
        );
    }

}
