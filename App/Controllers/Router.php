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
        $this->templatesPath = __DIR__ . '/../../Templates';
        $this->engine = new Engine($this->templatesPath);
    }

    public function router()
    {
        if (isset($_GET['menu'])) {
            $menu = $_GET['menu'];
        } else {
            $menu = 'landing';
        }

        switch ($menu) {
            case 'landing':
                $landing = new LandingController();
                $landing->renderLanding();
                break;

            case 'login':
                $Auth = new AuthController();
                $Auth->renderLogin($this->engine);
                break;
            case 'regRedirect':
                $Auth = new AuthController();
                $Auth->renderRegRedirect($this->engine);
                break;
            case 'regEmpresa':
                $Auth = new AuthController();
                $Auth->renderRegEmpresa($this->engine);
                break;
            case 'regAlumno':
                $Auth = new AuthController();
                $Auth->renderRegAlumno($this->engine);
                break;

            case 'admin-alumnos':
                $alumnoManage = new AlumnoController();
                $alumnoManage->renderList($this->engine);
                break;

            case 'admin-empresas':
                $empresaManage = new EmpresaController();
                $empresaManage->renderList($this->engine);
                break;
        }
    }
}