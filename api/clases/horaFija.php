
<?php
class HoraFija {
    const TABLA = 'horafija';
    
    private $id;
    private $dia;
    private $hora;
    private $servicio;
    private $idUser;
    
    public function __construct($dia, $hora, $servicio, $idUser, $id = null) {
        $this->dia = $dia;
        $this->hora = $hora;
        $this->servicio = $servicio;
        $this->idUser = $idUser;
        $this->id = $id;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getDia() { return $this->dia; }
    public function getHora() { return $this->hora; }
    public function getServicio() { return $this->servicio; }
    public function getIdUser() { return $this->idUser; }
    
    // Setters
    public function setDia($dia) { $this->dia = $dia; }
    public function setHora($hora) { $this->hora = $hora; }
    public function setServicio($servicio) { $this->servicio = $servicio; }
    public function setIdUser($idUser) { $this->idUser = $idUser; }
    
    public function guardar() {
        $conexion = new Conexion();
        
        if ($this->id) {
            // Actualizar registro existente
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                dia = :dia,
                hora = :hora,
                servicio = :servicio,
                idUser = :idUser
                WHERE id = :id');
            
            $consulta->bindParam(':dia', $this->dia);
            $consulta->bindParam(':hora', $this->hora);
            $consulta->bindParam(':servicio', $this->servicio);
            $consulta->bindParam(':idUser', $this->idUser);
            $consulta->bindParam(':id', $this->id);
            $consulta->execute();
        } else {
            // Insertar nuevo registro
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (dia, hora, servicio, idUser) 
                VALUES (:dia, :hora, :servicio, :idUser)');
            
            $consulta->bindParam(':dia', $this->dia);
            $consulta->bindParam(':hora', $this->hora);
            $consulta->bindParam(':servicio', $this->servicio);
            $consulta->bindParam(':idUser', $this->idUser);
            $consulta->execute();
            
            $this->id = $conexion->lastInsertId();
        }
        
        $conexion = null;
    }
    
    public static function buscarPorId($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, dia, hora, servicio, idUser 
            FROM ' . self::TABLA . ' 
            WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch();
        
        if ($registro) {
            return new self(
                $registro['dia'],
                $registro['hora'],
                $registro['servicio'],
                $registro['idUser'],
                $registro['id']
            );
        }
        
        return null;
    }
    
    public static function buscarPorUsuario($idUser) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, dia, hora, servicio, idUser 
            FROM ' . self::TABLA . ' 
            WHERE idUser = :idUser 
            ORDER BY dia, hora');
        $consulta->bindParam(':idUser', $idUser);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        
        $horasFijas = [];
        foreach ($registros as $registro) {
            $horasFijas[] = new self(
                $registro['dia'],
                $registro['hora'],
                $registro['servicio'],
                $registro['idUser'],
                $registro['id']
            );
        }
        
        return $horasFijas;
    }
    
    public static function buscarPorDia($dia) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, dia, hora, servicio, idUser 
            FROM ' . self::TABLA . ' 
            WHERE dia = :dia 
            ORDER BY hora');
        $consulta->bindParam(':dia', $dia);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        
        $horasFijas = [];
        foreach ($registros as $registro) {
            $horasFijas[] = new self(
                $registro['dia'],
                $registro['hora'],
                $registro['servicio'],
                $registro['idUser'],
                $registro['id']
            );
        }
        
        return $horasFijas;
    }
    
    public static function buscarPorDiaYServicio($dia, $servicio) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, dia, hora, servicio, idUser 
            FROM ' . self::TABLA . ' 
            WHERE dia = :dia AND servicio = :servicio 
            ORDER BY hora');
        $consulta->bindParam(':dia', $dia);
        $consulta->bindParam(':servicio', $servicio);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        
        $horasFijas = [];
        foreach ($registros as $registro) {
            $horasFijas[] = new self(
                $registro['dia'],
                $registro['hora'],
                $registro['servicio'],
                $registro['idUser'],
                $registro['id']
            );
        }
        
        return $horasFijas;
    }
    
    public static function recuperarTodos() {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, dia, hora, servicio, idUser 
            FROM ' . self::TABLA . ' 
            ORDER BY dia, hora');
        $consulta->execute();
        $registros = $consulta->fetchAll();
        
        $horasFijas = [];
        foreach ($registros as $registro) {
            $horasFijas[] = new self(
                $registro['dia'],
                $registro['hora'],
                $registro['servicio'],
                $registro['idUser'],
                $registro['id']
            );
        }
        
        return $horasFijas;
    }
    
    public function eliminar() {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('DELETE FROM ' . self::TABLA . ' WHERE id = :id');
        $consulta->bindParam(':id', $this->id);
        $consulta->execute();
        $conexion = null;
    }
    
    public static function existeHoraFija($dia, $hora, $servicio, $idUser) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT COUNT(*) as total 
            FROM ' . self::TABLA . ' 
            WHERE dia = :dia AND hora = :hora AND servicio = :servicio AND idUser = :idUser');
        $consulta->bindParam(':dia', $dia);
        $consulta->bindParam(':hora', $hora);
        $consulta->bindParam(':servicio', $servicio);
        $consulta->bindParam(':idUser', $idUser);
        $consulta->execute();
        $resultado = $consulta->fetch();
        
        return $resultado['total'] > 0;
    }
}
?>