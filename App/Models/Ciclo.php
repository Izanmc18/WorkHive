<?php

namespace App\Models;

class Ciclo {
    private $idCiclo;
    private $nombre;
    private $tipo; 
    private $idFamilia;

    public function __construct($idCiclo, $nombre, $tipo, $idFamilia) {
        $this->idCiclo = $idCiclo;
        $this->nombre = $nombre;
        $this->tipo = $tipo; 
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
    public function getTipo() {
        return $this->tipo;
    }
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    public function getIdFamilia() {
        return $this->idFamilia;
    }
    public function setIdFamilia($idFamilia) {
        $this->idFamilia = $idFamilia;
    }
}