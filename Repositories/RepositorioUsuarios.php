<?php
require_once __DIR__ . '/ConexionBD.php';
require_once __DIR__ . '/../Models/Usuario.php';

class RepositorioUsuarios {
    private $bd;
    private static $instancia = null;


    public function __construct() {
        $this->bd = ConexionBD::getInstancia()->getConexion();
    }

    public function crear(Usuario $usuario) {
        $sql = "INSERT INTO usuarios (correo, password) VALUES (:correo, :password)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':correo' => $usuario->getCorreo(),
            ':password' => $usuario->getClave()
        ]);
        $usuario->setId($this->bd->lastInsertId());
        return $usuario;
    }

    public function leer($id_Usuario) {
        $sql = "SELECT * FROM usuarios WHERE id_user = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id_Usuario]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fila) {
            return null;
        }
        return new Usuario($fila['id_user'], $fila['correo'], $fila['password']);
    }

    public function editar(Usuario $usuario) {
        $sql = "UPDATE usuarios SET correo = :correo, password = :password WHERE id_user = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':correo' => $usuario->getCorreo(),
            ':password' => $usuario->getClave(),
            ':id' => $usuario->getId()
        ]);
    }

    public function borrar($id_Usuario) {
        $sql = "DELETE FROM usuarios WHERE id_user = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $id_Usuario]);
    }

    public function listar() {
        $sql = "SELECT * FROM usuarios";
        $stmt = $this->bd->query($sql);
        $usuarios = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($fila['id_user'], $fila['correo'], $fila['password']);
        }
        return $usuarios;
    }

    public static function getInstancia() {
    if (self::$instancia == null) {
        self::$instancia = new RepositorioUsuarios();
    }
    return self::$instancia;
}


    public function modificarCorreo($idUsuario, $nuevoCorreo) {
        $sql = "UPDATE usuarios SET correo = :nuevoCorreo WHERE id_user = :idUsuario";
        $consulta = $this->bd->prepare($sql);
        return $consulta->execute(['nuevoCorreo' => $nuevoCorreo, 'idUsuario' => $idUsuario]);
    }


}
