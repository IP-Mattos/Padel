<?php

class Movimiento {
    private $id;
    private $idUs;
    private $fecha;
    private $detalle;
    private $debe;
    private $haber;
    private $saldo;

    const TABLA = 'movimientos';

    // Getters
    public function getId() { return $this->id; }
    public function getIdUs() { return $this->idUs; }
    public function getFecha() { return $this->fecha; }
    public function getDetalle() { return $this->detalle; }
    public function getDebe() { return $this->debe; }
    public function getHaber() { return $this->haber; }
    public function getSaldo() { return $this->saldo; }

    // Setters
    public function setIdUs($idUs) { $this->idUs = $idUs; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setDetalle($detalle) { $this->detalle = $detalle; }
    public function setDebe($debe) { $this->debe = $debe; }
    public function setHaber($haber) { $this->haber = $haber; }
    public function setSaldo($saldo) { $this->saldo = $saldo; }

    public function __construct(
        $id,
        $idUs,
        $fecha,
        $detalle,
        $debe,
        $haber,
        $saldo
    ) {
        $this->id = $id;
        $this->idUs = $idUs;
        $this->fecha = $fecha;
        $this->detalle = $detalle;
        $this->debe = $debe;
        $this->haber = $haber;
        $this->saldo = $saldo;
    }

    public function guardar() {
        $conexion = new Conexion();
        if (isset($this->id)) {
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                idUs = :idUs, 
                fecha = :fecha, 
                detalle = :detalle, 
                debe = :debe, 
                haber = :haber, 
                saldo = :saldo 
                WHERE id = :id');
            $consulta->bindParam(':id', $this->id);
            $consulta->bindParam(':idUs', $this->idUs);
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->bindParam(':detalle', $this->detalle);
            $consulta->bindParam(':debe', $this->debe);
            $consulta->bindParam(':haber', $this->haber);
            $consulta->bindParam(':saldo', $this->saldo);
            $consulta->execute();
        } else {
            // El trigger calculará automáticamente el saldo
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (idUs, fecha, detalle, debe, haber) 
                VALUES (:idUs, :fecha, :detalle, :debe, :haber)');
            $consulta->bindParam(':idUs', $this->idUs);
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->bindParam(':detalle', $this->detalle);
            $consulta->bindParam(':debe', $this->debe);
            $consulta->bindParam(':haber', $this->haber);
            $consulta->execute();
            $this->id = $conexion->lastInsertId();
        }
        $conexion = null;
    }

    public static function buscarPorId($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, idUs, fecha, detalle, debe, haber, saldo FROM ' . self::TABLA . ' WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro) {
            return new self(
                $registro['id'],
                $registro['idUs'],
                $registro['fecha'],
                $registro['detalle'],
                $registro['debe'],
                $registro['haber'],
                $registro['saldo']
            );
        } else {
            return null;
        }
    }

    public static function buscarPorUsuario($idUs) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, idUs, fecha, detalle, debe, haber, saldo FROM ' . self::TABLA . ' WHERE idUs = :idUs ORDER BY fecha DESC, id DESC');
        $consulta->bindParam(':idUs', $idUs);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        $movimientos = [];
        foreach ($registros as $registro) {
            $movimientos[] = new self(
                $registro['id'],
                $registro['idUs'],
                $registro['fecha'],
                $registro['detalle'],
                $registro['debe'],
                $registro['haber'],
                $registro['saldo']
            );
        }
        return $movimientos;
    }

    public static function obtenerSaldoUsuario($idUs) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT IFNULL(SUM(debe) - SUM(haber), 0) as saldo FROM ' . self::TABLA . ' WHERE idUs = :idUs');
        $consulta->bindParam(':idUs', $idUs);
        $consulta->execute();
        $resultado = $consulta->fetch();
        return $resultado ? $resultado['saldo'] : 0;
    }

    public static function recuperarTodos() {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, idUs, fecha, detalle, debe, haber, saldo FROM ' . self::TABLA . ' ORDER BY fecha DESC, id DESC');
        $consulta->execute();
        $registros = $consulta->fetchAll();
        $movimientos = [];
        foreach ($registros as $registro) {
            $movimientos[] = new self(
                $registro['id'],
                $registro['idUs'],
                $registro['fecha'],
                $registro['detalle'],
                $registro['debe'],
                $registro['haber'],
                $registro['saldo']
            );
        }
        return $movimientos;
    }

    public static function borrarRegistro($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('DELETE FROM ' . self::TABLA . ' WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
    }
}