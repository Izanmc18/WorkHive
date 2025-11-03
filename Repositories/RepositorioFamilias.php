<?php
require_once __DIR__ . '/../ConexionBD.php';
require_once __DIR__ . '/../Models/Familia.php';

class RepositorioFamilias {
    private $bd;

    public function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public function crear(Familia $familia) {
        $sql = "INSERT INTO familias (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':nombre' => $familia->getNombre(),
            ':descripcion' => $familia->getDescripcion()
        ]);
        $familia->setIdFamilia($this->bd->lastInsertId());
        return $familia;
    }

    public function leer($idFamilia) {
        $sql = "SELECT * FROM familias WHERE id_familia = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idFamilia]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Familia($fila['id_familia'], $fila['nombre'], $fila['descripcion']);
    }

    public function editar(Familia $familia) {
        $sql = "UPDATE familias SET nombre = :nombre, descripcion = :descripcion WHERE id_familia = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':nombre' => $familia->getNombre(),
            ':descripcion' => $familia->getDescripcion(),
            ':id' => $familia->getIdFamilia()
        ]);
    }

    public function borrar($idFamilia) {
        $sql = "DELETE FROM familias WHERE id_familia = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idFamilia]);
    }

    public function listar() {
        $sql = "SELECT * FROM familias";
        $stmt = $this->bd->query($sql);
        $familias = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $familias[] = new Familia($fila['id_familia'], $fila['nombre'], $fila['descripcion']);
        }
        return $familias;
    }
}
