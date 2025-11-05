<?php 
namespace App\Controllers;
use League\Plates\Engine;

class AuthController {
    private $engine;

    public function __construct($engine = null) {
        
        $this->engine = new Engine(__DIR__ . '/../../Templates/AuthenticationTemplates');
    }

    public function renderLogin() {
        echo $this->engine->render('login');
    }

    public function renderRegAlumno() {
        echo $this->engine->render('register-alumno');
    }

    public function renderRegEmpresa() {
        echo $this->engine->render('register-empresa');
    }

    public function renderRegRedirect() {
        echo $this->engine->render('register-redirect');
    }

    public function renderRecuPassword(){
        echo $this->engine->render('');
    }

    public function renderLogout(){
        echo $this->engine->render('');
    }
}

?>
