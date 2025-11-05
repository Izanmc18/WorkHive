<?php
namespace App\Models;

class Familia {
    private $idFamilia;
    private $nombre;
    private $descripcion;

    public function __construct($idFamilia = null, $nombre = null, $descripcion = null) {
        $this->idFamilia = $idFamilia;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    public function getIdFamilia() { 
        return $this->idFamilia; 
    }

    public function setIdFamilia($idFamilia) { 
        $this->idFamilia = $idFamilia; 
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
}
