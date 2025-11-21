<?php

namespace App\Repositories;

use App\Repositories\ConexionBD;
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
        
        $sql = "INSERT INTO solicitudes (id_oferta, id_alumno, comentario, estado) 
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
        
        $sql = "SELECT * FROM solicitudes WHERE id_solicitud = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idSolicitud]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) return null;
        
        return new Solicitud(
            $fila['id_solicitud'],
            $fila['id_oferta'],
            $fila['id_alumno'],
            $fila['comentario'],
            $fila['estado'],
            $fila['fecha_solicitud'] 
        );
    }

    public function editar(Solicitud $solicitud) {
        $sql = "UPDATE solicitudes SET comentario = :comentario, estado = :estado WHERE id_solicitud = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':comentario' => $solicitud->getComentario(),
            ':estado'     => $solicitud->getEstado(),
            ':id'         => $solicitud->getIdSolicitud()
        ]);
    }

    public function borrar($idSolicitud) {
        $sql = "DELETE FROM solicitudes WHERE id_solicitud = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $idSolicitud]);
    }

    public function listar() {
        $sql = "SELECT * FROM solicitudes";
        $stmt = $this->bd->query($sql);
        $solicitudes = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $solicitudes[] = new Solicitud(
                $fila['id_solicitud'],
                $fila['id_oferta'],
                $fila['id_alumno'],
                $fila['comentario'],
                $fila['estado'],
                $fila['fecha_solicitud'] 
            );
        }
        return $solicitudes;
    }


    public function contarSolicitudesPorEmpresa($idEmpresa) {
        $sql = "SELECT COUNT(s.id_solicitud) as total
                FROM solicitudes s
                JOIN ofertas o ON s.id_oferta = o.id_oferta
                WHERE o.id_empresa = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idEmpresa]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['total'] : 0;
    }

    
    public function obtenerTendenciaMensual($idEmpresa) {
        $sql = "SELECT 
                    DATE_FORMAT(s.fecha_solicitud, '%Y-%m') as mes, 
                    COUNT(*) as total
                FROM solicitudes s
                JOIN ofertas o ON s.id_oferta = o.id_oferta
                WHERE o.id_empresa = :id
                GROUP BY mes
                ORDER BY mes ASC
                LIMIT 12"; 
                
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idEmpresa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    
    public function obtenerDistribucionPorCiclos($idEmpresa) {
        $sql = "SELECT c.nombre, COUNT(s.id_solicitud) as total
                FROM solicitudes s
                JOIN ofertas o ON s.id_oferta = o.id_oferta
                JOIN oferta_ciclo oc ON o.id_oferta = oc.id_oferta
                JOIN ciclos c ON oc.id_ciclo = c.id_ciclo
                WHERE o.id_empresa = :id
                GROUP BY c.id_ciclo
                ORDER BY total DESC
                LIMIT 5";
                
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idEmpresa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAlumnosPorOferta($idOferta) {
        $sql = "SELECT s.*, a.nombre, a.apellido1, a.apellido2, a.curriculum_url, a.foto_perfil, u.correo
                FROM solicitudes s
                JOIN alumnos a ON s.id_alumno = a.id_alumno
                JOIN usuarios u ON a.id_user = u.id_user
                WHERE s.id_oferta = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idOferta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAceptadosPorOferta($idOferta) {
        $sql = "SELECT a.nombre, a.apellido1, a.apellido2, u.correo
                FROM solicitudes s
                JOIN alumnos a ON s.id_alumno = a.id_alumno
                JOIN usuarios u ON a.id_user = u.id_user
                WHERE s.id_oferta = :id
                AND s.estado = 'aceptada'";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idOferta]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerSolicitudConAlumno($idSolicitud) {
        $sql = "SELECT 
                    s.*, a.nombre, a.apellido1, u.correo, o.titulo, o.id_oferta
                FROM solicitudes s
                JOIN alumnos a ON s.id_alumno = a.id_alumno
                JOIN usuarios u ON a.id_user = u.id_user
                JOIN ofertas o ON s.id_oferta = o.id_oferta
                WHERE s.id_solicitud = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idSolicitud]);
        return $stmt->fetch(\PDO::FETCH_ASSOC); 
    }
    
    
    public function actualizarEstado($idSolicitud, $nuevoEstado) {
        $sql = "UPDATE solicitudes SET estado = :estado WHERE id_solicitud = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id' => $idSolicitud
        ]);
    }
}