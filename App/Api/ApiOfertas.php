<?php
namespace App\Api;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/Autoloader.php';

use App\Repositories\RepositorioOfertas;
use App\Repositories\RepositorioSolicitudes;
use App\Repositories\RepositorioAlumnos;
use App\Helpers\Sesion;

Sesion::iniciar();
header('Content-Type: application/json');

$metodo = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? null;
$idUser = Sesion::obtenerIdUsuario();

if (!$idUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$repoAlumnos = RepositorioAlumnos::getInstancia();
$alumno = $repoAlumnos->obtenerPorIdUsuario($idUser);

if (!$alumno) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Usuario no es alumno']);
    exit;
}

try {
    
    if ($metodo === 'GET' && isset($_GET['id'])) {
        $idOferta = (int)$_GET['id'];
        
        $repoOfertas = RepositorioOfertas::getInstancia();
        $oferta = $repoOfertas->leer($idOferta);
        
        if ($oferta) {
            
            $repoSolicitudes = RepositorioSolicitudes::getInstancia();
            $yaInscrito = $repoSolicitudes->existeSolicitud($idOferta, $alumno->getIdAlumno());

            echo json_encode([
                'success' => true,
                'oferta' => [
                    'id' => $oferta->getIdOferta(),
                    'titulo' => $oferta->getTitulo(),
                    'descripcion' => $oferta->getDescripcion(),
                    'fecha' => $oferta->getFechaPublicacion(),
                    'duracion' => $oferta->getDuracion(),
                    'importe' => $oferta->getImporte(),
                    'idEmpresa' => $oferta->getIdEmpresa()
                ],
                'yaInscrito' => $yaInscrito
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Oferta no encontrada']);
        }
    }
    
    else if ($metodo === 'POST' && $accion === 'postular') {
        $input = json_decode(file_get_contents('php://input'), true);
        $idOferta = $input['idOferta'] ?? null;

        if ($idOferta) {
            $repoSolicitudes = RepositorioSolicitudes::getInstancia();
            
            if ($repoSolicitudes->existeSolicitud($idOferta, $alumno->getIdAlumno())) {
                echo json_encode(['success' => false, 'error' => 'Ya estás inscrito.']);
            } else {
                $repoSolicitudes->crear($idOferta, $alumno->getIdAlumno());
                echo json_encode(['success' => true, 'message' => 'Postulación correcta.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Faltan datos.']);
        }
    }
    else {
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    }

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}