<?php

namespace App\Api;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/Autoloader.php';

use App\Repositories\RepositorioEmpresas;
use App\Models\Empresa;
use App\Models\Usuario;


ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');


$metodo = strtoupper($_SERVER["REQUEST_METHOD"] ?? 'GET');


$esMultipart = (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false);

if ($metodo === 'POST' && $esMultipart) {
    crearEmpresaConLogo($_POST, $_FILES['logo'] ?? null);
} else if ($metodo === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
    editarEmpresaConLogo($_POST, $_FILES['logo'] ?? null);
} else if ($metodo === 'PUT' && $esMultipart) {
    editarEmpresaConLogo($_POST, $_FILES['logo'] ?? null);
} else {
    $cuerpo = file_get_contents('php://input');
    switch ($metodo) {
        case 'GET':
            if (isset($_GET['buscar']) && $_GET['buscar'] !== '') {
                buscarEmpresas($_GET['buscar']);
            } else {
                obtenerEmpresas();
            }
            break;
        case 'POST':
            crearEmpresa($cuerpo);
            break;
        case 'PUT':
            editarEmpresa($cuerpo);
            break;
        case 'DELETE':
            borrarEmpresa($cuerpo);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
}

// GET

function obtenerEmpresas() {
    header('Content-Type: application/json');
    try {
        $repo = RepositorioEmpresas::getInstancia();
        $empresas = $repo->listar();
        $respuesta = [];
        foreach ($empresas as $empresa) {
            $respuesta[] = empresaAArray($empresa);
        }
        echo json_encode($respuesta);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function buscarEmpresas($texto) {
    header('Content-Type: application/json');
    try {
        $repo = RepositorioEmpresas::getInstancia();
        $empresas = $repo->buscarPorNombre(trim($texto));
        $respuesta = [];
        foreach ($empresas as $empresa) {
            $respuesta[] = empresaAArray($empresa);
        }
        echo json_encode($respuesta);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

//POST NORMAL

function crearEmpresa($cuerpo) {
    header('Content-Type: application/json');
    $datos = json_decode($cuerpo, true);

    if (!$datos || !isset($datos['correo'], $datos['nombre'], $datos['clave'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        return;
    }

    $empresa = new Empresa(null, null, $datos['correo'], $datos['nombre'], $datos['descripcion'] ?? null, '', $datos['direccion'] ?? null);
    $usuario = new Usuario(null, $datos['correo'], $datos['clave'], false, false);
    
    try {
        $repo = RepositorioEmpresas::getInstancia();
        $empresaCreada = $repo->crear($empresa, $usuario, null); // No hay archivo
        
        http_response_code(201);
        echo json_encode(['success' => true, 'empresa' => empresaAArray($empresaCreada)]);
    } catch (\Exception $e) {
        error_log("Error API creando empresa: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

//POST ARCHIVO

function crearEmpresaConLogo($datos, $archivoLogo) {
    header('Content-Type: application/json');
    
    if (!isset($datos['correo'], $datos['nombre'], $datos['clave'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        return;
    }

    $empresa = new Empresa(null, null, $datos['correo'], $datos['nombre'], $datos['descripcion'] ?? null, '', $datos['direccion'] ?? null);
    $usuario = new Usuario(null, $datos['correo'], $datos['clave'], false, false);

    try {
        $repo = RepositorioEmpresas::getInstancia();
        $empresaCreada = $repo->crear($empresa, $usuario, $archivoLogo);

        http_response_code(201);
        echo json_encode(['success' => true, 'empresa' => empresaAArray($empresaCreada)]);
    } catch (\Exception $e) {
        error_log("Error API creando empresa con logo: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

//PUT NORMAL

function editarEmpresa($cuerpo) {
    header('Content-Type: application/json');
    $datos = json_decode($cuerpo, true);
    
    if (!isset($datos['idEmpresa'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de empresa requerido']);
        return;
    }

    $repo = RepositorioEmpresas::getInstancia();
    $empresa = $repo->leer((int)$datos['idEmpresa']);

    if (!$empresa) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Empresa no encontrada']);
        return;
    }

    
    $empresa->setNombre($datos['nombre'] ?? $empresa->getNombre());
    $empresa->setDescripcion($datos['descripcion'] ?? $empresa->getDescripcion());
    $empresa->setDireccion($datos['direccion'] ?? $empresa->getDireccion());

    $usuario = new Usuario(
        $empresa->getIdUsuario(),
        $datos['correo'] ?? $empresa->getCorreo(),
        $datos['clave'] ?? '', 
        false,
        false
    );

    if ($repo->editar($empresa, $usuario, null)) { 
        
        $empresaActualizada = $repo->leer($empresa->getIdEmpresa());
        http_response_code(200);
        echo json_encode(['success' => true, 'empresa' => empresaAArray($empresaActualizada)]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
    }
}

// PUT CON ARCHIVO

function editarEmpresaConLogo($datos, $archivoLogo) {
    header('Content-Type: application/json');

    if (!isset($datos['idEmpresa'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de empresa requerido']);
        return;
    }

    $repo = RepositorioEmpresas::getInstancia();
    $empresa = $repo->leer((int)$datos['idEmpresa']);

    if (!$empresa) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Empresa no encontrada']);
        return;
    }
    
    $empresa->setNombre($datos['nombre'] ?? $empresa->getNombre());
    $empresa->setDescripcion($datos['descripcion'] ?? $empresa->getDescripcion());
    $empresa->setDireccion($datos['direccion'] ?? $empresa->getDireccion());

    $usuario = new Usuario(
        $empresa->getIdUsuario(),
        $datos['correo'] ?? $empresa->getCorreo(),
        $datos['clave'] ?? '',
        false,
        false
    );

    
    if ($repo->editar($empresa, $usuario, $archivoLogo)) {
        
        $empresaActualizada = $repo->leer($empresa->getIdEmpresa());
        http_response_code(200);
        echo json_encode(['success' => true, 'empresa' => empresaAArray($empresaActualizada)]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al actualizar con logo']);
    }
}

//DELETE

function borrarEmpresa($cuerpo) {
    header('Content-Type: application/json');
    $datos = json_decode($cuerpo, true);
    
    if (!isset($datos['idEmpresa'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Falta ID']);
        return;
    }

    $repo = RepositorioEmpresas::getInstancia();
    
    try {
        $borrado = $repo->borrar((int)$datos['idEmpresa']);
        if ($borrado) {
            http_response_code(200);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'No encontrado o no se pudo borrar']);
        }
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}



function empresaAArray($empresa) {
    
    $url_logo = ($empresa->getLogoUrl() !== '' && $empresa->getLogoUrl() !== null)
        ? '/Assets/Images/Empresa/' . $empresa->getLogoUrl()
        : '/Assets/Images/Empresa/placeholderUsers.png'; 
        $arrayEmpresas = [
            'idEmpresa' => $empresa->getIdEmpresa(),
            'idUsuario' => $empresa->getIdUsuario(),
            'correo' => $empresa->getCorreo(),
            'nombre' => $empresa->getNombre(),
            'descripcion' => $empresa->getDescripcion(),
            'logoUrl' => $url_logo,
            'direccion' => $empresa->getDireccion()
        ];

    return $arrayEmpresas; 
}