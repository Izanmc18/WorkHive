
// control del verbo y el verbo y los parametros me dicen que hacer cumpliendo la regla de al Api Rest Full
// asi que en vez de action que llegue el verbo y segun si viene solo o si viene con algun paramaetro lo 
// llevaremos a ahcer una funcion u otra de esta api
<?php

namespace App\Api;

use App\Repositories\RepositorioEmpresas;
use App\Models\Empresa;
use App\Models\Usuario;

// Determinar método HTTP y si viene logo (multipart/form-data)
$metodo = strtoupper($_SERVER["REQUEST_METHOD"] ?? 'GET');

if ($metodo === 'POST' && isset($_FILES['logo'])) {
    crearEmpresaConLogo($_POST, $_FILES['logo']);
} else if ($metodo === 'PUT' && isset($_FILES['logo'])) {
    editarEmpresaConLogo($_POST, $_FILES['logo']);
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
            echo json_encode(['exito' => false]);
            break;
    }
}

// -------- GET --------

function obtenerEmpresas() {
    header('Content-Type: application/json');
    http_response_code(200);
    $repo = RepositorioEmpresas::getInstancia();
    $empresas = $repo->listar();
    $respuesta = [];
    foreach ($empresas as $empresa) {
        $respuesta[] = empresaAArray($empresa);
    }
    echo json_encode($respuesta);
}

function buscarEmpresas($texto) {
    header('Content-Type: application/json');
    http_response_code(200);
    $repo = RepositorioEmpresas::getInstancia();
    $empresas = $repo->buscarPorNombre(trim($texto));
    $respuesta = [];
    foreach ($empresas as $empresa) {
        $respuesta[] = empresaAArray($empresa);
    }
    echo json_encode($respuesta);
}

// -------- POST --------

function crearEmpresa($cuerpo) {
    $datos = json_decode($cuerpo, true);
    if (!$datos || !isset($datos['correo'], $datos['nombre'], $datos['descripcion'], $datos['direccion'], $datos['contrasena'])) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'error' => 'Datos incompletos']);
        return;
    }
    $empresa = new Empresa(
        null,
        null,
        $datos['correo'],
        $datos['nombre'],
        $datos['descripcion'],
        '',
        $datos['direccion']
    );
    $usuario = new Usuario(
        null,
        $datos['correo'],
        $datos['nombre_usuario'] ?? '',
        $datos['contrasena'],
        'empresa'
    );
    $repo = RepositorioEmpresas::getInstancia();
    $empresaCreada = $repo->crear($empresa, $usuario);

    if ($empresaCreada) {
        http_response_code(201);
        echo json_encode(['exito' => true, 'empresa' => empresaAArray($empresaCreada)]);
    } else {
        http_response_code(400);
        echo json_encode(['exito' => false]);
    }
}

function crearEmpresaConLogo($datos, $archivoLogo) {
    if (!isset($datos['correo'], $datos['nombre'], $datos['descripcion'], $datos['direccion'], $datos['contrasena'])) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'error' => 'Datos incompletos']);
        return;
    }
    $nombreLogo = uniqid('logo_') . '_' . basename($archivoLogo['name']);
    $rutaLogo = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/empresas/' . $nombreLogo;

    if (move_uploaded_file($archivoLogo['tmp_name'], $rutaLogo)) {
        $empresa = new Empresa(
            null,
            null,
            $datos['correo'],
            $datos['nombre'],
            $datos['descripcion'],
            $nombreLogo,
            $datos['direccion']
        );
        $usuario = new Usuario(
            null,
            $datos['correo'],
            $datos['nombre_usuario'] ?? '',
            $datos['contrasena'],
            'empresa'
        );
        $repo = RepositorioEmpresas::getInstancia();
        $empresaCreada = $repo->crear($empresa, $usuario, $nombreLogo);

        if ($empresaCreada) {
            http_response_code(201);
            echo json_encode(['exito' => true, 'empresa' => empresaAArray($empresaCreada)]);
        } else {
            http_response_code(400);
            echo json_encode(['exito' => false, 'error' => 'No se pudo crear empresa']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['exito' => false, 'error' => 'Error al subir el logo']);
    }
}

// -------- PUT (edición) --------

function editarEmpresa($cuerpo) {
    $datos = json_decode($cuerpo, true);
    if (!isset($datos['idEmpresa'])) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'error' => 'ID de empresa requerido']);
        return;
    }
    $repo = RepositorioEmpresas::getInstancia();
    $empresa = $repo->leer((int)$datos['idEmpresa']);
    if (!$empresa) {
        http_response_code(404);
        echo json_encode(['exito' => false]);
        return;
    }
    $empresa->setNombre($datos['nombre'] ?? $empresa->getNombre());
    $empresa->setDescripcion($datos['descripcion'] ?? $empresa->getDescripcion());
    $empresa->setDireccion($datos['direccion'] ?? $empresa->getDireccion());

    // Cambia logo si llega nuevo nombre de archivo (ej: edición sólo url, no archivo)
    if (isset($datos['logoUrl']) && $datos['logoUrl'] != '' && $datos['logoUrl'] != $empresa->getLogoUrl()) {
        borrarLogoFisico($empresa->getLogoUrl());
        $empresa->setLogoUrl($datos['logoUrl']);
    }

    $usuario = new Usuario(
        $empresa->getIdUsuario(),
        $datos['correo'] ?? $empresa->getCorreo(),
        $datos['nombre_usuario'] ?? '',
        $datos['contrasena'] ?? '',
        'empresa'
    );

    $ok = $repo->editar($empresa, $usuario);
    if ($ok) {
        http_response_code(200);
        echo json_encode(['exito' => true, 'empresa' => empresaAArray($empresa)]);
    } else {
        http_response_code(400);
        echo json_encode(['exito' => false]);
    }
}

function editarEmpresaConLogo($datos, $archivoLogo) {
    if (!isset($datos['idEmpresa'])) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'error' => 'ID de empresa requerido']);
        return;
    }
    $repo = RepositorioEmpresas::getInstancia();
    $empresa = $repo->leer((int)$datos['idEmpresa']);
    if (!$empresa) {
        http_response_code(404);
        echo json_encode(['exito' => false]);
        return;
    }
    $nombreLogo = uniqid('logo_') . '_' . basename($archivoLogo['name']);
    $rutaLogo = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/empresas/' . $nombreLogo;

    if (move_uploaded_file($archivoLogo['tmp_name'], $rutaLogo)) {
        borrarLogoFisico($empresa->getLogoUrl());
        $empresa->setLogoUrl($nombreLogo);
    }

    $empresa->setNombre($datos['nombre'] ?? $empresa->getNombre());
    $empresa->setDescripcion($datos['descripcion'] ?? $empresa->getDescripcion());
    $empresa->setDireccion($datos['direccion'] ?? $empresa->getDireccion());

    $usuario = new Usuario(
        $empresa->getIdUsuario(),
        $datos['correo'] ?? $empresa->getCorreo(),
        $datos['nombre_usuario'] ?? '',
        $datos['contrasena'] ?? '',
        'empresa'
    );

    $ok = $repo->editar($empresa, $usuario);
    if ($ok) {
        http_response_code(200);
        echo json_encode(['exito' => true, 'empresa' => empresaAArray($empresa)]);
    } else {
        http_response_code(400);
        echo json_encode(['exito' => false]);
    }
}

// -------- DELETE --------

function borrarEmpresa($cuerpo) {
    $datos = json_decode($cuerpo, true);
    if (!isset($datos['idEmpresa'])) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'error' => 'Falta ID']);
        return;
    }
    $repo = RepositorioEmpresas::getInstancia();
    $empresa = $repo->leer((int)$datos['idEmpresa']);
    $borrado = $repo->borrar((int)$datos['idEmpresa']);
    if ($borrado) {
        borrarLogoFisico($empresa->getLogoUrl());
        http_response_code(200);
        echo json_encode(['exito' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['exito' => false]);
    }
}

// -------- Utilidad Logo --------

function empresaAArray($empresa) {
    $url_logo = ($empresa->getLogoUrl() !== '' && $empresa->getLogoUrl() !== null)
        ? '/assets/images/empresas/' . $empresa->getLogoUrl()
        : '/assets/images/empresas/default.png'; 
    return [
        'idEmpresa' => $empresa->getIdEmpresa(),
        'idUsuario' => $empresa->getIdUsuario(),
        'correo' => $empresa->getCorreo(),
        'nombre' => $empresa->getNombre(),
        'descripcion' => $empresa->getDescripcion(),
        'logoUrl' => $url_logo,
        'direccion' => $empresa->getDireccion()
    ];
}

function borrarLogoFisico($nombreLogo) {
    if ($nombreLogo && $nombreLogo !== 'default.png') {
        $ruta = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/empresas/' . $nombreLogo;
        if (file_exists($ruta)) {
            @unlink($ruta);
        }
    }
}
