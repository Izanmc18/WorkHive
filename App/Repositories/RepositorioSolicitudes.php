<?php

namespace App\Repositories;

use App\Models\Solicitud;
use PDO;

class RepositorioSolicitudes {
    private $bd;
    private static $instancia;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioSolicitudes();
        }
        return self::$instancia;
    }

    public function crear(Solicitud $solicitud) {
        $sql = "INSERT INTO solicitudes (idoferta, idalumno, comentario, estado) 
                VALUES (:idoferta, :idalumno, :comentario, :estado)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':idoferta'   => $solicitud->getIdOferta(),
            ':idalumno'   => $solicitud->getIdAlumno(),
            ':comentario' => $solicitud->getComentario(),
            ':estado'     => $solicitud->getEstado()
        ]);
        $solicitud->setIdSolicitud($this->bd->lastInsertId());
        return $solicitud;
    }

    public function leer($idSolicitud) {
        $sql = "SELECT * FROM solicitudes WHERE idsolicitud = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idSolicitud]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Solicitud(
            $fila['idsolicitud'],
            $fila['idoferta'],
            $fila['idalumno'],
            $fila['comentario'],
            $fila['estado']
        );
    }

    public function editar(Solicitud $solicitud) {
        $sql = "UPDATE solicitudes SET comentario = :comentario, estado = :estado WHERE idsolicitud = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':comentario' => $solicitud->getComentario(),
            ':estado'     => $solicitud->getEstado(),
            ':id'         => $solicitud->getIdSolicitud()
        ]);
    }

    public function borrar($idSolicitud) {
        $sql = "DELETE FROM solicitudes WHERE idsolicitud = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idSolicitud]);
    }

    public function listar() {
        $sql = "SELECT * FROM solicitudes";
        $stmt = $this->bd->query($sql);
        $solicitudes = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $solicitudes[] = new Solicitud(
                $fila['idsolicitud'],
                $fila['idoferta'],
                $fila['idalumno'],
                $fila['comentario'],
                $fila['estado']
            );
        }
        return $solicitudes;
    }
}
