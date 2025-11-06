<?php

namespace App\Models;

class Familia {
    private $idFamilia;
    private $nombre;

    public function __construct($idFamilia = null, $nombre = null) {
        $this->idFamilia = $idFamilia;
        $this->nombre = $nombre;
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
}
