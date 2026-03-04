<?php
require_once '../Conexion.php';

class FdpAgenda {
    private $id;
    private $fecha;
    private $idAgenda;
    private $idUsuario;
    private $fdpUsuario;
    private $idInvitado1;
    private $fdpInvitado1;
    private $idInvitado2;
    private $fdpInvitado2;
    private $idInvitado3;
    private $fdpInvitado3;
    private $impUsu;
    private $impInv1;
    private $impInv2;
    private $impInv3;

    const TABLA = 'pagos';

    public function getId() { return $this->id; }
    public function getFecha() { return $this->fecha; }
    public function getIdAgenda() { return $this->idAgenda; }
    public function getIdUsuario() { return $this->idUsuario; }
    public function getFdpUsuario() { return $this->fdpUsuario; }
    public function getIdInvitado1() { return $this->idInvitado1; }
    public function getFdpInvitado1() { return $this->fdpInvitado1; }
    public function getIdInvitado2() { return $this->idInvitado2; }
    public function getFdpInvitado2() { return $this->fdpInvitado2; }
    public function getIdInvitado3() { return $this->idInvitado3; }
    public function getFdpInvitado3() { return $this->fdpInvitado3; }
    public function getImpUsu() { return $this->impUsu; }
    public function getImpInv1() { return $this->impInv1; }
    public function getImpInv2() { return $this->impInv2; }
    public function getImpInv3() { return $this->impInv3; }

    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setIdAgenda($idAgenda) { $this->idAgenda = $idAgenda; }
    public function setIdUsuario($idUsuario) { $this->idUsuario = $idUsuario; }
    public function setFdpUsuario($fdpUsuario) { $this->fdpUsuario = $fdpUsuario; }
    public function setIdInvitado1($idInvitado1) { $this->idInvitado1 = $idInvitado1; }
    public function setFdpInvitado1($fdpInvitado1) { $this->fdpInvitado1 = $fdpInvitado1; }
    public function setIdInvitado2($idInvitado2) { $this->idInvitado2 = $idInvitado2; }
    public function setFdpInvitado2($fdpInvitado2) { $this->fdpInvitado2 = $fdpInvitado2; }
    public function setIdInvitado3($idInvitado3) { $this->idInvitado3 = $idInvitado3; }
    public function setFdpInvitado3($fdpInvitado3) { $this->fdpInvitado3 = $fdpInvitado3; }
    public function setImpUsu($impUsu) { $this->impUsu = $impUsu; }
    public function setImpInv1($impInv1) { $this->impInv1 = $impInv1; }
    public function setImpInv2($impInv2) { $this->impInv2 = $impInv2; }
    public function setImpInv3($impInv3) { $this->impInv3 = $impInv3; }

    public function __construct($id, $fecha, $idAgenda, $idUsuario, $fdpUsuario, $idInvitado1, $fdpInvitado1, $idInvitado2, $fdpInvitado2, $idInvitado3, $fdpInvitado3, $impUsu, $impInv1, $impInv2, $impInv3) {
        $this->id = $id;
        $this->fecha = $fecha;
        $this->idAgenda = $idAgenda;
        $this->idUsuario = $idUsuario;
        $this->fdpUsuario = $fdpUsuario;
        $this->idInvitado1 = $idInvitado1;
        $this->fdpInvitado1 = $fdpInvitado1;
        $this->idInvitado2 = $idInvitado2;
        $this->fdpInvitado2 = $fdpInvitado2;
        $this->idInvitado3 = $idInvitado3;
        $this->fdpInvitado3 = $fdpInvitado3;
        $this->impUsu = $impUsu;
        $this->impInv1 = $impInv1;
        $this->impInv2 = $impInv2;
        $this->impInv3 = $impInv3;
    }

    public function guardar() {
        $conexion = new Conexion();
        $fdpAgenda = FdpAgenda::buscarporId($this->idAgenda);
        
        if ($fdpAgenda) {
            $idAg = $fdpAgenda->getId();
            //echo "update";
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                fecha = :fecha,
                idAgenda = :idAgenda, 
                idUsuario = :idUsuario, 
                fdpUsuario = :fdpUsuario, 
                IdInvitado1 = :idInvitado1, 
                fdpInvitado1 = :fdpInvitado1, 
                idInvitado2 = :idInvitado2, 
                fdpInvitado2 = :fdpInvitado2, 
                idInvitado3 = :idInvitado3, 
                fdpInvitado3 = :fdpInvitado3,
                impUsu = :impUsu,
                impInv1 = :impInv1,
                impInv2 = :impInv2,
                impInv3 = :impInv3
                WHERE id = :id');
            $consulta->bindParam(':id', $idAg);
            $consulta->bindParam(':fecha', $this->fecha);
            $consulta->bindParam(':idAgenda', $this->idAgenda);
            $consulta->bindParam(':idUsuario', $this->idUsuario);
            $consulta->bindParam(':fdpUsuario', $this->fdpUsuario);
            $consulta->bindParam(':idInvitado1', $this->idInvitado1);
            $consulta->bindParam(':fdpInvitado1', $this->fdpInvitado1);
            $consulta->bindParam(':idInvitado2', $this->idInvitado2);
            $consulta->bindParam(':fdpInvitado2', $this->fdpInvitado2);
            $consulta->bindParam(':idInvitado3', $this->idInvitado3);
            $consulta->bindParam(':fdpInvitado3', $this->fdpInvitado3);
            $consulta->bindParam(':impUsu', $this->impUsu);
            $consulta->bindParam(':impInv1', $this->impInv1);
            $consulta->bindParam(':impInv2', $this->impInv2);
            $consulta->bindParam(':impInv3', $this->impInv3);
            $consulta->execute();
        } else {
            //echo "insert";
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (fecha, idAgenda, idUsuario, fdpUsuario, IdInvitado1, fdpInvitado1, idInvitado2, fdpInvitado2, idInvitado3, fdpInvitado3, impUsu, impInv1, impInv2, impInv3) 
                VALUES (:fecha, :idAgenda, :idUsuario, :fdpUsuario, :idInvitado1, :fdpInvitado1, :idInvitado2, :fdpInvitado2, :idInvitado3, :fdpInvitado3, :impUsu, :impInv1, :impInv2, :impInv3)');
        }
        $consulta->bindParam(':fecha', $this->fecha);
        $consulta->bindParam(':idAgenda', $this->idAgenda);
        $consulta->bindParam(':idUsuario', $this->idUsuario);
        $consulta->bindParam(':fdpUsuario', $this->fdpUsuario);
        $consulta->bindParam(':idInvitado1', $this->idInvitado1);
        $consulta->bindParam(':fdpInvitado1', $this->fdpInvitado1);
        $consulta->bindParam(':idInvitado2', $this->idInvitado2);
        $consulta->bindParam(':fdpInvitado2', $this->fdpInvitado2);
        $consulta->bindParam(':idInvitado3', $this->idInvitado3);
        $consulta->bindParam(':fdpInvitado3', $this->fdpInvitado3);
        $consulta->bindParam(':impUsu', $this->impUsu);
        $consulta->bindParam(':impInv1', $this->impInv1);
        $consulta->bindParam(':impInv2', $this->impInv2);
        $consulta->bindParam(':impInv3', $this->impInv3);
        $consulta->execute();
        if (!$this->id) {
            $this->id = $conexion->lastInsertId();
        }
        $conexion = null;
    }

    public static function buscarPorId($idAgenda) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT * FROM ' . self::TABLA.' WHERE idAgenda = :idAgenda');
        $consulta->bindParam(':idAgenda', $idAgenda);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro) {
            return new FdpAgenda(
                $registro['id'],
                $registro['fecha'],
                $registro['idAgenda'],
                $registro['idUsuario'],
                $registro['fdpUsuario'],
                $registro['IdInvitado1'],
                $registro['fdpInvitado1'],
                $registro['idInvitado2'],
                $registro['fdpInvitado2'],
                $registro['idInvitado3'],
                $registro['fdpInvitado3'],
                $registro['impUsu'],
                $registro['impInv1'],
                $registro['impInv2'],
                $registro['impInv3']
            );
        } else {
            return null;
        }
    }

        
public static function recuperarPorRangoFechas($fechaDesde, $fechaHasta) {
    $conexion = new Conexion();
    $consulta = $conexion->prepare('SELECT id, fecha, idAgenda, idUsuario, fdpUsuario, IdInvitado1, fdpInvitado1, idInvitado2, fdpInvitado2, idInvitado3, fdpInvitado3, impUsu, impInv1, impInv2, impInv3 
        FROM ' . self::TABLA . ' 
        WHERE fecha BETWEEN :fechaDesde AND :fechaHasta 
        ORDER BY fecha ASC');
    $consulta->bindParam(':fechaDesde', $fechaDesde);
    $consulta->bindParam(':fechaHasta', $fechaHasta);
    $consulta->execute();
    $registros = $consulta->fetchAll();
    $pagos = [];
    foreach ($registros as $registro) {
        $pagos[] = new self(
            $registro['id'],
            $registro['fecha'],
            $registro['idAgenda'],
            $registro['idUsuario'],
            $registro['fdpUsuario'],
            $registro['IdInvitado1'],
            $registro['fdpInvitado1'],
            $registro['idInvitado2'],
            $registro['fdpInvitado2'],
            $registro['idInvitado3'],
            $registro['fdpInvitado3'],
            $registro['impUsu'],
            $registro['impInv1'],
            $registro['impInv2'],
            $registro['impInv3']
        );
    }
    return $pagos;
}

public static function recuperarPorUsuarioYFechas($idUsuario, $fechaDesde, $fechaHasta) {
    $conexion = new Conexion();
    $consulta = $conexion->prepare('SELECT id, fecha, idAgenda, idUsuario, fdpUsuario, IdInvitado1, fdpInvitado1, idInvitado2, fdpInvitado2, idInvitado3, fdpInvitado3, impUsu, impInv1, impInv2, impInv3 
        FROM ' . self::TABLA . ' 
        WHERE (idUsuario = :idUsuario OR IdInvitado1 = :idInv1 OR idInvitado2 = :idInv2 OR idInvitado3 = :idInv3) 
        AND fecha BETWEEN :fechaDesde AND :fechaHasta 
        ORDER BY fecha ASC');
    $consulta->bindParam(':idUsuario', $idUsuario);
    $consulta->bindParam(':idInv1', $idUsuario);
    $consulta->bindParam(':idInv2', $idUsuario);
    $consulta->bindParam(':idInv3', $idUsuario);
    $consulta->bindParam(':fechaDesde', $fechaDesde);
    $consulta->bindParam(':fechaHasta', $fechaHasta);
    $consulta->execute();
    $registros = $consulta->fetchAll();
    $pagos = [];
    foreach ($registros as $registro) {
        $pagos[] = new self(
            $registro['id'],
            $registro['fecha'],
            $registro['idAgenda'],
            $registro['idUsuario'],
            $registro['fdpUsuario'],
            $registro['IdInvitado1'],
            $registro['fdpInvitado1'],
            $registro['idInvitado2'],
            $registro['fdpInvitado2'],
            $registro['idInvitado3'],
            $registro['fdpInvitado3'],
            $registro['impUsu'],
            $registro['impInv1'],
            $registro['impInv2'],
            $registro['impInv3']
        );
    }
    return $pagos;
}
        public static function buscarPorIdAgenda($idAgenda) {
           $conexion = new Conexion();
            $consulta = $conexion->prepare('SELECT id,fecha, idAgenda, idUsuario, fdpUsuario, IdInvitado1, fdpInvitado1, idInvitado2, fdpInvitado2, idInvitado3, fdpInvitado3, impUsu, impInv1, impInv2, impInv3 FROM '. self::TABLA.' WHERE idAgenda = :idAgenda');
            $consulta->bindParam(':idAgenda', $idAgenda);
            $consulta->execute();
            $registro = $consulta->fetchAll();
            if ($registro) {
                //retornar array campo valor
                return $registro;
            } else {
                return null;
            } 
        }


}
?>