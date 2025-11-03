<?php
require_once __DIR__ . '/../ConexionBD.php';
require_once __DIR__ . '/../Models/Oferta.php';

class RepositorioOfertas {
    private $bd;

    public function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public function crear(Oferta $oferta) {
        $sql = "INSERT INTO ofertas (idempresa, descripcion, fechainicio, fechafin, activa, titulo) 
                VALUES (:idempresa, :descripcion, :fechainicio, :fechafin, :activa, :titulo)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':idempresa' => $oferta->getIdEmpresa(),
            ':descripcion' => $oferta->getDescripcion(),
            ':fechainicio' => $oferta->getFechaInicio(),
            ':fechafin' => $oferta->getFechaFin(),
            ':activa' => $oferta->getActiva(),
            ':titulo' => $oferta->getTitulo()
        ]);
        $oferta->setIdOferta($this->bd->lastInsertId());
        return $oferta;
    }

    public function leer($idOferta) {
        $sql = "SELECT * FROM ofertas WHERE idoferta = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idOferta]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Oferta(
            $fila['idoferta'], $fila['idempresa'], $fila['descripcion'], $fila['fechainicio'],
            $fila['fechafin'], $fila['activa'], $fila['titulo']
        );
    }

    public function editar(Oferta $oferta) {
        $sql = "UPDATE ofertas SET descripcion = :descripcion, fechainicio = :fechainicio, fechafin = :fechafin,
                activa = :activa, titulo = :titulo WHERE idoferta = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':descripcion' => $oferta->getDescripcion(),
            ':fechainicio' => $oferta->getFechaInicio(),
            ':fechafin' => $oferta->getFechaFin(),
            ':activa' => $oferta->getActiva(),
            ':titulo' => $oferta->getTitulo(),
            ':id' => $oferta->getIdOferta()
        ]);
    }

    public function borrar($idOferta) {
        $sql = "DELETE FROM ofertas WHERE idoferta = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idOferta]);
    }

    public function listar() {
        $sql = "SELECT * FROM ofertas";
        $stmt = $this->bd->query($sql);
        $ofertas = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ofertas[] = new Oferta(
                $fila['idoferta'], $fila['idempresa'], $fila['descripcion'], $fila['fechainicio'],
                $fila['fechafin'], $fila['activa'], $fila['titulo']
            );
        }
        return $ofertas;
    }
}
