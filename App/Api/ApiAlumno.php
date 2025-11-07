<?php

namespace App\Api;

use App\Repositories\RepositorioAlumnos;
use App\Models\Alumno;
use App\Models\Usuario;

$metodo = strtoupper($_SERVER["REQUEST_METHOD"] ?? 'GET');

// Ruta de la carpeta para la foto de perfil 
$carpetaFotos = __DIR__ . '/../../Public/Assets/Images/Alumno';
$fotoDefault = 'default.png';

if ($metodo === 'POST' && isset($_FILES['fotoPerfil'])) {
    crearAlumnoConFoto($_POST, $_FILES['fotoPerfil']);
} else if ($metodo === 'PUT' && isset($_FILES['fotoPerfil'])) {
    editarAlumnoConFoto($_POST, $_FILES['fotoPerfil']);
} else {
    $cuerpo = file_get_contents('php://input');
    switch ($metodo) {
        case 'GET':
            if (isset($_GET['buscar']) && $_GET['buscar'] !== '') {
                buscarAlumnos($_GET['buscar']);
            } else {
                obtenerAlumnos();
            }
            break;
        case 'POST':
            crearAlumno($cuerpo);
            break;
        case 'PUT':
            editarAlumno($cuerpo);
            break;
        case 'DELETE':
            borrarAlumno($cuerpo);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false]);
            break;
    }
}

function obtenerAlumnos() {
    header('Content-Type: application/json');
    http_response_code(200);
    $repo = RepositorioAlumnos::getInstancia();
    $alumnos = $repo->listar();
    $respuesta = [];
    foreach ($alumnos as $alumno) {
        $respuesta[] = alumnoAArray($alumno);
    }
    echo json_encode($respuesta);
}

function buscarAlumnos($texto) {
    header('Content-Type: application/json');
    http_response_code(200);
    $repo = RepositorioAlumnos::getInstancia();
    $alumnos = $repo->buscarPorNombre(trim($texto));
    $respuesta = [];
    foreach ($alumnos as $alumno) {
        $respuesta[] = alumnoAArray($alumno);
    }
    echo json_encode($respuesta);
}

function crearAlumno($cuerpo) {
    $datos = json_decode($cuerpo, true);
    if (!$datos || !isset($datos['correo'], $datos['nombre'], $datos['apellido1'], $datos['apellido2'], $datos['direccion'], $datos['edad'], $datos['contrasena'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        return;
    }
    $alumno = new Alumno(
        null, null, 
        $datos['correo'],
        $datos['nombre'],
        $datos['apellido1'],
        $datos['apellido2'],
        $datos['direccion'],
        $datos['edad'],
        $datos['curriculumurl'] ?? '',
        ''
    );
    $usuario = new Usuario(
        null,
        $datos['correo'],
        $datos['nombre_usuario'] ?? '',
        $datos['contrasena'],
        'alumno'
    );
    $repo = RepositorioAlumnos::getInstancia();
    $alumnoCreado = $repo->crear($alumno, $usuario);

    if ($alumnoCreado) {
        http_response_code(201);
        echo json_encode(['success' => true, 'alumno' => alumnoAArray($alumnoCreado)]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false]);
    }
}

function crearAlumnoConFoto($datos, $archivoFoto) {
    global $carpetaFotos;
    if (!isset($datos['correo'], $datos['nombre'], $datos['apellido1'], $datos['apellido2'], $datos['direccion'], $datos['edad'], $datos['contrasena'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        return;
    }
    $nombreFoto = uniqid('foto_') . '_' . basename($archivoFoto['name']);
    $rutaFoto = $carpetaFotos . $nombreFoto;
    if (move_uploaded_file($archivoFoto['tmp_name'], $rutaFoto)) {
        $alumno = new Alumno(
            null, null, 
            $datos['correo'],
            $datos['nombre'],
            $datos['apellido1'],
            $datos['apellido2'],
            $datos['direccion'],
            $datos['edad'],
            $datos['curriculumurl'] ?? '',
            $nombreFoto
        );
        $usuario = new Usuario(
            null,
            $datos['correo'],
            $datos['nombre_usuario'] ?? '',
            $datos['contrasena'],
            'alumno'
        );
        $repo = RepositorioAlumnos::getInstancia();
        $alumnoCreado = $repo->crear($alumno, $usuario, $nombreFoto);

        if ($alumnoCreado) {
            http_response_code(201);
            echo json_encode(['success' => true, 'alumno' => alumnoAArray($alumnoCreado)]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No se pudo crear alumno']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error subiendo foto']);
    }
}

function editarAlumno($cuerpo) {
    $datos = json_decode($cuerpo, true);
    if (!isset($datos['idAlumno'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de alumno requerido']);
        return;
    }
    $repo = RepositorioAlumnos::getInstancia();
    $alumno = $repo->leer((int)$datos['idAlumno']);
    if (!$alumno) {
        http_response_code(404);
        echo json_encode(['success' => false]);
        return;
    }
    $alumno->setNombre($datos['nombre'] ?? $alumno->getNombre());
    $alumno->setApellido1($datos['apellido1'] ?? $alumno->getApellido1());
    $alumno->setApellido2($datos['apellido2'] ?? $alumno->getApellido2());
    $alumno->setDireccion($datos['direccion'] ?? $alumno->getDireccion());
    $alumno->setEdad($datos['edad'] ?? $alumno->getEdad());
    $alumno->setCurriculumurl($datos['curriculumurl'] ?? $alumno->getCurriculumurl());

    // Cambiar foto solo si llega nuevo nombre (edición solo URL, no archivo)
    if (isset($datos['fotoPerfil']) && $datos['fotoPerfil'] != '' && $datos['fotoPerfil'] != $alumno->getFotoPerfil()) {
        borrarFotoFisica($alumno->getFotoPerfil());
        $alumno->setFotoPerfil($datos['fotoPerfil']);
    }

    $usuario = new Usuario(
        $alumno->getIdUsuario(),
        $datos['correo'] ?? $alumno->getCorreo(),
        $datos['nombre_usuario'] ?? '',
        $datos['contrasena'] ?? '',
        'alumno'
    );

    $ok = $repo->editar($alumno, $usuario);
    if ($ok) {
        http_response_code(200);
        echo json_encode(['success' => true, 'alumno' => alumnoAArray($alumno)]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false]);
    }
}

function editarAlumnoConFoto($datos, $archivoFoto) {
    global $carpetaFotos;
    if (!isset($datos['idAlumno'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de alumno requerido']);
        return;
    }
    $repo = RepositorioAlumnos::getInstancia();
    $alumno = $repo->leer((int)$datos['idAlumno']);
    if (!$alumno) {
        http_response_code(404);
        echo json_encode(['success' => false]);
        return;
    }
    $nombreFoto = uniqid('foto_') . '_' . basename($archivoFoto['name']);
    $rutaFoto = $carpetaFotos . $nombreFoto;
    if (move_uploaded_file($archivoFoto['tmp_name'], $rutaFoto)) {
        borrarFotoFisica($alumno->getFotoPerfil());
        $alumno->setFotoPerfil($nombreFoto);
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
        $datos['nombre_usuario'] ?? '',
        $datos['contrasena'] ?? '',
        'alumno'
    );
    $ok = $repo->editar($alumno, $usuario);
    if ($ok) {
        http_response_code(200);
        echo json_encode(['success' => true, 'alumno' => alumnoAArray($alumno)]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false]);
    }
}

function borrarAlumno($cuerpo) {
    $datos = json_decode($cuerpo, true);
    if (!isset($datos['idAlumno'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Falta ID']);
        return;
    }
    $repo = RepositorioAlumnos::getInstancia();
    $alumno = $repo->leer((int)$datos['idAlumno']);
    $borrado = $repo->borrar((int)$datos['idAlumno']);
    if ($borrado) {
        borrarFotoFisica($alumno->getFotoPerfil());
        http_response_code(200);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false]);
    }
}

function alumnoAArray($alumno) {
    global $fotoDefault;
    $url_foto = ($alumno->getFotoPerfil() !== '' && $alumno->getFotoPerfil() !== null)
        ? '/Assets/Images/' . $alumno->getFotoPerfil()
        : '/Assets/Images/' . $fotoDefault;
    return [
        'idAlumno' => $alumno->getIdAlumno(),
        'idUsuario' => $alumno->getIdUsuario(),
        'correo' => $alumno->getCorreo(),
        'nombre' => $alumno->getNombre(),
        'apellido1' => $alumno->getApellido1(),
        'apellido2' => $alumno->getApellido2(),
        'direccion' => $alumno->getDireccion(),
        'edad' => $alumno->getEdad(),
        'curriculumurl' => $alumno->getCurriculumurl(),
        'fotoPerfil' => $url_foto
    ];
}

function borrarFotoFisica($nombreFoto) {
    global $fotoDefault, $carpetaFotos;
    if ($nombreFoto && $nombreFoto !== $fotoDefault) {
        $ruta = $carpetaFotos . $nombreFoto;
        if (file_exists($ruta)) {
            @unlink($ruta);
        }
    }
}
