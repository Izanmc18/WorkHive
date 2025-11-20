<?php

namespace App\Repositories;

use App\Repositories\ConexionBD;
use App\Models\Oferta;
use PDO;

class RepositorioOfertas {
    private $bd;
    private static $instancia;

    private function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new RepositorioOfertas();
        }
        return self::$instancia;
    }

    
    public function crearConCiclos(Oferta $oferta, array $idsCiclos) {
        try {
            $this->bd->beginTransaction();

            $sql = "INSERT INTO ofertas (id_empresa, descripcion, fechainicio, fechafin, activa, titulo)
                    VALUES (:id_empresa, :descripcion, :fechainicio, :fechafin, :activa, :titulo)";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':id_empresa' => $oferta->getIdEmpresa(),
                ':descripcion' => $oferta->getDescripcion(),
                ':fechainicio' => $oferta->getFechaInicio(),
                ':fechafin' => $oferta->getFechaFin(),
                ':activa' => $oferta->getActiva(),
                ':titulo' => $oferta->getTitulo()
            ]);
            $oferta->setIdOferta($this->bd->lastInsertId());

            
            foreach ($idsCiclos as $idCiclo) {
                $sqlCycle = "INSERT INTO oferta_ciclo (idoferta, idciclo) VALUES (:idoferta, :idciclo)";
                $stmtCycle = $this->bd->prepare($sqlCycle);
                $stmtCycle->execute([
                    ':idoferta' => $oferta->getIdOferta(),
                    ':idciclo' => $idCiclo
                ]);
            }

            $this->bd->commit();
            return $oferta;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            throw $e;
        }
    }

    public function leer($idOferta) {
        $sql = "SELECT * FROM ofertas WHERE idoferta = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idOferta]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Oferta(
            $fila['idoferta'],
            $fila['id_empresa'],
            $fila['descripcion'],
            $fila['fechainicio'],
            $fila['fechafin'],
            $fila['activa'],
            $fila['titulo']
        );
    }

    
    public function editarConCiclos(Oferta $oferta, array $idsCiclos) {
        try {
            $this->bd->beginTransaction();

            $sql = "UPDATE ofertas SET descripcion = :descripcion, fechainicio = :fechainicio,
                    fechafin = :fechafin, activa = :activa, titulo = :titulo
                    WHERE idoferta = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':descripcion' => $oferta->getDescripcion(),
                ':fechainicio' => $oferta->getFechaInicio(),
                ':fechafin' => $oferta->getFechaFin(),
                ':activa' => $oferta->getActiva(),
                ':titulo' => $oferta->getTitulo(),
                ':id' => $oferta->getIdOferta()
            ]);

            // Borrar relaciones previas y añadir nuevas
            $sqlDel = "DELETE FROM oferta_ciclo WHERE idoferta = :id";
            $stmtDel = $this->bd->prepare($sqlDel);
            $stmtDel->execute([':id' => $oferta->getIdOferta()]);

            foreach ($idsCiclos as $idCiclo) {
                $sqlCycle = "INSERT INTO oferta_ciclo (idoferta, idciclo) VALUES (:idoferta, :idciclo)";
                $stmtCycle = $this->bd->prepare($sqlCycle);
                $stmtCycle->execute([
                    ':idoferta' => $oferta->getIdOferta(),
                    ':idciclo' => $idCiclo
                ]);
            }

            $this->bd->commit();
            return true;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    
    public function borrar($idOferta) {
        try {
            $this->bd->beginTransaction();

            
            $sqlRel = "DELETE FROM oferta_ciclo WHERE idoferta = :id";
            $stmtRel = $this->bd->prepare($sqlRel);
            $stmtRel->execute([':id' => $idOferta]);

            
            $sql = "DELETE FROM ofertas WHERE idoferta = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $idOferta]);

            $this->bd->commit();
            return true;
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return false;
        }
    }

    public function listar() {
        $sql = "SELECT * FROM ofertas";
        $stmt = $this->bd->query($sql);
        $ofertas = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ofertas[] = new Oferta(
                $fila['idoferta'],
                $fila['id_empresa'],
                $fila['descripcion'],
                $fila['fechainicio'],
                $fila['fechafin'],
                $fila['activa'],
                $fila['titulo']
            );
        }
        return $ofertas;
    }

    public function contarOfertasPorEmpresa($id_empresa) {
        $sql = "SELECT COUNT(*) as total FROM ofertas WHERE id_empresa = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id_empresa]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['total'] : 0;
    }

    
    public function obtenerEstadisticasEstado($id_empresa) {
        $sql = "SELECT activa, COUNT(*) as cantidad 
                FROM ofertas 
                WHERE id_empresa = :id 
                GROUP BY activa";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id_empresa]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 
    }
    
    
    public function obtenerTopOfertas($id_empresa) {
        $sql = "SELECT o.titulo, COUNT(s.id_solicitud) as total_solicitudes
                FROM ofertas o
                LEFT JOIN solicitudes s ON o.id_oferta = s.id_oferta
                WHERE o.id_empresa = :id
                GROUP BY o.id_oferta, o.titulo
                ORDER BY total_solicitudes DESC
                LIMIT 5";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id_empresa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
