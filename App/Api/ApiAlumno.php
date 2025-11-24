<?php

namespace App\Api;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/Autoloader.php';

use App\Repositories\RepositorioAlumnos;
use App\Models\Alumno;
use App\Models\Usuario;


ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

$metodo = strtoupper($_SERVER["REQUEST_METHOD"] ?? 'GET');
$esMultipart = (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false);


function getInputData() {
    $rawInput = file_get_contents("php://input");
    $jsonData = json_decode($rawInput, true);

    if ($jsonData === null || $jsonData === false || empty($jsonData)) {
        if (!empty($_POST)) {
            return $_POST;
        }
        return [];
    }
    return $jsonData;
}

if ($metodo === 'POST' && $esMultipart) {
    crearAlumnoConArchivos($_POST, $_FILES);
} 
else if ($metodo === 'PUT' || ($metodo === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT')) {
    $datos = getInputData();
    editarAlumno($datos, $_FILES);
} 
else {
    switch ($metodo) {
        case 'GET':
            if (isset($_GET['id']) && $_GET['id'] !== '') {
                obtenerAlumnoPorId((int)$_GET['id']); 
            } else if (isset($_GET['buscar']) && $_GET['buscar'] !== '') {
                buscarAlumnos($_GET['buscar']);
            } else {
                obtenerAlumnos();
            }
            break;
        case 'POST':
            $datos = getInputData();
            crearAlumno($datos);
            break;
        case 'PUT':
            $datos = getInputData();
            editarAlumno($datos, []);
            break;
        case 'DELETE':
            $datos = getInputData();
            borrarAlumno($datos);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
}

function obtenerAlumnos() {
    header('Content-Type: application/json');
    try {
        $repo = RepositorioAlumnos::getInstancia();
        $alumnos = $repo->listar();
        $respuesta = [];
        foreach ($alumnos as $alumno) {
            $respuesta[] = alumnoAArray($alumno);
        }
        echo json_encode($respuesta);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function buscarAlumnos($texto) {
    header('Content-Type: application/json');
    try {
        $repo = RepositorioAlumnos::getInstancia();
        $alumnos = $repo->buscarPorNombre(trim($texto));
        $respuesta = [];
        foreach ($alumnos as $alumno) {
            if ($alumno) { 
                $respuesta[] = alumnoAArray($alumno);
            }
        }
        echo json_encode($respuesta);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function crearAlumno($datos) {
    header('Content-Type: application/json');
    procesarCreacion($datos, null, null);
}

function crearAlumnoConArchivos($datos, $archivos) {
    header('Content-Type: application/json');
    $foto = $archivos['fotoPerfil'] ?? null;
    $cv = $archivos['curriculum'] ?? null;
    procesarCreacion($datos, $foto, $cv);
}

function procesarCreacion($datos, $foto, $cv) {
    if (!$datos || !isset($datos['correo'], $datos['nombre'], $datos['apellido1'], $datos['contrasena'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        return;
    }

    $alumno = new Alumno(
        null, null,
        $datos['correo'],
        $datos['nombre'],
        $datos['apellido1'],
        $datos['apellido2'] ?? '',
        $datos['direccion'] ?? '',
        $datos['edad'] ?? null,
        '', 
        ''  
    );

    $usuario = new Usuario(
        null,
        $datos['correo'],
        $datos['contrasena'],
        false,
        false
    );

    try {
        $repo = RepositorioAlumnos::getInstancia();
        $alumnoCreado = $repo->create($alumno, $usuario, $foto, $cv);
        if ($alumnoCreado) {
            http_response_code(201);
            echo json_encode(['success' => true, 'alumno' => alumnoAArray($alumnoCreado)]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No se pudo crear el alumno']);
        }
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function editarAlumno($datos, $archivos = []) {
    header('Content-Type: application/json');
    procesarEdicion($datos, $archivos['fotoPerfil'] ?? null, $archivos['curriculum'] ?? null);
}

function procesarEdicion($datos, $foto, $cv) {
    if (!isset($datos['idAlumno'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de alumno requerido']);
        return;
    }

    $repo = RepositorioAlumnos::getInstancia();
    $alumno = $repo->leer((int)$datos['idAlumno']);

    if (!$alumno) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Alumno no encontrado']);
        return;
    }

    $alumno->setNombre($datos['nombre'] ?? $alumno->getNombre());
    $alumno->setApellido1($datos['apellido1'] ?? $alumno->getApellido1());
    $alumno->setApellido2($datos['apellido2'] ?? $alumno->getApellido2());
    $alumno->setDireccion($datos['direccion'] ?? $alumno->getDireccion());
    $alumno->setEdad($datos['edad'] ?? $alumno->getEdad());
    $alumno->setCurriculumurl($datos['curriculumurl'] ?? $alumno->getCurriculumurl());

    $usuario = new Usuario(
        $alumno->getIdUsuario(),
        $datos['correo'] ?? $alumno->getCorreo(),
        $datos['contrasena'] ?? '',
        false,
        false
    );

    try {
        $ok = $repo->save($alumno, $usuario, $foto, $cv);

        if ($ok) {
            $alumnoActualizado = $repo->leer($alumno->getIdAlumno());
            http_response_code(200);
            echo json_encode(['success' => true, 'alumno' => alumnoAArray($alumnoActualizado)]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error al guardar cambios']);
        }
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function borrarAlumno($datos) {
    header('Content-Type: application/json');

    if (!isset($datos['idAlumno'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Falta ID']);
        return;
    }

    $repo = RepositorioAlumnos::getInstancia();

    try {
        $borrado = $repo->delete((int)$datos['idAlumno']);
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

    function alumnoAArray($alumno) {
        $fotoDefault = 'placeholderUsers.png'; 
        $nombreFoto = ($alumno->getFotoPerfil() && $alumno->getFotoPerfil() !== '')
            ? $alumno->getFotoPerfil()
            : $fotoDefault;
        $url_foto = 'Assets/Images/' . $nombreFoto;
        return [
            'idalumno'     => $alumno->getIdAlumno(),
            'iduser'       => $alumno->getIdUsuario(),
            'correo'       => $alumno->getCorreo(),
            'nombre'       => $alumno->getNombre(),
            'apellido1'    => $alumno->getApellido1(),
            'apellido2'    => $alumno->getApellido2(),
            'direccion'    => $alumno->getDireccion(),
            'edad'         => $alumno->getEdad(),
            'curriculumurl'=> $alumno->getCurriculumUrl(),
            'fotoperfil'   => $url_foto
        ];
    }

    function obtenerAlumnoPorId($id) {
    header('Content-Type: application/json');
    try {
        $repo = RepositorioAlumnos::getInstancia();
        $alumno = $repo->leer($id); 

        if ($alumno) {
            
            $respuesta = [
                'success' => true,
                'alumno' => alumnoAArray($alumno),
                'estudios' => [] 
            ];
            echo json_encode($respuesta);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Alumno no encontrado.']);
        }
    } catch (\Exception $e) {
        http_response_code(500);
        // Esto ayudará a depurar el error 500 en los logs
        error_log("Error al obtener Alumno por ID: " . $e->getMessage()); 
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}