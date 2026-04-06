<?php
require_once '../Conexion.php';

class Deuda {
    private $id;
    private $idUsuario;
    private $idChelada;
    private $idPagos;
    private $idCobros;
    private $debe;
    private $haber;
    private $saldo;
    private $fecha;

    const TABLA = 'deuda';

    // Getters
    public function getId() { return $this->id; }
    public function getIdUsuario() { return $this->idUsuario; }
    public function getIdChelada() { return $this->idChelada; }
    public function getIdPagos() { return $this->idPagos; }
    public function getidCobros() { return $this->idCobros; }
    public function getDebe() { return $this->debe; }
    public function getHaber() { return $this->haber; }
    public function getSaldo() { return $this->saldo; }
    public function getFecha() { return $this->fecha; }

    // Setters
    public function setIdUsuario($idUsuario) { $this->idUsuario = $idUsuario; }
    public function setIdChelada($idChelada) { $this->idChelada = $idChelada; }
    public function setIdPagos($idPagos) { $this->idPagos = $idPagos; }
    public function setidCobros($idCobros) { $this->idCobros = $idCobros; }
    public function setDebe($debe) { $this->debe = $debe; }
    public function setHaber($haber) { $this->haber = $haber; }
    public function setSaldo($saldo) { $this->saldo = $saldo; }
    public function setFecha($fecha) { $this->fecha = $fecha; }

    // Constructor
    public function __construct($idUsuario, $idChelada, $idPagos, $idCobros, $debe, $haber, $saldo, $fecha = null, $id = null) {
        $this->idUsuario = $idUsuario;
        $this->idChelada = $idChelada;
        $this->idPagos = $idPagos;
        $this->idCobros = $idCobros;
        $this->debe = $debe;
        $this->haber = $haber;
        $this->saldo = $saldo;
        $this->fecha = $fecha ?? date('Y-m-d H:i:s');
        $this->id = $id;
    }

    // Guardar (Insertar o Actualizar)
    public function guardar() {
        $conexion = new Conexion();
        
        if ($this->id) {
            // Actualizar registro existente
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                idUsuario = :idUsuario,
                idChelada = :idChelada,
                idPagos = :idPagos,
                idCobros = :idCobros,
                debe = :debe,
                haber = :haber,
                saldo = :saldo,
                fecha = :fecha
                WHERE id = :id');
            
            $consulta->bindParam(':idUsuario', $this->idUsuario);
            $consulta->bindParam(':idChelada', $this->idChelada);
            $consulta->bindParam(':idPagos', $this->idPagos);
            $consulta->bindParam(':idCobros', $this->idCobros);
            $consulta->bindParam(':debe', $this->debe);
            $consulta->bindParam(':haber', $this->haber);
            $consulta->bindParam(':saldo', $this->saldo);
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->bindParam(':id', $this->id);
            $consulta->execute();
        } else {
            // Insertar nuevo registro
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (idUsuario, idChelada, idPagos, idCobros, debe, haber, saldo, fecha) 
                VALUES (:idUsuario, :idChelada, :idPagos, :idCobros, :debe, :haber, :saldo, :fecha)');
            
            $consulta->bindParam(':idUsuario', $this->idUsuario);
            $consulta->bindParam(':idChelada', $this->idChelada);
            $consulta->bindParam(':idPagos', $this->idPagos);
            $consulta->bindParam(':idCobros', $this->idCobros);
            $consulta->bindParam(':debe', $this->debe);
            $consulta->bindParam(':haber', $this->haber);
            $consulta->bindParam(':saldo', $this->saldo);
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->execute();
            
            $this->id = $conexion->lastInsertId();
        }
        
        $conexion = null;
    }

    // Buscar por ID
    public static function buscarPorId($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, idUsuario, idChelada, idPagos, idCobros, debe, haber, saldo, fecha 
            FROM ' . self::TABLA . ' 
            WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch();
        
        if ($registro) {
            return new self(
                $registro['idUsuario'],
                $registro['idChelada'],
                $registro['idPagos'],
                $registro['idCobros'],
                $registro['debe'],
                $registro['haber'],
                $registro['saldo'],
                $registro['fecha'],
                $registro['id']
            );
        } else {
            return null;
        }
    }

    // Buscar por Usuario
    public static function buscarPorUsuario($idUsuario) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, idUsuario, idChelada, idPagos, idCobros, debe, haber, saldo, fecha 
            FROM ' . self::TABLA . ' 
            WHERE idUsuario = :idUsuario 
            ORDER BY id DESC');
        $consulta->bindParam(':idUsuario', $idUsuario);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        
        $deudas = [];
        foreach ($registros as $registro) {
            $deudas[] = new self(
                $registro['idUsuario'],
                $registro['idChelada'],
                $registro['idPagos'],
                $registro['idCobros'],
                $registro['debe'],
                $registro['haber'],
                $registro['saldo'],
                $registro['fecha'],
                $registro['id']
            );
        }
        
        return $deudas;
    }

    // Obtener saldo total de un usuario
    public static function obtenerSaldoUsuario($idUsuario) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT (SUM(debe)-SUM(haber)) as saldoTotal 
            FROM ' . self::TABLA . ' 
            WHERE idUsuario = :idUsuario');
        $consulta->bindParam(':idUsuario', $idUsuario);
        $consulta->execute();
        $resultado = $consulta->fetch();
        
        return $resultado['saldoTotal'] ?? 0;
    }

    // Recuperar todos los registros
    public static function recuperarTodosDeudores() {
        $conexion = new Conexion();
        $sql = 'SELECT d.id, d.idUsuario, d.idChelada, d.idPagos, d.idCobros, d.debe, d.haber, (SUM(d.debe)-SUM(d.haber)) as saldo, u.nombre fecha 
            FROM ' . self::TABLA . ' d LEFT JOIN usuarios u ON u.id = d.idUsuario WHERE (d.debe <> 0 OR d.haber <> 0) 
            GROUP BY d.idUsuario ORDER BY u.nombre ASC';
           //  echo $sql;
        $consulta = $conexion->prepare($sql);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        
        $deudas = [];
        foreach ($registros as $registro) {
            $deudas[] = new self(
                $registro['idUsuario'],
                $registro['idChelada'],
                $registro['idPagos'],
                $registro['idCobros'],
                $registro['debe'],
                $registro['haber'],
                $registro['saldo'],
                $registro['fecha'],
                $registro['id']
            );
        }
        
        return $deudas;
    }

    // Borrar registro
    public static function borrarRegistro($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('DELETE FROM ' . self::TABLA . ' WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $conexion = null;
        
        return $consulta->rowCount() > 0;
    }

    public static function obtenerSaldoActual($idUsuario) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT (SUM(debe)-SUM(haber)) as saldo 
            FROM ' . self::TABLA . ' 
            WHERE idUsuario = :idUsuario 
            ORDER BY id DESC 
            LIMIT 1');
        $consulta->bindParam(':idUsuario', $idUsuario);
        $consulta->execute();
        $registro = $consulta->fetch();
        
        return $registro ? floatval($registro['saldo']) : 0;
    }
}
?>