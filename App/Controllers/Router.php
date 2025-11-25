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
        $rolSesion = Sesion::obtenerRol(); 
        
        $rutasSoloInvitados = ['login', 'regRedirect', 'regEmpresa', 'regAlumno'];

        if ($rolSesion && in_array($menu, $rutasSoloInvitados)) {
            $dashboard = $this->getDashboardRoute($rolSesion);
            header("Location: index.php?menu=$dashboard");
            
        }

        // API 
        $apiAlumnoFile = __DIR__ . '/../Api/ApiAlumno.php';
        if (($menu === 'alumno-ficha-api' || $menu === 'alumno-editar-api')) {
            if (file_exists($apiAlumnoFile)) {
                require $apiAlumnoFile; 
                return;
            }
        }
        
        // RUTAS PÚBLICAS 
        
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


        if (!$rolSesion) {
            header('Location: index.php?menu=login');
            
        }

        //LISTA DE CONTROL DE ACCESO POR ROLES 
        $permisos = [
            'admin-dashboard' => ['ADMIN'],
            'admin-empresas'  => ['ADMIN'],
            'admin-alumnos'   => ['ADMIN'],
            'empresa-dashboard' => ['EMPRESA'],
            'empresa-dashboard'      => ['EMPRESA'],
            'empresa-ofertas'        => ['EMPRESA'], 
            'empresa-eliminar-oferta'=> ['EMPRESA'], 
            'empresa-crear-oferta'   => ['EMPRESA'], 
            'empresa-editar-oferta'  => ['EMPRESA'],
            'empresa-ver-solicitudes' => ['EMPRESA'],
            'empresa-crear-oferta'         => ['EMPRESA'], 
            'empresa-procesar-creacion-oferta' => ['EMPRESA'],
            'empresa-procesar-solicitud' => ['EMPRESA'],
            'empresa-generar-pdf' => ['EMPRESA'],
            'alumno-dashboard' => ['ALUMNO'],
            'alumno-ver-ofertas' => ['ALUMNO'],
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
            case 'alumno-ofertas':
                $alumnoController->verOfertas($this->engine);
                break;

            case 'alumno-ver-oferta':
                $alumnoController->verDetalleOferta($this->engine);
                break;
            case 'alumno-ofertas-api':
                require __DIR__ . '/../Api/ApiOfertas.php';
                return;
            case 'alumno-postular':
                $alumnoController->procesarPostulacion();
                break;
            case 'empresa-dashboard':
                $empresaController->renderDashboard($this->engine); 
                break;

            case 'empresa-ofertas':
                $empresaController->gestionarOfertas($this->engine);
                break;

            case 'empresa-editar-oferta': 
                $empresaController->renderEditarOferta($this->engine);
                break;

            case 'empresa-eliminar-oferta':
                $empresaController->eliminarOferta();
                break;

            case 'empresa-ver-solicitudes':
                $empresaController->verSolicitudesOferta($this->engine);
                break;
            case 'empresa-generar-pdf':
                $empresaController->generarPdfSolicitudes();
                return;
            case 'empresa-procesar-solicitud':
                $empresaController->procesarSolicitud();
                return;
            case 'empresa-crear-oferta':
                $empresaController->renderCrearOferta($this->engine);
                return;
            case 'empresa-procesar-creacion-oferta':
                $empresaController->procesarCreacionOferta();
                return;
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