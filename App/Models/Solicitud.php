<?php

namespace App\Models;

class Solicitud {
    private $idSolicitud;
    private $idOferta;
    private $idAlumno;
    private $comentario;
    private $estado;

    public function __construct($idSolicitud = null, $idOferta = null, $idAlumno = null, $comentario = null, $estado = null) {
        $this->idSolicitud = $idSolicitud;
        $this->idOferta = $idOferta;
        $this->idAlumno = $idAlumno;
        $this->comentario = $comentario;
        $this->estado = $estado;
    }

    public function getIdSolicitud() {
        return $this->idSolicitud;
    }
    public function setIdSolicitud($idSolicitud) {
        $this->idSolicitud = $idSolicitud;
    }
    public function getIdOferta() {
        return $this->idOferta;
    }
    public function setIdOferta($idOferta) {
        $this->idOferta = $idOferta;
    }
    public function getIdAlumno() {
        return $this->idAlumno;
    }
    public function setIdAlumno($idAlumno) {
        $this->idAlumno = $idAlumno;
    }
    public function getComentario() {
        return $this->comentario;
    }
    public function setComentario($comentario) {
        $this->comentario = $comentario;
    }
    public function getEstado() {
        return $this->estado;
    }
    public function setEstado($estado) {
        $this->estado = $estado;
    }
}
