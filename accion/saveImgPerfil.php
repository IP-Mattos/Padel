<?php
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once "../api/utils.php";
        $token = $_COOKIE['goCookToken'];
       
                //echo $_FILES['imgPerfilUser']['type']."<br>";

                $arrTipo        = explode("/",$_FILES['imgPerfilUser']['type']);
                $tipo           = $arrTipo[1];
                $tamanio        = $_FILES['imgPerfilUser']['size'];
                                
                $archivoName    = $_FILES['imgPerfilUser']['tmp_name'];
                $peso           = $_FILES['imgPerfilUser']['size']/1000;
                $nameProv       = "prov_".$_POST['idUser'].".".$tipo;
                $nameDef        = date("Ymdhis")."_".$_POST['idUser'].".".$tipo;
        
                $dir = "./imgPerfilUser/";
            /*
                echo "<br>tipo = ".$tipo;
                echo "<br>tamanio = ".$tamanio;
                echo "<br>archivoName = ".$archivoName;
                echo "<br>nameProv = ".$nameProv;
                echo "<br>nameDef = ".$nameDef;
                echo "<br>peso = ".$peso , " kb";
            */  
        
                $destination = $dir.$nameProv;
                move_uploaded_file($archivoName, $destination);
        
            
                /// luego de mover la imagen a  la crpeta la redimencionamos
        
                // Creamos la variable que contiene la imagen
                // Uso de la librería imagick
                $im = new imagick($destination);
                $imageprops = $im->getImageGeometry();
                // reconocimiento de la altura y ancho de la imagen
                $width = $imageprops['width'];
                $height = $imageprops['height'];
                // Nueva altura y ancho
                if($width > $height){
                    $newHeight = 300;
                    $newWidth = (300 / $height) * $width;
                }else{
                    $newWidth = 300;
                    $newHeight = (300 / $width) * $height;
                }
                $im->resizeImage($newWidth,$newHeight, imagick::FILTER_BOX, true);
        
               
                //$im->cropImage (280,280,0,0);
                // Escribimos la nueva imagen redimensionada
                $im->writeImage( $dir.$nameDef );

                
                $_SESSION["userImgPerfil"] =  $nameDef;
    
        
                $_SESSION['mensaje_alta'] = "ARCHIVO GUARDADO CORRECTAMENTE";
                unlink($dir.$nameProv);

                updateUserImage($token,$_POST['idUser'],$nameDef);
            
       

       
?>