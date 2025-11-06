<?php

namespace App\Repositories;

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

    // Crear oferta con relación a ciclos
    public function crearConCiclos(Oferta $oferta, array $idsCiclos) {
        try {
            $this->bd->beginTransaction();

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

            // Insertar relación oferta-ciclo
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
            $fila['idempresa'],
            $fila['descripcion'],
            $fila['fechainicio'],
            $fila['fechafin'],
            $fila['activa'],
            $fila['titulo']
        );
    }

    // Editar oferta y actualizar ciclos relacionados
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

    // Elimina oferta y relaciones en cascada
    public function borrar($idOferta) {
        try {
            $this->bd->beginTransaction();

            // Eliminar relaciones oferta-ciclo (por si no hay ON DELETE CASCADE)
            $sqlRel = "DELETE FROM oferta_ciclo WHERE idoferta = :id";
            $stmtRel = $this->bd->prepare($sqlRel);
            $stmtRel->execute([':id' => $idOferta]);

            // Eliminar la oferta
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
                $fila['idempresa'],
                $fila['descripcion'],
                $fila['fechainicio'],
                $fila['fechafin'],
                $fila['activa'],
                $fila['titulo']
            );
        }
        return $ofertas;
    }
}
