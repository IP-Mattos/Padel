<?php

 class Conexion extends PDO { 
 
/*
    private $tipo_de_base = 'mysql';
    private $host = 'localhost';
    private $nombre_de_base = 'gopadel';
    private $usuario = 'root';
    private $contrasena = ''; 
    private $port = '3307';
    */
  private $tipo_de_base = 'mysql';
  private $host = 'localhost';
  private $nombre_de_base = 'gopadel_gopadel';
  private $usuario = 'gopadel_user';
  private $contrasena = '7t0)Wkz[X3*7';
  private $port = '3306';

    private $options = array(
       PDO::ATTR_PERSISTENT => true, 
       PDO::ATTR_EMULATE_PREPARES => false, 
       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
       PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
    );
    
 
    public function __construct() {
       //Sobreescribo el método constructor de la clase PDO.
       try{
          parent::__construct($this->tipo_de_base.':host='.$this->host.';port='.
          $this->port.';dbname='.$this->nombre_de_base, $this->usuario, $this->contrasena,$this->options);
 
         
       }catch(PDOException $e){
          echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
          exit;
       }
    } 
    
  } 
 
  ?>
