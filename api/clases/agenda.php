<?php
// File: c:\wamp64\www\www\www\elGO\gopadel\api\clases\agenda.php
require_once '../Conexion.php';

class Agenda {
    private $id;
    private $fecha;
    private $hora;
    private $idUsuario;
    private $estado;
    private $timeEstado;
    private $servicio;
    private $idUserRival;
    private $estadoRival;
    private $mensaje;
    private $invitado1;
    private $invitado2;
    private $invitado3;

    const TABLA = 'agenda';

    public function getId()                       { return $this->id; }
    public function getFecha()                    { return $this->fecha; }
    public function getHora()                     { return $this->hora; }
    public function getIdUsuario()                { return $this->idUsuario; }
    public function getEstado()                   { return $this->estado; }
    public function getTimeEstado()               { return $this->timeEstado; }
    public function getServicio()                 { return $this->servicio; }
    public function getIdUserRival()              { return $this->idUserRival; }
    public function getEstadoRival()              { return $this->estadoRival; }
    public function getMensaje()                  { return $this->mensaje; }
    public function getInvitado1()                { return $this->invitado1; }
    public function getInvitado2()                { return $this->invitado2; }
    public function getInvitado3()                { return $this->invitado3; }

    public function setFecha($fecha)              { $this->fecha = $fecha; }
    public function setHora($hora)                { $this->hora = $hora; }
    public function setIdUsuario($idUsuario)      { $this->idUsuario = $idUsuario; }
    public function setEstado($estado)            { $this->estado = $estado; }
    public function setTimeEstado($timeEstado)    { $this->timeEstado = $timeEstado; }
    public function setServicio($servicio)        { $this->servicio = $servicio; }
    public function setIdUserRival($idUserRival)  { $this->idUserRival = $idUserRival; }
    public function setEstadoRival($estadoRival)  { $this->estadoRival = $estadoRival; }
    public function setMensaje($mensaje)          { $this->mensaje = $mensaje; }
    public function setInvitado1($invitado1)      { $this->invitado1 = $invitado1; }
    public function setInvitado2($invitado2)      { $this->invitado2 = $invitado2; }
    public function setInvitado3($invitado3)      { $this->invitado3 = $invitado3; }

    public function __construct($id, $fecha, $hora, $idUsuario, $estado, $timeEstado, $servicio,
     $idUserRival, $estadoRival, $mensaje, $invitado1, $invitado2, $invitado3) {
        $this->id         = $id;
        $this->fecha      = $fecha;
        $this->hora       = $hora;
        $this->idUsuario  = $idUsuario;
        $this->estado     = $estado;
        $this->timeEstado = $timeEstado;
        $this->servicio   = $servicio;
        $this->idUserRival = $idUserRival;
        $this->estadoRival = $estadoRival;
        $this->mensaje    = $mensaje;
        $this->invitado1  = $invitado1;
        $this->invitado2  = $invitado2;
        $this->invitado3  = $invitado3;
    }

    public function guardar() {
        $conexion = new Conexion();
        if ($this->id) {
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                fecha      = :fecha, 
                hora       = :hora, 
                idUsuario  = :idUsuario, 
                estado     = :estado, 
                timeEstado = :timeEstado, 
                servicio   = :servicio,
                idUserRival = :idUserRival,
                estadoRival = :estadoRival,
                mensaje = :mensaje,
                invitado1 = :invitado1,
                invitado2 = :invitado2,
                invitado3 = :invitado3 
                WHERE id = :id');
            $consulta->bindParam(':id', $this->id);
        } else {
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (fecha, hora, idUsuario, estado, timeEstado, servicio, idUserRival, estadoRival, mensaje, invitado1, invitado2, invitado3)     
                VALUES (:fecha, :hora, :idUsuario, :estado, :timeEstado, :servicio, :idUserRival, :estadoRival, :mensaje, :invitado1, :invitado2, :invitado3)');
        }
        $consulta->bindParam(':fecha', $this->fecha);
        $consulta->bindParam(':hora', $this->hora);
        $consulta->bindParam(':idUsuario', $this->idUsuario);
        $consulta->bindParam(':estado', $this->estado);
        $consulta->bindParam(':timeEstado', $this->timeEstado);
        $consulta->bindParam(':servicio', $this->servicio);
        $consulta->bindParam(':idUserRival', $this->idUserRival);
        $consulta->bindParam(':estadoRival', $this->estadoRival);
        $consulta->bindParam(':mensaje', $this->mensaje);
        $consulta->bindParam(':invitado1', $this->invitado1);
        $consulta->bindParam(':invitado2', $this->invitado2);
        $consulta->bindParam(':invitado3', $this->invitado3);
        $consulta->execute();
        if (!$this->id) {
            $this->id = $conexion->lastInsertId();
        }
        $conexion = null;
    }

    public static function buscarPorId($id) {
        $conexion = new Conexion();
        $sql = 'SELECT id, fecha, hora, idUsuario, estado, timeEstado, servicio, idUserRival, estadoRival, mensaje, invitado1, invitado2, invitado3 
            FROM '. self::TABLA.' WHERE id = :id';
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro) {
            return new self(
                $registro['id'],
                $registro['fecha'],
                $registro['hora'],
                $registro['idUsuario'],
                $registro['estado'],
                $registro['timeEstado'],
                $registro['servicio'],
                $registro['idUserRival'],
                $registro['estadoRival'],
                $registro['mensaje'],
                $registro['invitado1'],
                $registro['invitado2'],
                $registro['invitado3']
            );
        } else {
            return false;
        }
    }

    public static function recuperarTodos() {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, fecha, hora, idUsuario, estado, timeEstado, servicio, idUserRival, estadoRival, mensaje, invitado1, invitado2, invitado3  
            FROM ' . self::TABLA);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
    }

    public static function recuperarTodosIdUserFech($fechaDesde,$fechaHasta, $idUsuario) {
        $conexion = new Conexion();
        $sql = 'SELECT id, fecha, hora, idUsuario, estado, timeEstado, servicio, idUserRival, estadoRival, mensaje, invitado1, invitado2, invitado3   
            FROM ' . self::TABLA . ' WHERE fecha BETWEEN :fechaDesde AND :fechaHasta AND 
            (idUsuario = :idUsuario or idUserRival = :idRival or invitado1 = :inv1 or invitado2 = :inv2 or invitado3 = :inv3) ORDER BY fecha ASC, hora ASC';
        //echo $sql;
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':fechaDesde', $fechaDesde);
        $consulta->bindParam(':fechaHasta', $fechaHasta);
        $consulta->bindParam(':idUsuario', $idUsuario);
        $consulta->bindParam(':idRival', $idUsuario);
        $consulta->bindParam(':inv1', $idUsuario);
        $consulta->bindParam(':inv3', $idUsuario);
        $consulta->bindParam(':inv2', $idUsuario);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
    }

    public static function recuperarTodosLasReservas($fechaDesde,$fechaHasta) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT s.id, s.fecha, s.hora, s.idUsuario, s.estado, s.timeEstado, s.servicio, s.idUserRival, s.estadoRival, s.mensaje, 
        s.invitado1, s.invitado2, s.invitado3, 
        (select count(h.id) FROM horafija h WHERE h.dia = DAYOFWEEK(s.fecha) AND h.servicio = s.servicio AND h.hora = s.hora) as horaFija  
            FROM ' . self::TABLA . ' s WHERE s.fecha BETWEEN :fechaDesde AND :fechaHasta ORDER BY s.fecha ASC, s.hora ASC');
        $consulta->bindParam(':fechaDesde', $fechaDesde);
        $consulta->bindParam(':fechaHasta', $fechaHasta);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
    }

    //recuperar reservas en estado 1 o que no tenga los 4 medios de pago
    public static function recuperarReservasSinConfirmarOpagar($fechaDesde,$fechaHasta) {
        $conexion = new Conexion();
        $sql = 'SELECT s.id, s.fecha, s.hora, s.idUsuario, s.estado, s.timeEstado, s.servicio, s.idUserRival, s.estadoRival, s.mensaje, 
        s.invitado1, s.invitado2, s.invitado3, 
        (select count(h.id) FROM horafija h WHERE h.dia = DAYOFWEEK(s.fecha) AND h.servicio = s.servicio AND h.hora = s.hora) as horaFija  
            FROM ' . self::TABLA . ' s WHERE s.fecha BETWEEN :fechaDesde AND :fechaHasta AND (s.estado = 1 OR (s.estado = 2 AND 
            (select count(p.id) FROM pagos p WHERE p.idAgenda = s.id) = 0)) ORDER BY s.fecha ASC, s.hora ASC';
            //echo $sql . $fechaDesde . " - " . $fechaHasta;
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':fechaDesde', $fechaDesde);
        $consulta->bindParam(':fechaHasta', $fechaHasta);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;

    }

    //recuperar si usuario tiene reserva en estado 1
    public static function tieneReserva($idUsuario) {
        $conexion = new Conexion();
        $sql = 'SELECT COUNT(*) as cantidad
            FROM '. self::TABLA.' WHERE idUsuario = :idUsuario AND estado = 1';
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':idUsuario', $idUsuario);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro['cantidad'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function borrarRegistro($id) {
        try {
            $conexion = new Conexion();
            $consulta = $conexion->prepare('DELETE FROM ' . self::TABLA . ' WHERE id = :id');
            $consulta->bindParam(':id', $id);
            $consulta->execute();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}