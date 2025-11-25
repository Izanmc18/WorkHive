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

            
            $sql = "INSERT INTO ofertas (id_empresa, descripcion, fecha_inicio, fecha_fin, activa, titulo)
                    VALUES (:id_empresa, :descripcion, :fecha_inicio, :fecha_fin, :activa, :titulo)";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':id_empresa' => $oferta->getIdEmpresa(),
                ':descripcion' => $oferta->getDescripcion(),
                ':fecha_inicio' => $oferta->getFechaInicio(),
                ':fecha_fin' => $oferta->getFechaFin(),
                ':activa' => $oferta->getActiva(),
                ':titulo' => $oferta->getTitulo()
            ]);
            
            $idNuevaOferta = $this->bd->lastInsertId();
            $oferta->setIdOferta($idNuevaOferta); 

            
            if (!empty($idsCiclos)) {
                $sqlInsert = "INSERT INTO oferta_ciclo (id_oferta, id_ciclo) VALUES ";
                $values = [];
                $params = [':idoferta' => $idNuevaOferta];
                $counter = 0;
                
                foreach ($idsCiclos as $idCiclo) {
                    $paramIdCiclo = ":idc_{$counter}";
                    $values[] = "(:idoferta, {$paramIdCiclo})"; 
                    $params[$paramIdCiclo] = (int)$idCiclo;
                    $counter++;
                }

                $sqlInsert .= implode(', ', $values);
                $stmtInsert = $this->bd->prepare($sqlInsert);
                $stmtInsert->execute($params); 
            }

            $this->bd->commit();
            return $oferta; 
            
        } catch (\Exception $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            error_log("FATAL EXCEPTION - Creación de Oferta: " . $e->getMessage()); 
            return false;
        }
    }

    public function leer($id_oferta) {
        $sql = "SELECT * FROM ofertas WHERE id_oferta = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id_oferta]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) return null;
        return new Oferta(
            $fila['id_oferta'],
            $fila['id_empresa'],
            $fila['descripcion'],
            $fila['fecha_inicio'],
            $fila['fecha_fin'],
            $fila['activa'],
            $fila['titulo']
        );
    }

    
    public function editarConCiclos(Oferta $oferta, array $idsCiclos) {
        try {
            $this->bd->beginTransaction();

            
            $sql = "UPDATE ofertas SET titulo = :titulo, descripcion = :descripcion, 
                    fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, activa = :activa
                    WHERE id_oferta = :id";
            $stmt = $this->bd->prepare($sql);

            
            $fechaFin = $oferta->getFechaFin();
            
            $fechaFinParam = ($fechaFin === null) ? \PDO::PARAM_NULL : \PDO::PARAM_STR;

            $stmt->bindValue(':titulo', $oferta->getTitulo(), \PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $oferta->getDescripcion(), \PDO::PARAM_STR);
            $stmt->bindValue(':fecha_inicio', $oferta->getFechaInicio(), \PDO::PARAM_STR);
            $stmt->bindValue(':fecha_fin', $fechaFin, $fechaFinParam);
            $stmt->bindValue(':activa', $oferta->getActiva(), \PDO::PARAM_INT);
            $stmt->bindValue(':id', $oferta->getIdOferta(), \PDO::PARAM_INT);
            
            $stmt->execute();

            
            $sqlDel = "DELETE FROM oferta_ciclo WHERE id_oferta = :id";
            $stmtDel = $this->bd->prepare($sqlDel);
            $stmtDel->bindValue(':id', $oferta->getIdOferta(), \PDO::PARAM_INT);
            $stmtDel->execute();

            
            if (!empty($idsCiclos)) {
                $idOfertaValor = $oferta->getIdOferta();
                $sqlInsert = "INSERT INTO oferta_ciclo (id_oferta, id_ciclo) VALUES ";
                $values = [];
                $params = [':idoferta' => $idOfertaValor];
                $counter = 0;
                
                foreach ($idsCiclos as $idCiclo) {
                    $paramIdCiclo = ":idc_{$counter}";
                    
                    $values[] = "(:idoferta, {$paramIdCiclo})"; 
                    $params[$paramIdCiclo] = (int)$idCiclo; 
                    $counter++;
                }

                $sqlInsert .= implode(', ', $values);
                $stmtInsert = $this->bd->prepare($sqlInsert);
                $stmtInsert->execute($params); 
            }

            
            $this->bd->commit();
            return true;
            
        } catch (\Exception $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            error_log("FATAL EXCEPTION - Edición de Oferta: " . $e->getMessage()); 
            return false;
        }
    }
    public function borrar($id_oferta) {
        try {
            $this->bd->beginTransaction();

            
            $sqlRel = "DELETE FROM oferta_ciclo WHERE id_oferta = :id";
            $stmtRel = $this->bd->prepare($sqlRel);
            $stmtRel->execute([':id' => $id_oferta]);

            
            $sql = "DELETE FROM ofertas WHERE id_oferta = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':id' => $id_oferta]);

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
                $fila['id_oferta'],
                $fila['id_empresa'],
                $fila['descripcion'],
                $fila['fecha_inicio'],
                $fila['fecha_fin'],
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

    public function obtenerOfertasConSolicitudes($idEmpresa, $limit = 10, $offset = 0) {
        $sql = "SELECT o.*, COUNT(s.id_solicitud) as num_solicitudes
                FROM ofertas o
                LEFT JOIN solicitudes s ON o.id_oferta = s.id_oferta
                WHERE o.id_empresa = :id
                GROUP BY o.id_oferta
                ORDER BY o.fecha_inicio DESC
                LIMIT :limit OFFSET :offset"; 
                
        $stmt = $this->bd->prepare($sql);
        
        $stmt->bindParam(':id', $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    
    public function contarTodasOfertasPorEmpresa($id_Empresa) {
        $sql = "SELECT COUNT(*) as total FROM ofertas WHERE id_empresa = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id_Empresa]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['total'] : 0;
    }
    
    public function obtenerCiclosDeOferta($idOferta) {
        $sql = "SELECT id_ciclo FROM oferta_ciclo WHERE id_oferta = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $idOferta]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC); 
    }

    public function contarTotalOfertas() {
        $sql = "SELECT COUNT(*) as total FROM ofertas";
        $stmt = $this->bd->query($sql);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $fila ? (int)$fila['total'] : 0;
    }
}
