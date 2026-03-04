<?php
require_once '../Conexion.php';

class Canje {
    private $id;
    private $fecha;
    private $usuario;
    private $puntos;
    private $estado;

    const TABLA = 'canje';

    public function getId()         { return $this->id; }
    public function getFecha()      { return $this->fecha; }
    public function getUsuario()    { return $this->usuario; }
    public function getPuntos()     { return $this->puntos; }
    public function getEstado()     { return $this->estado; }

    public function setFecha($fecha)        { $this->fecha = $fecha; }
    public function setUsuario($usuario)    { $this->usuario = $usuario; }
    public function setPuntos($puntos)      { $this->puntos = $puntos; }
    public function setEstado($estado)      { $this->estado = $estado; }

    public function __construct($fecha, $usuario, $puntos, $estado, $id = null) {
        $this->fecha = $fecha;
        $this->usuario = $usuario;
        $this->puntos = $puntos;
        $this->estado = $estado;
        $this->id = $id;
    }

    public function guardar() {
        $conexion = new Conexion();
        if ($this->id) {
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                fecha = :fecha, 
                usuario = :usuario, 
                puntos = :puntos, 
                estado = :estado 
                WHERE id = :id');
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->bindParam(':usuario', $this->usuario);
            $consulta->bindParam(':puntos', $this->puntos);
            $consulta->bindParam(':estado', $this->estado);
            $consulta->bindParam(':id', $this->id);
            $consulta->execute();
        } else {
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (fecha, usuario, puntos, estado) 
                VALUES (:fecha, :usuario, :puntos, :estado)');
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->bindParam(':usuario', $this->usuario);
            $consulta->bindParam(':puntos', $this->puntos);
            $consulta->bindParam(':estado', $this->estado);
            $consulta->execute();
            $this->id = $conexion->lastInsertId();
        }
        $conexion = null;
    }

    public static function buscarPorId($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, fecha, usuario, puntos, estado 
            FROM ' . self::TABLA . ' WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro) {
            return new self(
                $registro['fecha'],
                $registro['usuario'],
                $registro['puntos'],
                $registro['estado'],
                $registro['id']
            );
        } else {
            return false;
        }
        $conexion = null;
    }

    public static function recuperarTodos($usuario = null, $estado = null, $fechaDesde = null, $fechaHasta = null) {
        $conexion = new Conexion();
        $sql = 'SELECT id, fecha, usuario, puntos, estado FROM ' . self::TABLA . ' WHERE id > 0';
        
        if ($usuario) {
            $sql .= ' AND usuario = ' . $usuario;
        }
        if ($estado !== null) {
            $sql .= ' AND estado = "' . $estado . '"';
        }
        if ($fechaDesde) {
            $sql .= ' AND fecha >= "' . $fechaDesde . '"';
        }
        if ($fechaHasta) {
            $sql .= ' AND fecha <= "' . $fechaHasta . '"';
        }
        
        $sql .= ' ORDER BY fecha DESC';
        
        $consulta = $conexion->prepare($sql);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
    }

    public static function borrarRegistro($id) {
        try {
            $conexion = new Conexion();
            $consulta = $conexion->prepare('DELETE FROM ' . self::TABLA . ' WHERE id = :id and estado = 0');
            $consulta->bindParam(':id', $id);
            $consulta->execute();
            

        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
?>