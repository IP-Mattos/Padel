<?php
require_once __DIR__ . '/../Conexion.php';

class CategoriasSocios
{
    private $id;
    private $nombre;
    private $valor;
    private $valorHoraBaja;
    private $valorHoraAlta;
    private $descripcion;

    const TABLA = 'categorias_socios';

    public function getId()
    {
        return $this->id;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function getValor()
    {
        return $this->valor;
    }
    public function getValorHoraBaja()
    {
        return $this->valorHoraBaja;
    }
    public function getValorHoraAlta()
    {
        return $this->valorHoraAlta;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function setValor($valor)
    {
        $this->valor = $valor;
    }
    public function setValorHoraBaja($valorHoraBaja)
    {
        $this->valorHoraBaja = $valorHoraBaja;
    }
    public function setValorHoraAlta($valorHoraAlta)
    {
        $this->valorHoraAlta = $valorHoraAlta;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function __construct($id, $nombre, $valor, $valorHoraBaja, $valorHoraAlta, $descripcion)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->valor = $valor;
        $this->valorHoraBaja = $valorHoraBaja;
        $this->valorHoraAlta = $valorHoraAlta;
        $this->descripcion = $descripcion;
    }

    public function guardar()
    {
        $conexion = new Conexion();
        if ($this->id) {
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET
                nombre = :nombre,
                valor = :valor,
                valorHoraBaja = :valorHoraBaja,
                valorHoraAlta = :valorHoraAlta,
                descripcion = :descripcion
                WHERE id = :id');
            $consulta->bindParam(':nombre', $this->nombre);
            $consulta->bindParam(':valor', $this->valor);
            $consulta->bindParam(':valorHoraBaja', $this->valorHoraBaja);
            $consulta->bindParam(':valorHoraAlta', $this->valorHoraAlta);
            $consulta->bindParam(':descripcion', $this->descripcion);
            $consulta->bindParam(':id', $this->id);
            $consulta->execute();
        } else {
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . '
                (nombre, valor, valorHoraBaja, valorHoraAlta, descripcion)
                VALUES (:nombre, :valor, :valorHoraBaja, :valorHoraAlta, :descripcion)');
            $consulta->bindParam(':nombre', $this->nombre);
            $consulta->bindParam(':valor', $this->valor);
            $consulta->bindParam(':valorHoraBaja', $this->valorHoraBaja);
            $consulta->bindParam(':valorHoraAlta', $this->valorHoraAlta);
            $consulta->bindParam(':descripcion', $this->descripcion);
            $consulta->execute();
            $this->id = $conexion->lastInsertId();
        }
        $conexion = null;
    }

    public static function buscarPorId($id)
    {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, nombre, valor, valorHoraBaja, valorHoraAlta, descripcion
            FROM ' . self::TABLA . ' WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro) {
            return new self(
                $registro['id'],
                $registro['nombre'],
                $registro['valor'],
                $registro['valorHoraBaja'],
                $registro['valorHoraAlta'],
                $registro['descripcion']
            );
        } else {
            return false;
        }
        $conexion = null;
    }

    public static function recuperarTodos()
    {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, nombre, valor, valorHoraBaja, valorHoraAlta, descripcion
            FROM ' . self::TABLA . ' ORDER BY nombre ASC');
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
    }

    public static function borrarRegistro($id)
    {
        try {
            $conexion = new Conexion();
            $consulta = $conexion->prepare('DELETE FROM ' . self::TABLA . ' WHERE id = :id');
            $consulta->bindParam(':id', $id);
            $consulta->execute();
            $conexion = null;
            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
?>