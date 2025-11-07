<?php

namespace App\Controllers;

use League\Plates\Engine;
use App\Controllers\LandingController;
use App\Controllers\AuthController;
use App\Controllers\AlumnoController;

class Router
{
    private $templatesPath;
    private $engine;

    public function __construct()
    {
        // Ajusta la ruta a tus plantillas según tu estructura
        $this->templatesPath = __DIR__ . '/../Templates';
        $this->engine = new Engine($this->templatesPath);
    }

    public function router()
    {
        $menu = isset($_GET['menu']) ? $_GET['menu'] : 'home';

        switch ($menu) {
            case 'home':
                $landing = new LandingController();
                $landing->renderLanding($this->engine);
                break;

            case 'login':
                $auth = new AuthController();
                $auth->renderLogin($this->engine);
                break;

            case 'regRedirect':
                $auth = new AuthController();
                $auth->renderRegRedirect($this->engine);
                break;

            case 'regEmpresa':
                $auth = new AuthController();
                $auth->renderRegEmpresa($this->engine);
                break;

            case 'regAlumno':
                $auth = new AuthController();
                $auth->renderRegAlumno($this->engine);
                break;

            case 'listado':
                $alumnoManage = new AlumnoController();
                $alumnoManage->renderList($this->engine);
                break;
        }
    }
}