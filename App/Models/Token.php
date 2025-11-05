<?php
namespace App\Models;

class Token {
    private $idToken;
    private $idUsuario;
    private $token;
    private $fechaCreacion;
    private $fechaExpiracion;

    public function __construct($idToken = null, $idUsuario = null, $token = null, $fechaCreacion = null, $fechaExpiracion = null) {
        $this->idToken = $idToken;
        $this->idUsuario = $idUsuario;
        $this->token = $token;
        $this->fechaCreacion = $fechaCreacion;
        $this->fechaExpiracion = $fechaExpiracion;
    }

    public function getIdToken() {
        return $this->idToken; 
    }

    public function setIdToken($idToken) {
        $this->idToken = $idToken; 
    }

    public function getIdUsuario() {
        return $this->idUsuario; 
    }
    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario; 
    }

    public function getToken() {
        return $this->token; 
    }

    public function setToken($token) {
        $this->token = $token; 
    }

    public function getFechaCreacion() {
        return $this->fechaCreacion; 
    }

    public function setFechaCreacion($fechaCreacion) {
        $this->fechaCreacion = $fechaCreacion; 
    }

    public function getFechaExpiracion() {
        return $this->fechaExpiracion; 
    }

    public function setFechaExpiracion($fechaExpiracion) { 
        $this->fechaExpiracion = $fechaExpiracion; 
    }
}
