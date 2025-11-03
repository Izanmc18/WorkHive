<?php
class Ciclo {
    private $idCiclo;
    private $nombre;
    private $descripcion;
    private $idFamilia;

    public function __construct($idCiclo = null, $nombre = null, $descripcion = null, $idFamilia = null) {
        $this->idCiclo = $idCiclo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->idFamilia = $idFamilia;
    }

    public function getIdCiclo() { 
        return $this->idCiclo; 
    }

    public function setIdCiclo($idCiclo) { 
        $this->idCiclo = $idCiclo; 
    }

    public function getNombre() { 
        return $this->nombre; 
    }

    public function setNombre($nombre) { 
        $this->nombre = $nombre; 
    }

    public function getDescripcion() { 
        return $this->descripcion; 
    }

    public function setDescripcion($descripcion) { 
        $this->descripcion = $descripcion; 
    }

    public function getIdFamilia() { 
        return $this->idFamilia; 
    }

    public function setIdFamilia($idFamilia) { 
        $this->idFamilia = $idFamilia; 
    }
}
