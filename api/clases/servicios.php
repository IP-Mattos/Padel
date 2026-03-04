<?php
require_once '../Conexion.php';

class Servicio {
    private $id;
    private $usuario;
    private $servicio;
    private $domingo;
    private $lunes;
    private $martes;
    private $miercoles;
    private $jueves;
    private $viernes;
    private $sabado;
    private $intervalo;
    private $nombre;

    const TABLA = 'servicio';

    public function getId()                       { return $this->id; }
    public function getUsuario()                  { return $this->usuario; }
    public function getServicio()                 { return $this->servicio; }
    public function getDomingo()                  { return $this->domingo; }
    public function getLunes()                    { return $this->lunes; }
    public function getMartes()                   { return $this->martes; }
    public function getMiercoles()                { return $this->miercoles; }
    public function getJueves()                   { return $this->jueves; }
    public function getViernes()                  { return $this->viernes; }
    public function getSabado()                   { return $this->sabado; }
    public function getIntervalo()                   { return $this->intervalo; }
    public function getNombre()                        {return $this->nombre; }

    public function setUsuario($usuario)          { $this->usuario = $usuario; }
    public function setServicio($servicio)        { $this->servicio = $servicio; }
    public function setDomingo($domingo)          { $this->domingo = $domingo; }
    public function setLunes($lunes)              { $this->lunes = $lunes; }
    public function setMartes($martes)            { $this->martes = $martes; }
    public function setMiercoles($miercoles)      { $this->miercoles = $miercoles; }
    public function setJueves($jueves)            { $this->jueves = $jueves; }
    public function setViernes($viernes)          { $this->viernes = $viernes; }
    public function setSabado($sabado)            { $this->sabado = $sabado; }
    public function setIntervalo($intervalo)        { $this->intervalo = $intervalo; }
    public function setNombre($nombre)                     { $this->nombre = $nombre; }
    


    public function __construct(
        $id,
        $usuario,
        $servicio,
        $domingo,
        $lunes,
        $martes,
        $miercoles,
        $jueves,
        $viernes,
        $sabado,
        $intervalo,
        $nombre
    ) {
        $this->id          = $id;
        $this->usuario     = $usuario;
        $this->servicio    = $servicio;
        $this->domingo     = $domingo;
        $this->lunes       = $lunes;
        $this->martes      = $martes;
        $this->miercoles   = $miercoles;
        $this->jueves      = $jueves;
        $this->viernes     = $viernes;
        $this->sabado      = $sabado;
        $this->intervalo = $intervalo;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $conexion = new Conexion();
        if ($this->id) {
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                usuario    = :usuario, 
                servicio   = :servicio, 
                domingo    = :domingo, 
                lunes      = :lunes, 
                martes     = :martes, 
                miercoles  = :miercoles, 
                jueves     = :jueves, 
                viernes    = :viernes, 
                sabado     = :sabado,
                intervalo = :intervalo,
                nombre = :nombre
                WHERE id = :id');
            $consulta->bindParam(':usuario', $this->usuario);
            $consulta->bindParam(':servicio', $this->servicio);
            $consulta->bindParam(':domingo', $this->domingo);
            $consulta->bindParam(':lunes', $this->lunes);
            $consulta->bindParam(':martes', $this->martes);
            $consulta->bindParam(':miercoles', $this->miercoles);
            $consulta->bindParam(':jueves', $this->jueves);
            $consulta->bindParam(':viernes', $this->viernes);
            $consulta->bindParam(':sabado', $this->sabado);
            $consulta->bindParam(':intervalo', $this->intervalo);
            $consulta->bindParam(':nombre', $this->nombre);
            $consulta->bindParam(':id', $this->id);
            $consulta->execute();
        } else {
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (usuario, servicio, domingo, lunes, martes, miercoles, jueves, viernes, sabado, intervalo,  nombre)    
                VALUES (:usuario, :servicio, :domingo, :lunes, :martes, :miercoles, :jueves, :viernes, :sabado, :intervalo, :nombre)');
            $consulta->bindParam(':usuario', $this->usuario);
            $consulta->bindParam(':servicio', $this->servicio);
            $consulta->bindParam(':domingo', $this->domingo);
            $consulta->bindParam(':lunes', $this->lunes);
            $consulta->bindParam(':martes', $this->martes);
            $consulta->bindParam(':miercoles', $this->miercoles);
            $consulta->bindParam(':jueves', $this->jueves);
            $consulta->bindParam(':viernes', $this->viernes);
            $consulta->bindParam(':sabado', $this->sabado);
            $consulta->bindParam(':intervalo', $this->intervalo);
            $consulta->bindParam(':nombre', $this->nombre);
            $consulta->execute();
            $this->id = $conexion->lastInsertId();
        }
        $conexion = null;
    }

    public static function buscarPorId($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, usuario, servicio, domingo, lunes, martes, miercoles, jueves, viernes, sabado, intervalo, nombre
            FROM ' . self::TABLA . ' WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro) {
            return new self(
                $registro['id'],
                $registro['usuario'],
                $registro['servicio'],
                $registro['domingo'],
                $registro['lunes'],
                $registro['martes'],
                $registro['miercoles'],
                $registro['jueves'],
                $registro['viernes'],
                $registro['sabado'],
                $registro['intervalo'],
                $registro['nombre']
            );
        } else {
            return false;
        }
    }

    //buscar por servicio y usuario
    public static function buscarPorServicioYUsuario($servicio, $usuario, $dia) {
        $conexion = new Conexion();
        $sql = 'SELECT id, usuario, servicio, '.$dia.', intervalo
            FROM ' . self::TABLA.' WHERE 1=1 AND usuario = :usuario';
        
       
        //si servicio es menor o igual que 4, todos son sobre la cancha 1
        if($servicio <= 4){
            $sql .= ' AND servicio = '.$servicio;
           
        }else{
            $sql.= ' AND servicio = '.$servicio;
            
        }
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':usuario', $usuario);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
    }

    public static function recuperarTodos() {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, usuario, servicio, domingo, lunes, martes, miercoles, jueves, viernes, sabado ,  intervalo, nombre
            FROM ' . self::TABLA);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
    }

    public static function recuperarporTodosFiltro($servicio, $usuario) {
        $conexion = new Conexion();
        $sql = 'SELECT id, usuario, servicio, domingo, lunes, martes, miercoles, jueves, viernes, sabado ,  intervalo,  nombre
            FROM ' . self::TABLA .'WHERE servicio = :servicio AND usuario = :usuario';
        $consulta = $conexion->prepare($sql);
             $consulta = $conexion->prepare($sql);
             $consulta->bindParam(':servicio', $servicio);
             $consulta->bindParam(':usuario', $usuario);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
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