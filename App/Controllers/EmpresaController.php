<?php

namespace App\Controllers;

use App\Repositories\RepositorioEmpresas;
use App\Repositories\RepositorioUsuarios; 
use App\Repositories\RepositorioOfertas;
use App\Repositories\RepositorioSolicitudes;
use App\Helpers\Adapter;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Helpers\Sesion; 
use App\Helpers\Security\Validator; // 🆕 Importar Validator

class EmpresaController
{
    private $repositorio;
    private $repositorioUsuarios;

    public function __construct()
    {
        $this->repositorio = RepositorioEmpresas::getInstancia();
        $this->repositorioUsuarios = RepositorioUsuarios::getInstancia();
    }

    public function renderDashboard($engine) {
        Sesion::iniciar();
        $idUsuario = Sesion::obtenerIdUsuario();

        $empresa = $this->repositorio->obtenerPorIdUsuario($idUsuario);

        if (!$empresa) {
            header('Location: index.php?menu=login');
            exit;
        }

        $idEmpresa = $empresa->getIdEmpresa();
        
        $repoOfertas = RepositorioOfertas::getInstancia();
        $repoSolicitudes = RepositorioSolicitudes::getInstancia();

        $datosDashboard = [
            'totalOfertas' => $repoOfertas->contarOfertasPorEmpresa($idEmpresa),
            'totalSolicitudes' => $repoSolicitudes->contarSolicitudesPorEmpresa($idEmpresa),
            'estadoOfertas' => $repoOfertas->obtenerEstadisticasEstado($idEmpresa), 
            'ofertasPorCiclo' => $repoSolicitudes->obtenerDistribucionPorCiclos($idEmpresa),
            'topOfertas' => $repoOfertas->obtenerTopOfertas($idEmpresa),
            'tendenciaMensual' => $repoSolicitudes->obtenerTendenciaMensual($idEmpresa)
        ];

        echo $engine->render('Pages/Empresa/DashboardEmpresa', $datosDashboard);
    }

    public function mostrarListado($engine)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
            $this->handleGetActions($engine);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostActions($engine);
            return;
        }

        $this->renderizarListadoPrincipal($engine);
    }
    
    private function handleGetActions($engine)
    {
        $action = $_GET['action'];
        $id = (int)($_GET['id'] ?? 0);
        $empresa = $id > 0 ? $this->repositorio->leer($id) : null;

        switch ($action) {
            case 'add':
                echo $engine->render('Pages/Empresa/AñadirEmpresa');
                break;
            case 'edit':
                if ($empresa) {
                    echo $engine->render('Pages/Empresa/EditarEmpresa', ['empresaEdit' => $empresa]);
                } else {
                    $this->renderizarListadoPrincipal($engine);
                }
                break;
            case 'view':
                if ($empresa) {
                    echo $engine->render('Pages/Empresa/VerFichaEmpresa', ['empresaVer' => $empresa]);
                } else {
                    $this->renderizarListadoPrincipal($engine);
                }
                break;
            default:
                $this->renderizarListadoPrincipal($engine);
                break;
        }
    }

    private function handlePostActions($engine)
    {
        if (isset($_POST['btnGuardarEmpresa'])) {
            $this->procesarCreacion($engine);
        } else if (isset($_POST['btnAceptar'])) {
            $this->procesarEdicion($engine);
        } else if (isset($_POST['btnEliminarEmpresa'])) {
            $this->eliminarEmpresa((int)$_POST['id_empresa']);
            header('Location: index.php?menu=admin-empresas');
            exit;
        } else if (isset($_POST['btnConfirmarEmpresa'])) {
            // 🆕 LLAMADA AL VALIDATOR
            Validator::validarEmpresa((int)$_POST['id_empresa']);
            header('Location: index.php?menu=admin-empresas');
            exit;
        } else if (isset($_POST['btnRechazarEmpresa'])) {
            $this->eliminarEmpresa((int)$_POST['id_empresa']);
            header('Location: index.php?menu=admin-empresas');
            exit;
        } else {
            $this->renderizarListadoPrincipal($engine);
        }
    }

    private function procesarCreacion($engine)
    {
        try {
            $datos = $_POST;
            $usuario = new Usuario(
                null,
                $datos['correo'] ?? '',
                $datos['clave'] ?? '', 
                false, 
                true   
            );
            $empresa = Adapter::dtoAEmpresa($datos);
            
            $archivoLogo = (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) 
                ? $_FILES['logo'] 
                : null;
            
            $this->repositorio->crear($empresa, $usuario, $archivoLogo);
            
            header('Location: index.php?menu=admin-empresas');
            exit;

        } catch (\Exception $e) {
            error_log("Error al crear empresa por Admin: " . $e->getMessage());
            echo $engine->render('Pages/Empresa/AñadirEmpresa', ['error' => $e->getMessage()]);
        }
    }

    private function procesarEdicion($engine)
    {
        $idEmpresa = (int)$_POST['id_empresa'];
        $idUsuario = (int)$_POST['id_user'];

        $empresa = $this->repositorio->leer($idEmpresa);
        $usuario = $this->repositorioUsuarios->leer($idUsuario);
        
        if (!$empresa || !$usuario) {
            $this->renderizarListadoPrincipal($engine);
            return;
        }

        try {
            $empresa->setNombre($_POST['nombre']);
            $empresa->setDescripcion($_POST['descripcion']);
            $empresa->setDireccion($_POST['direccion']);
            
            $usuario->setCorreo($_POST['correo']);

            $archivoLogo = (isset($_FILES['nuevoLogo']) && $_FILES['nuevoLogo']['error'] === UPLOAD_ERR_OK) 
                ? $_FILES['nuevoLogo'] 
                : null;
            
            $this->repositorio->editar($empresa, $usuario, $archivoLogo);

            header('Location: index.php?menu=admin-empresas');
            exit;

        } catch (\Exception $e) {
            error_log("Error al editar empresa: " . $e->getMessage());
            echo $engine->render('Pages/Empresa/EditarEmpresa', ['empresaEdit' => $empresa, 'error' => $e->getMessage()]);
        }
    }
    
    private function eliminarEmpresa($id) 
    { 
        try {
            $this->repositorio->borrar($id);
        } catch (\Exception $e) {
            error_log("Error al eliminar empresa ID $id: " . $e->getMessage());
        }
    }
    
    

    private function renderizarListadoPrincipal($engine) 
    { 
        $empresasTodas = $this->repositorio->listarEmpresasValidadas(); 
        $empresasDTO = Adapter::todasEmpresasADTO($empresasTodas);
        
        $pendientesModelo = $this->repositorio->listarEmpresasPendientes();
        $pendientesDTO = Adapter::todasEmpresasADTO($pendientesModelo);

        echo $engine->render('Pages/Empresa/ListadoEmpresa', [
            'empresasTotal' => $empresasDTO, 
            'pendientes' => $pendientesDTO
        ]);
    }
}