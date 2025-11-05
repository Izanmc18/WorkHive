<?php
namespace App\Repositories;

require_once __DIR__ . '/../ConexionBD.php';
require_once __DIR__ . '/../Models/Ciclo.php';
use App\Models\Ciclo;
class RepositorioCiclos {
    private $bd;

    public function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public function crear(Ciclo $ciclo) {
        $sql = "INSERT INTO ciclos (nombre, descripcion, id_familia) VALUES (:nombre, :descripcion, :id_familia)";
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
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Ciclo($fila['id_ciclo'], $fila['nombre'], $fila['descripcion'], $fila['id_familia']);
    }

    public function editar(Ciclo $ciclo) {
        $sql = "UPDATE ciclos SET nombre = :nombre, descripcion = :descripcion, id_familia = :id_familia WHERE id_ciclo = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':nombre' => $ciclo->getNombre(),
            ':descripcion' => $ciclo->getDescripcion(),
            ':id_familia' => $ciclo->getIdFamilia(),
            ':id' => $ciclo->getIdCiclo()
        ]);
    }

    public function borrar($idCiclo) {
        $sql = "DELETE FROM ciclos WHERE id_ciclo = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idCiclo]);
    }

    public function listar() {
        $sql = "SELECT * FROM ciclos";
        $stmt = $this->bd->query($sql);
        $ciclos = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $ciclos[] = new Ciclo($fila['id_ciclo'], $fila['nombre'], $fila['descripcion'], $fila['id_familia']);
        }
        return $ciclos;
    }
}
