<?php

namespace App\Controllers;

use League\Plates\Engine;
use App\Repositories\RepositorioEmpresas;
use App\Repositories\RepositorioUsuarios; 
use App\Repositories\RepositorioOfertas;
use App\Repositories\RepositorioSolicitudes;
use App\Repositories\RepositorioCiclos;
use App\Repositories\RepositorioFamilias;
use App\Helpers\Adapter;
use App\Helpers\Mailer;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\Oferta;
use App\Helpers\Sesion; 
use App\Helpers\PdfGenerator;
use App\Helpers\Security\Validator;

class EmpresaController
{
    private $repositorio;
    private $repositorioUsuarios;

    public function __construct()
    {
        $this->repositorio = RepositorioEmpresas::getInstancia();
        $this->repositorioUsuarios = RepositorioUsuarios::getInstancia();
    }

    public function renderDashboard(Engine $engine) {
        Sesion::iniciar();
        $idUsuario = Sesion::obtenerIdUsuario();
        $empresa = $this->repositorio->obtenerPorIdUsuario($idUsuario);

        if (!$empresa) {
            header('Location: index.php?menu=login');
            
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

    public function mostrarListado(Engine $engine)
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
    
    private function handleGetActions(Engine $engine)
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
            
        } else if (isset($_POST['btnConfirmarEmpresa'])) {
            Validator::validarEmpresa((int)$_POST['id_empresa']);
            header('Location: index.php?menu=admin-empresas');
            
        } else if (isset($_POST['btnRechazarEmpresa'])) {
            $this->eliminarEmpresa((int)$_POST['id_empresa']);
            header('Location: index.php?menu=admin-empresas');
            
        } else {
            $this->renderizarListadoPrincipal($engine);
        }
    }

    private function procesarCreacion(Engine $engine)
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
            

        } catch (\Exception $e) {
            error_log("Error al crear empresa por Admin: " . $e->getMessage());
            echo $engine->render('Pages/Empresa/AñadirEmpresa', ['error' => $e->getMessage()]);
        }
    }

    private function procesarEdicion(Engine $engine)
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
            

        } catch (\Exception $e) {
            error_log("Error al editar empresa: " . $e->getMessage());
            echo $engine->render('Pages/Empresa/EditarEmpresa', ['empresaEdit' => $empresa, 'error' => $e->getMessage()]);
        }
    }
    
    public function eliminarEmpresa($id) 
    { 
        try {
            $this->repositorio->borrar($id);
        } catch (\Exception $e) {
            error_log("Error al eliminar empresa ID $id: " . $e->getMessage());
        }
    }

    private function renderizarListadoPrincipal(Engine $engine) 
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
    
    
    
    public function gestionarOfertas(Engine $engine) {
        Sesion::iniciar();
        $idUsuario = Sesion::obtenerIdUsuario();
        $empresa = $this->repositorio->obtenerPorIdUsuario($idUsuario);

        if (!$empresa) {
            header('Location: index.php?menu=login');
        }
        
        $repoOfertas = RepositorioOfertas::getInstancia();
        $idEmpresa = $empresa->getIdEmpresa();

        
        $limit = (int)($_GET['limit'] ?? 10);
        $page = (int)($_GET['page'] ?? 1);
        
        $totalCount = $repoOfertas->contarTodasOfertasPorEmpresa($idEmpresa);
        $totalPages = ceil($totalCount / $limit);
        
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $offset = ($page - 1) * $limit;
        
        $todasOfertas = $repoOfertas->obtenerOfertasConSolicitudes($idEmpresa, $limit, $offset);

        $topOfertas = $repoOfertas->obtenerTopOfertas($idEmpresa);

        echo $engine->render('Pages/Empresa/Ofertas', [
            'topOfertas' => $topOfertas,
            'todasOfertas' => $todasOfertas,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
        ]);
    }
    
    public function eliminarOferta() {
        Sesion::iniciar();
        if (Sesion::obtenerRol() !== 'EMPRESA') {
             header('Location: index.php?menu=login');
             
        }

        if (isset($_POST['id_oferta'])) {
            $repoOfertas = RepositorioOfertas::getInstancia();
            $repoOfertas->borrar((int)$_POST['id_oferta']);
        }
        
        header('Location: index.php?menu=empresa-ofertas');
        
    }
    
    public function renderEditarOferta(Engine $engine) {
        Sesion::iniciar();
        $idOferta = (int)($_GET['id'] ?? 0);
        $idUsuario = Sesion::obtenerIdUsuario();
        
        $repoOfertas = RepositorioOfertas::getInstancia();
        $repoCiclos = RepositorioCiclos::getInstancia();
        $repoFamilias = RepositorioFamilias::getInstancia();

        $oferta = $repoOfertas->leer($idOferta);
        $empresa = $this->repositorio->obtenerPorIdUsuario($idUsuario);

        if (!$oferta || !$empresa || $oferta->getIdEmpresa() !== $empresa->getIdEmpresa()) {
            $_SESSION['error'] = 'Acceso denegado o oferta no encontrada.';
            header('Location: index.php?menu=empresa-ofertas');
            
        }

        $ciclosDisponibles = $repoCiclos->listar();
        $familiasDisponibles = $repoFamilias->listar();
        $ciclosAsociadosData = $repoOfertas->obtenerCiclosDeOferta($idOferta);

        $ciclosAsociadosIds = array_column($ciclosAsociadosData, 'idciclo'); 

        echo $engine->render('Pages/Empresa/EditarOferta', [
            'oferta' => $oferta,
            'ciclosDisponibles' => $ciclosDisponibles,
            'familiasDisponibles' => $familiasDisponibles,
            'ciclosAsociadosIds' => $ciclosAsociadosIds
        ]);
    }
    
    public function verSolicitudesOferta(Engine $engine) {
        Sesion::iniciar();
        $idOferta = (int)($_GET['id'] ?? 0);
        if ($idOferta <= 0) {
            header('Location: index.php?menu=empresa-ofertas');
            
        }
        $repoSolicitudes = RepositorioSolicitudes::getInstancia();
        $repoOfertas = RepositorioOfertas::getInstancia();
        $oferta = $repoOfertas->leer($idOferta);
        $idUsuario = Sesion::obtenerIdUsuario();
        $empresa = $this->repositorio->obtenerPorIdUsuario($idUsuario);
        if (!$oferta || !$empresa || $oferta->getIdEmpresa() !== $empresa->getIdEmpresa()) {
             header('Location: index.php?menu=empresa-ofertas');
             
        }
        $solicitudes = $repoSolicitudes->obtenerAlumnosPorOferta($idOferta);
        echo $engine->render('Pages/Empresa/VerSolicitudes', [
            'oferta' => $oferta,
            'solicitudes' => $solicitudes
        ]);
    }
    
    public function procesarEdicionOferta() {
        
        $idOferta = (int)($_POST['id_oferta'] ?? 0);
        $idEmpresa = (int)($_POST['id_empresa'] ?? 0);
        $idsCiclos = $_POST['ciclos'] ?? []; 
        $activa = (int)($_POST['activa'] ?? 0); 

        $fechafin = empty($_POST['fechafin']) ? null : ($_POST['fechafin'] ?? null);
        $fechainicio = $_POST['fechainicio'] ?? null;
        
        $oferta = new Oferta(
            $idOferta,
            $idEmpresa,
            $_POST['descripcion'] ?? '',
            $fechainicio,
            $fechafin, 
            $activa,
            $_POST['titulo'] ?? ''
        );
        
        $repoOfertas = RepositorioOfertas::getInstancia();
        
        $ofertaExistente = $repoOfertas->leer($idOferta);
        if (!$ofertaExistente || $ofertaExistente->getIdEmpresa() !== $idEmpresa) {
            $_SESSION['error'] = 'Error de seguridad: Intento de edición no autorizado.';
            header('Location: index.php?menu=empresa-ofertas');
            
        }

        $resultado = $repoOfertas->editarConCiclos($oferta, $idsCiclos);

        if ($resultado === true) {
            $_SESSION['exito'] = 'Oferta actualizada con éxito.';
            header('Location: index.php?menu=empresa-ofertas');
        } else {
            $_SESSION['error'] = 'ERROR DE BD: No se pudo guardar. Causa: ' . $resultado;
            header('Location: index.php?menu=empresa-editar-oferta&id=' . $idOferta);
        }
        
    }

    public function generarPdfSolicitudes() {
        $idOferta = (int)($_GET['id'] ?? 0);
        
        if ($idOferta <= 0) {
            $_SESSION['error'] = "ID de oferta no válido para generar el PDF.";
            header('Location: index.php?menu=empresa-ofertas');
            
        }

        $repoSolicitudes = RepositorioSolicitudes::getInstancia();
        $repoOfertas = RepositorioOfertas::getInstancia();
        
        $oferta = $repoOfertas->leer($idOferta);
        if (!$oferta) {
            $_SESSION['error'] = "Oferta no encontrada.";
            header('Location: index.php?menu=empresa-ofertas');
            
        }
    
        $candidatosAceptados = $repoSolicitudes->obtenerAceptadosPorOferta($idOferta);
        
        $pdfGenerator = new PdfGenerator();
        
        $pdfGenerator->generarReporteSolicitudesAceptadas($oferta->getTitulo(), $candidatosAceptados);
    }

    public function procesarSolicitud() {
        
        $idSolicitud = (int)($_POST['id_solicitud'] ?? 0);
        $idOferta = (int)($_POST['id_oferta'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($idSolicitud <= 0 || $idOferta <= 0 || !in_array($action, ['aceptar', 'rechazar'])) {
            $_SESSION['error'] = 'Solicitud o acción no válida.';
            header('Location: index.php?menu=empresa-ofertas'); 
            
        }

        $repoSolicitudes = RepositorioSolicitudes::getInstancia();
        $candidatoData = $repoSolicitudes->obtenerSolicitudConAlumno($idSolicitud);
        
        if (!$candidatoData) { 
            $_SESSION['error'] = 'Candidato no encontrado.';
            header('Location: index.php?menu=empresa-ofertas'); 
            
        }

        $nuevoEstado = ($action === 'aceptar') ? 'aceptada' : 'rechazada';

        
        $exitoDB = $repoSolicitudes->actualizarEstado($idSolicitud, $nuevoEstado);

        
        if ($exitoDB) {
            $recipientEmail = $candidatoData['correo'];
            $recipientName = $candidatoData['nombre'] . ' ' . $candidatoData['apellido1'];
            $offerTitle = $candidatoData['titulo'];
            
            $mailer = new Mailer();
            $mailer->enviarEstadoSolicitud($recipientEmail, $recipientName, $offerTitle, $nuevoEstado);
            
            $_SESSION['exito'] = "Solicitud marcada como '$nuevoEstado' y email enviado.";
        } else {
            $_SESSION['error'] = "Error al actualizar el estado en la base de datos.";
        }
        
        header('Location: index.php?menu=empresa-ver-solicitudes&id=' . $idOferta);
        
    }

    public function renderCrearOferta(Engine $engine) {
    Sesion::iniciar();
    $idUsuario = Sesion::obtenerIdUsuario();
    
    $repoCiclos = RepositorioCiclos::getInstancia();
    $repoFamilias = RepositorioFamilias::getInstancia();
    $empresa = $this->repositorio->obtenerPorIdUsuario($idUsuario);

    if (!$empresa) {
        header('Location: index.php?menu=login');
        
    }

    echo $engine->render('Pages/Empresa/CrearOferta', [
        'ciclosDisponibles' => $repoCiclos->listar(),
        'familiasDisponibles' => $repoFamilias->listar(),
        'idEmpresa' => $empresa->getIdEmpresa() 
    ]);
}


public function procesarCreacionOferta() {
    $idEmpresa = (int)($_POST['id_empresa'] ?? 0);
    $idsCiclos = $_POST['ciclos'] ?? []; 
    $activa = (int)($_POST['activa'] ?? 1);
    
    
    if (empty($_POST['titulo']) || empty($_POST['fechainicio']) || $idEmpresa <= 0) {
        $_SESSION['error'] = 'El título y la fecha de inicio son obligatorios.';
        header('Location: index.php?menu=empresa-crear-oferta');
    
    }
    
    
    $fechafin = empty($_POST['fechafin']) ? null : ($_POST['fechafin'] ?? null);
    $fechainicio = $_POST['fechainicio'] ?? null;

    
    $oferta = new Oferta(
        null, 
        $idEmpresa,
        $_POST['descripcion'] ?? '',
        $fechainicio,
        $fechafin,
        $activa,
        $_POST['titulo'] ?? ''
    );
    
    $repoOfertas = RepositorioOfertas::getInstancia();
    $nuevaOferta = $repoOfertas->crearConCiclos($oferta, $idsCiclos);

    if ($nuevaOferta !== false) {
        $_SESSION['exito'] = '¡Oferta de trabajo "' . $nuevaOferta->getTitulo() . '" creada con éxito!';
    } else {
        $_SESSION['error'] = 'Error al crear la oferta. Revise los datos.';
    }

    // Redirigir siempre al listado
    header('Location: index.php?menu=empresa-ofertas');
    
}
}