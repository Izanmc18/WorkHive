<?php
namespace App\Controllers;

use League\Plates\Engine;
use App\Controllers\AuthController;
use App\Controllers\AlumnoController;
use App\Controllers\EmpresaController;
use App\Controllers\AdminController;
use App\Controllers\LandingController;
use App\Helpers\Sesion;

class Router
{
    private $templatesPath;
    private $engine;

    public function __construct()
    {
        $this->templatesPath = __DIR__ . '/../Templates';
        $this->engine = new Engine($this->templatesPath);
    }

    public function router()
    {
        
        Sesion::iniciar();
        
        $menu = $_GET['menu'] ?? 'landing'; 
        $rolSesion = Sesion::obtenerRol(); // Devuelve 'ADMIN', 'EMPRESA', 'ALUMNO' o null
        
        $rutasSoloInvitados = ['login', 'regRedirect', 'regEmpresa', 'regAlumno'];

        if ($rolSesion && in_array($menu, $rutasSoloInvitados)) {
            $dashboard = $this->getDashboardRoute($rolSesion);
            header("Location: index.php?menu=$dashboard");
            exit;
        }

        // --- API ---
        $apiAlumnoFile = __DIR__ . '/../Api/ApiAlumno.php';
        if (($menu === 'alumno-ficha-api' || $menu === 'alumno-editar-api')) {
            if (file_exists($apiAlumnoFile)) {
                require $apiAlumnoFile; 
                return;
            }
        }
        
        // --- RUTAS PÚBLICAS  ---
        
        switch ($menu) {
            case 'login':
            case 'regRedirect':
                $auth = new AuthController();
                $this->gestionarPublicAuthRoutes($menu, $auth);
                return;
            
            case 'regEmpresa':
            case 'regAlumno':
                $auth = new AuthController();
                $this->gestionarPublicRegistrationRoutes($menu, $auth); 
                return;

            case 'landing':
                $landing = new LandingController();
                $landing->renderLanding($this->engine);
                return;
                
            case 'logout':
                $auth = new AuthController();
                $auth->logout();
                return;
        }


        // CONTROL DE ACCESO (SI NO HAY SESIÓN)
        // Si llegamos aquí, es una ruta privada. Si no hay rol, fuera.

        if (!$rolSesion) {
            header('Location: index.php?menu=login');
            exit;
        }

        //CONTROL DE ACCESO POR ROLES 
        $permisos = [
            'admin-dashboard' => ['ADMIN'],
            'admin-empresas'  => ['ADMIN'],
            'admin-alumnos'   => ['ADMIN'],
            'empresa-dashboard' => ['EMPRESA'],
            'alumno-dashboard' => ['ALUMNO'],
        ];

        if (array_key_exists($menu, $permisos)) {
            if (!in_array($rolSesion, $permisos[$menu])) {
                $rutaCorrecta = $this->getDashboardRoute($rolSesion);
                header("Location: index.php?menu=$rutaCorrecta");
                
            }
        }

        // --- CONTROLADORES Y VISTAS ---
        
        $alumnoController = new AlumnoController();
        $empresaController = new EmpresaController(); 
        $adminController = new AdminController();
        
        $dashboardRoute = $this->getDashboardRoute($rolSesion); 

        switch ($menu) { 
            case 'alumno-dashboard':
                $alumnoController->renderDashboard($this->engine);
                break;
            
            case 'empresa-dashboard':
                $empresaController->renderDashboard($this->engine); 
                break;

            case 'admin-alumnos':
                $adminController->renderListAlumnos($this->engine);
                break;
            case 'admin-empresas':
                $empresaController->mostrarListado($this->engine);
                break;
            case 'admin-dashboard':
                $adminController->renderDashboard($this->engine); 
                break;
                
            default:
                header("Location: index.php?menu=$dashboardRoute"); 
                exit;
        }
    }

    private function gestionarPublicRegistrationRoutes(string $menu, AuthController $auth)
    {
        switch ($menu) {
            case 'regEmpresa':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $auth->procesarRegistroEmpresa($this->engine);
                } else {
                    $auth->renderRegEmpresa($this->engine);
                }
                break;
            case 'regAlumno':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $auth->procesarRegistroAlumno($this->engine);
                } else {
                    $auth->renderRegAlumno($this->engine);
                }
                break;
        }
    }

    private function getDashboardRoute(string $role)
    {
        switch ($role) {
            case 'ALUMNO': return 'alumno-dashboard';
            case 'EMPRESA': return 'empresa-dashboard';
            case 'ADMIN': return 'admin-dashboard';
            default: return 'landing';
        }
    }

    private function gestionarPublicAuthRoutes(string $menu, AuthController $auth)
    {
        switch ($menu) {
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $auth->procesarLogin($this->engine);
                } else {
                    $auth->renderLogin($this->engine);
                }
                break;
            case 'regRedirect':
                $auth->renderRegRedirect($this->engine);
                break;
        }
    }
}