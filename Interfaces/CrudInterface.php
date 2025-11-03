<?php
namespace Interfaces;

interface CrudInterface {
    //public function crear(array $datos);
    //public function editar($id, array $datos);
    public function leer($id);
    public function borrar($id);
    public function listar($filtro = '', $campoOrden = '', $ascendente = true);
}

?>