<?php

namespace App\Api;

use App\Helpers\Adapter;
use App\Helpers\Sesion;
use App\Helpers\Login;
use App\Helpers\Security\Validator;
use App\Helpers\Security\Authorization;

function obtenerEncabezadoAutorizacion() {
    if (function_exists('getallheaders')) {
        $encabezados = getallheaders();
        return $encabezados['Authorization'] ?? null;
    }
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return $_SERVER['HTTP_AUTHORIZATION'];
    }
    if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        return $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
    return null;
}

$verboHTTP = strtoupper($_SERVER["REQUEST_METHOD"] ?? 'GET');
$cuerpoPeticion = file_get_contents('php://input');
$valorEncabezadoAutorizacion = obtenerEncabezadoAutorizacion();

switch ($verboHTTP) {
    case 'GET':
        if (empty($cuerpoPeticion)) {
            obtenerToken();
        }
        break;

    case 'POST':
        $proceso = $_GET['proceso'] ?? '';
        if ($proceso === "logout") {
            cerrarSesionUsuario($valorEncabezadoAutorizacion);
        } else if ($proceso === "login") {
            procesarLogin($cuerpoPeticion);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Proceso no definido']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        break;
}

function obtenerToken() {
    if (Login::estaLogueado()) {
        $tokenSesion = Sesion::obtenerToken();
        $rolUsuario = Sesion::obtenerRol();

        if ($tokenSesion) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'token' => $tokenSesion,
                'rol' => $rolUsuario,
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'token' => '']);
        }
    } else {
        http_response_code(403);
        echo json_encode(['success' => false]);
    }
}

function cerrarSesionUsuario($valorEncabezadoAutorizacion) {
    header('Content-Type: application/json');
    $tokenRecibido = $valorEncabezadoAutorizacion;

    if (Sesion::obtenerToken() === $tokenRecibido) {
        Authorization::deleteToken($tokenRecibido);
        Login::cerrarSesion();
        http_response_code(204);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token inválido o no autorizado']);
    }
}

function procesarLogin($cuerpoPeticion) {
    header('Content-Type: application/json');
    $datos = json_decode($cuerpoPeticion, true);

    if (!isset($datos['correo'], $datos['contrasena'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        return;
    }

    $usuarioValido = Validator::validarUsuario($datos['correo'], $datos['contrasena']);
    if ($usuarioValido) {
        // Genera token seguro y guárdalo en la base de datos y en sesión PHP
        $tokenGenerado = Authorization::generarToken($usuarioValido);
        Sesion::iniciar();

        // 🛑 CAMBIO CLAVE AQUÍ: Usamos roles consistentes con el Repositorio/Controller
        // Asumiendo que tu repositorio maneja 'admin', 'alumno', 'empresa'.
        $rolSesion = $usuarioValido->isAdmin() ? 'admin' : ($usuarioValido->isAlumno() ? 'alumno' : 'empresa');

        Sesion::establecerSesion([
            'usuario' => $usuarioValido,
            'token' => $tokenGenerado,
            'rol' => $rolSesion // 'admin', 'alumno', o 'empresa'
        ]);

        http_response_code(200);
        // Devolvemos el rol que espera el cliente API (puedes mantener 'admin'/'usuario' o usar $rolSesion)
        echo json_encode(['success' => true, 'token' => $tokenGenerado, 'rol' => $usuarioValido->isAdmin() ? 'admin' : 'usuario']); 
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    }
}
