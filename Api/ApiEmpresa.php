
// control del verbo y el verbo y los parametros me dicen que hacer cumpliendo la regla de al Api Rest Full
// asi que en vez de action que llegue el verbo y segun si viene solo o si viene con algun paramaetro lo 
// llevaremos a ahcer una funcion u otra de esta api
<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../Repositories/RepositorioEmpresas.php';
require_once __DIR__ . '/../Models/Empresa.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Repositories/ConexionBD.php';


$repo = new RepositorioEmpresas();
$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

try {
    switch ($method) {
        case 'GET':
            procesarGet($repo, $id);
            break;
        case 'POST':
            procesarPost($repo);
            break;
        case 'PUT':
            procesarPut($repo, $id);
            break;
        case 'DELETE':
            procesarDelete($repo, $id);
            break;
        default:
            responder(['error' => 'Método no soportado'], 405);
    }
} catch (Exception $e) {
    responder(['error' => $e->getMessage()], 500);
}

// Envio una respuesta JSON con código de respuesta
function responder($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function procesarGet($repo, $id) {
    if ($id) {
        $empresa = $repo->leer($id);
        if ($empresa) {
            responder($empresa);
        } else {
            responder(['error' => 'Empresa no encontrada'], 404);
        }
    } else {
        $listado = $repo->listar();
        responder($listado);
    }
}

function procesarPost($repo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuario = new Usuario(null, $data['correo'], $data['clave']);
    $empresa = new Empresa(null, null, $data['correo'], $data['nombre'], $data['descripcion'], $data['logo_url'], $data['direccion']);
    $creada = $repo->crear($empresa, $usuario);
    responder($creada, 201);
}

function procesarPut($repo, $id) {
    if (!$id) responder(['error' => 'ID requerido para actualizar'], 400);
    $data = json_decode(file_get_contents('php://input'), true);

    $correo = $data['correo'];
    $nombre = $data['nombre'];
    $descripcion = $data['descripcion'];
    $logo_url = $data['logo_url'];
    $direccion = $data['direccion'];

    $resultado = $repo->editar($id, $correo, $nombre, $descripcion, $logo_url, $direccion);

    if ($resultado) {
        responder(['success' => true]);
    } else {
        responder(['error' => 'No se pudo editar'], 500);
    }
}

function procesarDelete($repo, $id) {
    if (!$id) responder(['error' => 'ID requerido para borrar'], 400);
    $delResult = $repo->borrar($id);
    if ($delResult) {
        responder(['success' => true]);
    } else {
        responder(['error' => 'No se pudo borrar'], 500);
    }
}
