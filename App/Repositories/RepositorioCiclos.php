<?php
namespace App\Repositories;

use App\Repositories\ConexionBD;
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

    
    public function borrar($idCiclo) {
        try {
            $this->bd->beginTransaction();

            $sql = "DELETE FROM oferta_ciclo WHERE id_ciclo = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $idCiclo]);

            $sql2 = "DELETE FROM alumno_ciclo WHERE id_ciclo = :id";
            $stmt2 = $this->bd->prepare($sql2);
            $stmt2->execute([':id' => $idCiclo]);

            
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
        $sql = "SELECT * FROM ciclos WHERE id_ciclo = ?"; 
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([$idCiclo]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$fila) {
            return null;
        }

        return new Ciclo(
            $fila['id_ciclo'],    
            $fila['nombre'],
            $fila['descripcion'],
            $fila['id_familia'] 
        );
    }


    public function obtenerCiclosDeAlumno(int $idAlumno) : array
    {
        $sql = "SELECT c.* FROM ciclos c
                JOIN alumno_ciclo ac ON c.id_ciclo = ac.id_ciclo
                WHERE ac.id_alumno = :id_alumno";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id_alumno' => $idAlumno]);
        
        $ciclos = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
           
            $ciclos[] = new Ciclo(
                $fila['id_ciclo'], 
                $fila['nombre'], 
                $fila['tipo'],
                $fila['id_familia']
            );
        }
        return $ciclos;
    }

}
