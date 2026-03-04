<?php
require_once '../Conexion.php';

class Usuarios {
    private $id;
    private $nombre;
    private $mail;
    private $usuario;
    private $pass;
    private $estado;
    private $cedula;
    private $celular;
    private $categoria;
    private $vigencia;
    private $beneficiarioDe;
    private $juego;
    private $fechnac;
    private $frase;
    private $profesor;
    private $mascategoria;
    private $imgperfil;
    private $isadmin;
    private $misestrellas; // Nuevo atributo

    const TABLA = 'usuarios';

    public function getId()                       { return $this->id; }
    public function getNombre()                   { return $this->nombre; }
    public function getMail()                     { return $this->mail; }
    public function getUsuario()                  { return $this->usuario; }
    public function getPass()                     { return $this->pass; }
    public function getEstado()                   { return $this->estado; }
    public function getCedula()                   { return $this->cedula; }
    public function getCelular()                  { return $this->celular; }
    public function getCategoria()                { return $this->categoria; }
    public function getVigencia()                 { return $this->vigencia; }
    public function getBeneficiarioDe()           { return $this->beneficiarioDe; }
    public function getJuego()                    { return $this->juego; }
    public function getFechnac()                  { return $this->fechnac; }
    public function getFrase()                    { return $this->frase; }
    public function getProfesor()                 { return $this->profesor; }
    public function getMascategoria()             { return $this->mascategoria; }
    public function getImgperfil()                { return $this->imgperfil; }
    public function getisadmin()                  { return $this->isadmin; }
    public function getMisestrellas()             { return $this->misestrellas; } // Nuevo getter

    public function setNombre($nombre)            { $this->nombre = $nombre; }
    public function setMail($mail)                { $this->mail = $mail; }
    public function setUsuario($usuario)          { $this->usuario = $usuario; }
    public function setPass($pass)                { $this->pass = $pass; }
    public function setEstado($estado)            { $this->estado = $estado; }
    public function setCedula($cedula)            { $this->cedula = $cedula; }
    public function setCelular($celular)          { $this->celular = $celular; }
    public function setCategoria($categoria)      { $this->categoria = $categoria; }
    public function setVigencia($vigencia)        { $this->vigencia = $vigencia; }
    public function setBeneficiarioDe($beneficiarioDe) { $this->beneficiarioDe = $beneficiarioDe; }
    public function setJuego($juego)              { $this->juego = $juego; }
    public function setFechnac($fechnac)          { $this->fechnac = $fechnac; }
    public function setFrase($frase)              { $this->frase = $frase; }
    public function setProfesor($profesor)        { $this->profesor = $profesor; }
    public function setMascategoria($mascategoria){ $this->mascategoria = $mascategoria; }
    public function setImgperfil($imgperfil)      { $this->imgperfil = $imgperfil; }
    public function setisadmin($isadmin)          { $this->isadmin = $isadmin; }
    public function setMisestrellas($misestrellas){ $this->misestrellas = $misestrellas; } // Nuevo setter

    public function __construct(
        $id,
        $nombre,
        $mail,
        $usuario,
        $pass,
        $estado,
        $cedula,
        $celular,
        $categoria,
        $vigencia,
        $beneficiarioDe,
        $juego,
        $fechnac,
        $frase,
        $profesor,
        $mascategoria,
        $imgperfil,
        $isadmin,
        $misestrellas // Nuevo parámetro
    ) {
        $this->id              = $id;
        $this->nombre          = $nombre;
        $this->mail            = $mail;
        $this->usuario         = $usuario;
        $this->pass            = $pass;
        $this->estado          = $estado;
        $this->cedula          = $cedula;
        $this->celular         = $celular;
        $this->categoria       = $categoria;
        $this->vigencia        = $vigencia;
        $this->beneficiarioDe  = $beneficiarioDe;
        $this->juego           = $juego;
        $this->fechnac         = $fechnac;
        $this->frase           = $frase;
        $this->profesor        = $profesor;
        $this->mascategoria    = $mascategoria;
        $this->imgperfil       = $imgperfil;
        $this->isadmin         = $isadmin;
        $this->misestrellas    = $misestrellas; // Nueva asignación
    }

    public function guardar() {
        $conexion = new Conexion();
        if ($this->id) {
            $consulta = $conexion->prepare('UPDATE ' . self::TABLA . ' SET 
                nombre          = :nombre, 
                mail            = :mail, 
                usuario         = :usuario, 
                pass            = :pass, 
                estado          = :estado, 
                cedula          = :cedula, 
                celular         = :celular, 
                categoria       = :categoria, 
                vigencia        = :vigencia, 
                beneficiarioDe  = :beneficiarioDe, 
                juego           = :juego, 
                fechnac         = :fechnac, 
                frase           = :frase, 
                profesor        = :profesor, 
                mascategoria    = :mascategoria, 
                imgperfil       = :imgperfil, 
                isadmin         = :isadmin,
                misestrellas    = :misestrellas 
                WHERE id = :id');
            $consulta->bindParam(':nombre', $this->nombre);
            $consulta->bindParam(':mail', $this->mail);
            $consulta->bindParam(':usuario', $this->usuario);
            $consulta->bindParam(':pass', $this->pass);
            $consulta->bindParam(':estado', $this->estado);
            $consulta->bindParam(':cedula', $this->cedula);
            $consulta->bindParam(':celular', $this->celular);
            $consulta->bindParam(':categoria', $this->categoria);
            $consulta->bindParam(':vigencia', $this->vigencia);
            $consulta->bindParam(':beneficiarioDe', $this->beneficiarioDe);
            $consulta->bindParam(':juego', $this->juego);
            $consulta->bindParam(':fechnac', $this->fechnac);
            $consulta->bindParam(':frase', $this->frase);
            $consulta->bindParam(':profesor', $this->profesor);
            $consulta->bindParam(':mascategoria', $this->mascategoria);
            $consulta->bindParam(':imgperfil', $this->imgperfil);
            $consulta->bindParam(':isadmin', $this->isadmin);
            $consulta->bindParam(':misestrellas', $this->misestrellas); // Nuevo bindParam
            $consulta->bindParam(':id', $this->id);
            $consulta->execute();
        } else {
            $consulta = $conexion->prepare('INSERT INTO ' . self::TABLA . ' 
                (nombre, mail, usuario, pass, estado, cedula, celular, categoria, vigencia, beneficiarioDe, juego, fechnac, frase, profesor, mascategoria, imgperfil, isadmin, misestrellas) 
                VALUES (:nombre, :mail, :usuario, :pass, :estado, :cedula, :celular, :categoria, :vigencia, :beneficiarioDe, :juego, :fechnac, :frase, :profesor, :mascategoria, :imgperfil, :isadmin, :misestrellas)');
            $consulta->bindParam(':nombre', $this->nombre);
            $consulta->bindParam(':mail', $this->mail);
            $consulta->bindParam(':usuario', $this->usuario);
            $consulta->bindParam(':pass', $this->pass);
            $consulta->bindParam(':estado', $this->estado);
            $consulta->bindParam(':cedula', $this->cedula);
            $consulta->bindParam(':celular', $this->celular);
            $consulta->bindParam(':categoria', $this->categoria);
            $consulta->bindParam(':vigencia', $this->vigencia);
            $consulta->bindParam(':beneficiarioDe', $this->beneficiarioDe);
            $consulta->bindParam(':juego', $this->juego);
            $consulta->bindParam(':fechnac', $this->fechnac);
            $consulta->bindParam(':frase', $this->frase);
            $consulta->bindParam(':profesor', $this->profesor);
            $consulta->bindParam(':mascategoria', $this->mascategoria);
            $consulta->bindParam(':imgperfil', $this->imgperfil);
            $consulta->bindParam(':isadmin', $this->isadmin);
            $consulta->bindParam(':misestrellas', $this->misestrellas); // Nuevo bindParam
            $consulta->execute();
            $this->id = $conexion->lastInsertId();
        }
        $conexion = null;
    }
    
    public static function buscarPorId($id) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, nombre, mail, usuario, pass, estado, cedula, celular, categoria, vigencia, beneficiarioDe, juego, fechnac, frase, profesor, mascategoria, imgperfil, isadmin, misestrellas 
            FROM ' . self::TABLA . ' WHERE id = :id');
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro) {
            return new self(
                $registro['id'],
                $registro['nombre'],
                $registro['mail'],
                $registro['usuario'],
                $registro['pass'],
                $registro['estado'],
                $registro['cedula'],
                $registro['celular'],
                $registro['categoria'],
                $registro['vigencia'],
                $registro['beneficiarioDe'],
                $registro['juego'],
                $registro['fechnac'],
                $registro['frase'],
                $registro['profesor'],
                $registro['mascategoria'],
                $registro['imgperfil'],
                $registro['isadmin'],
                $registro['misestrellas'] // Nuevo campo
            );
        } else {
            return false;
        }
        $conexion = null;
    }

    public static function buscarPorCedula($cedula) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, nombre, mail, usuario, pass, estado, cedula, celular, categoria, vigencia, beneficiarioDe, juego, fechnac, frase, profesor, mascategoria, imgperfil, isadmin, misestrellas 
            FROM ' . self::TABLA.' WHERE cedula = :cedula');
        $consulta->bindParam(':cedula', $cedula);
        $consulta->execute();
        $registro = $consulta->fetch();
        if ($registro) {
            return new self(
                $registro['id'],
                $registro['nombre'],
                $registro['mail'],
                $registro['usuario'],
                $registro['pass'],
                $registro['estado'],
                $registro['cedula'],
                $registro['celular'],
                $registro['categoria'],
                $registro['vigencia'],
                $registro['beneficiarioDe'],
                $registro['juego'],
                $registro['fechnac'],
                $registro['frase'],
                $registro['profesor'],
                $registro['mascategoria'],
                $registro['imgperfil'],
                $registro['isadmin'],
                $registro['misestrellas'] // Nuevo campo
            );
        }else {
            return false;
        }
        $conexion = null;
      
    }
    
    public static function recuperarTodos() {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, nombre, mail, usuario, pass, estado, cedula, celular, categoria, vigencia, beneficiarioDe, juego, fechnac, frase, profesor, mascategoria, imgperfil, isadmin, misestrellas 
            FROM ' . self::TABLA);
        $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;

    }
    public static function recuperarPorFiltro($filtro) {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, nombre, usuario, categoria,imgperfil FROM ' . self::TABLA . '
        WHERE nombre LIKE "%'.$filtro.'%" OR usuario LIKE "%'.$filtro.
        '%" OR celular LIKE "%'.$filtro.'%" OR cedula LIKE "%'.$filtro.'%"');
      $consulta->execute();
        $registros = $consulta->fetchAll();
        return $registros;
       
    }

    public static function recuperarProfesores() {
        $conexion = new Conexion();
        $consulta = $conexion->prepare('SELECT id, nombre, mail, usuario, estado, cedula, celular, categoria, vigencia, beneficiarioDe, 
        juego, fechnac, frase, profesor, mascategoria, imgperfil, isadmin, misestrellas 
            FROM ' . self::TABLA . ' where profesor > 0' );
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
?>