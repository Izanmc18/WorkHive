<?php
class Recuperacion {
    private $idRecuperacion;
    private $idUsuario;
    private $idToken;
    private $fechaSolicitud;
    private $fechaUso;

    public function __construct($idRecuperacion = null, $idUsuario = null, $idToken = null, $fechaSolicitud = null, $fechaUso = null) {
        $this->idRecuperacion = $idRecuperacion;
        $this->idUsuario = $idUsuario;
        $this->idToken = $idToken;
        $this->fechaSolicitud = $fechaSolicitud;
        $this->fechaUso = $fechaUso;
    }

    public function getIdRecuperacion() { 
        return $this->idRecuperacion; 
    }

    public function setIdRecuperacion($idRecuperacion) { 
        $this->idRecuperacion = $idRecuperacion; 
    }

    public function getIdUsuario() { 
        return $this->idUsuario; 
    }

    public function setIdUsuario($idUsuario) { 
        $this->idUsuario = $idUsuario; 
    }

    public function getIdToken() { 
        return $this->idToken; 
    }

    public function setIdToken($idToken) { 
        $this->idToken = $idToken; 
    }

    public function getFechaSolicitud() { 
        return $this->fechaSolicitud; 
    }

    public function setFechaSolicitud($fechaSolicitud) { 
        $this->fechaSolicitud = $fechaSolicitud; 
    }

    public function getFechaUso() { 
        return $this->fechaUso; 
    }
    public function setFechaUso($fechaUso) { 
        $this->fechaUso = $fechaUso; 
    }
}
