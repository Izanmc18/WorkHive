<?php
namespace App\Models;

class Usuario {
    private $id;
    private $correo;
    private $clave;
    private $es_admin;
    private $verificado;

    public function __construct($id = null, $correo = null, $clave = null, $es_admin = false, $verificado = false) {
        $this->id = $id;
        $this->correo = $correo;
        $this->clave = $clave;
        $this->es_admin = $es_admin;
        $this->verificado = $verificado;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
    }

    public function getClave() {
        return $this->clave;
    }

    public function setClave($clave) {
        $this->clave = $clave;
    }

    public function isAdmin() {
        return $this->es_admin;
    }

    public function setEsAdmin($es_admin) {
        $this->es_admin = $es_admin;
    }

    public function isVerificado() {
        return $this->verificado;
    }

    public function setVerificado($verificado) {
        $this->verificado = $verificado;
    }
}


