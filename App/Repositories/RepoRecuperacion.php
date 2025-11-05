<?php
namespace App\Repositories;

use App\Repositories\ConexionBD;
use App\Models\Recuperacion;

class RepoRecuperacion {
    private $bd;

    public function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public function crear(Recuperacion $recuperacion) {
        $sql = "INSERT INTO recuperacionpassword (iduser, idtoken, fechasolicitud, fechauso) VALUES (:iduser, :idtoken, :fechasolicitud, :fechauso)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':iduser' => $recuperacion->getIdUsuario(),
            ':idtoken' => $recuperacion->getIdToken(),
            ':fechasolicitud' => $recuperacion->getFechaSolicitud(),
            ':fechauso' => $recuperacion->getFechaUso()
        ]);
        $recuperacion->setIdRecuperacion($this->bd->lastInsertId());
        return $recuperacion;
    }

    public function leer($idRecuperacion) {
        $sql = "SELECT * FROM recuperacionpassword WHERE idrecuperacion = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idRecuperacion]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Recuperacion(
            $fila['idrecuperacion'], $fila['iduser'], $fila['idtoken'], $fila['fechasolicitud'], $fila['fechauso']
        );
    }

    public function editar(Recuperacion $recuperacion) {
        $sql = "UPDATE recuperacionpassword SET iduser = :iduser, idtoken = :idtoken, fechasolicitud = :fechasolicitud, fechauso = :fechauso WHERE idrecuperacion = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':iduser' => $recuperacion->getIdUsuario(),
            ':idtoken' => $recuperacion->getIdToken(),
            ':fechasolicitud' => $recuperacion->getFechaSolicitud(),
            ':fechauso' => $recuperacion->getFechaUso(),
            ':id' => $recuperacion->getIdRecuperacion()
        ]);
    }

    public function borrar($idRecuperacion) {
        $sql = "DELETE FROM recuperacionpassword WHERE idrecuperacion = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idRecuperacion]);
    }

    public function listar() {
        $sql = "SELECT * FROM recuperacionpassword";
        $stmt = $this->bd->query($sql);
        $recuperaciones = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $recuperaciones[] = new Recuperacion(
                $fila['idrecuperacion'], $fila['iduser'], $fila['idtoken'], $fila['fechasolicitud'], $fila['fechauso']
            );
        }
        return $recuperaciones;
    }
}
