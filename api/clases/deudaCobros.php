<?php

require_once __DIR__ . '/../Conexion.php';

class DeudaCobros {
    private $id;
    private $idUsuario;
    private $fecha;
    private $monto;
    private $origen;
    private $detalle;
    private $estado;

    const TABLA = 'deuda_cobros';

    // Getters
    public function getId() { return $this->id; }
    public function getIdUsuario() { return $this->idUsuario; }
    public function getFecha() { return $this->fecha; }
    public function getMonto() { return $this->monto; }
    public function getOrigen() { return $this->origen; }
    public function getDetalle() { return $this->detalle; }
    public function getEstado() { return $this->estado; }

    // Setters
    public function setIdUsuario($idUsuario) { $this->idUsuario = $idUsuario; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setMonto($monto) { $this->monto = $monto; }
    public function setOrigen($origen) { $this->origen = $origen; }
    public function setDetalle($detalle) { $this->detalle = $detalle; }
    public function setEstado($estado) { $this->estado = $estado; }

    public function __construct($id = null, $idUsuario = null, $fecha = null, $monto = null, $origen = null, $detalle = null, $estado = 1) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->fecha = $fecha;
        $this->monto = $monto;
        $this->origen = $origen;
        $this->detalle = $detalle;
        $this->estado = $estado;
    }

    public function guardar() {
        $conexion = new Conexion();
        if (isset($this->id)) {
            // Actualizar registro existente
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                idUsuario = :idUsuario, 
                fecha = :fecha, 
                monto = :monto, 
                origen = :origen, 
                detalle = :detalle, 
                estado = :estado 
                WHERE id = :id');
            $consulta->bindParam(':id', $this->id);
            $consulta->bindParam(':idUsuario', $this->idUsuario);
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->bindParam(':monto', $this->monto);
            $consulta->bindParam(':origen', $this->origen);
            $consulta->bindParam(':detalle', $this->detalle);
            $consulta->bindParam(':estado', $this->estado);
            $consulta->execute();
        } else {
            // Insertar nuevo registro
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (idUsuario, fecha, monto, origen, detalle, estado) 
                VALUES (:idUsuario, :fecha, :monto, :origen, :detalle, :estado)');
            $consulta->bindParam(':idUsuario', $this->idUsuario);
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->bindParam(':monto', $this->monto);
            $consulta->bindParam(':origen', $this->origen);
            $consulta->bindParam(':detalle', $this->detalle);
            $consulta->bindParam(':estado', $this->estado);
            $consulta->execute();
            $this->id = $conexion->lastInsertId();
        }
        $conexion = null;
        return $this->id;
    }

    public static function buscarPorId($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, idUsuario, fecha, monto, origen, detalle, estado 
            FROM ' . self::TABLA . ' 
            WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch(PDO::FETCH_ASSOC);
        $conexion = null;
        
        if ($registro) {
            return new DeudaCobros(
                $registro['id'],
                $registro['idUsuario'],
                $registro['fecha'],
                $registro['monto'],
                $registro['origen'],
                $registro['detalle'],
                $registro['estado']
            );
        }
        return null;
    }

    public static function buscarPorUsuario($idUsuario, $estado = null) {
        $conexion = new Conexion();
        $sql = 'SELECT id, idUsuario, fecha, monto, origen, detalle, estado 
            FROM ' . self::TABLA . ' 
            WHERE idUsuario = :idUsuario';
        
        if ($estado !== null) {
            $sql .= ' AND estado = :estado';
        }
        
        $sql .= ' ORDER BY fecha DESC';
        
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':idUsuario', $idUsuario);
        
        if ($estado !== null) {
            $consulta->bindParam(':estado', $estado);
        }
        
        $consulta->execute();
        $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $conexion = null;
        
        $cobros = [];
        foreach ($registros as $registro) {
            $cobros[] = new DeudaCobros(
                $registro['id'],
                $registro['idUsuario'],
                $registro['fecha'],
                $registro['monto'],
                $registro['origen'],
                $registro['detalle'],
                $registro['estado']
            );
        }
        return $cobros;
    }

    public static function obtenerTotalPorUsuario($idUsuario, $estado = 1) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT COALESCE(SUM(monto), 0) as total 
            FROM ' . self::TABLA . ' 
            WHERE idUsuario = :idUsuario AND estado = :estado');
        $consulta->bindParam(':idUsuario', $idUsuario);
        $consulta->bindParam(':estado', $estado);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        $conexion = null;
        
        return $resultado['total'];
    }

    public static function recuperarPorRangoFechas($fechaDesde, $fechaHasta, $idUsuario = null) {
        $conexion = new Conexion();
        $sql = 'SELECT id, idUsuario, fecha, monto, origen, detalle, estado 
            FROM ' . self::TABLA . ' 
            WHERE fecha BETWEEN :fechaDesde AND :fechaHasta';
        
        if ($idUsuario !== null) {
            $sql .= ' AND idUsuario = :idUsuario';
        }
        
        $sql .= ' ORDER BY fecha DESC';
        
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':fechaDesde', $fechaDesde);
        $consulta->bindParam(':fechaHasta', $fechaHasta);
        
        if ($idUsuario !== null) {
            $consulta->bindParam(':idUsuario', $idUsuario);
        }
        
        $consulta->execute();
        $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $conexion = null;
        
        $cobros = [];
        foreach ($registros as $registro) {
            $cobros[] = new DeudaCobros(
                $registro['id'],
                $registro['idUsuario'],
                $registro['fecha'],
                $registro['monto'],
                $registro['origen'],
                $registro['detalle'],
                $registro['estado']
            );
        }
        return $cobros;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'idUsuario' => $this->idUsuario,
            'fecha' => $this->fecha,
            'monto' => $this->monto,
            'origen' => $this->origen,
            'detalle' => $this->detalle,
            'estado' => $this->estado
        ];
    }
}
?>