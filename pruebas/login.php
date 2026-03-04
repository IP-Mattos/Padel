        
<?php      
    if(!isset($_SESSION)) { 
        session_start(); 
      }

    $elUs = "marce";
    $pass = "1234";

    $data = array("UsUsuario"=>$elUs, "UsPasword"=>$pass);
        $ch = curl_init("https://gopadel.uy/api/getLoginusuario/login");
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //enable headers
            curl_setopt($ch, CURLOPT_HEADER, 1);
            //get only headers
            //curl_setopt($ch, CURLOPT_NOBODY, 1);
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            
            // $output contains the output string
            $response = curl_exec($ch);
            curl_close($ch);

            $data = explode("\n",$response);
            $headers['status'] = $data[0];
            array_shift($data);
            print_r($data);

            foreach($data as $part){
                $middle=explode(":",$part,2);
                if ($middle[0]==="Authorization"){
                    $_SESSION["userToken"]=trim($middle[1]);
                }
                if ($middle[0]==="userid"){
                    $_SESSION["userId"]=trim($middle[1]);
                }
                if ($middle[0]==="useruser"){
                    $_SESSION["userUser"]=trim($middle[1]);
                }
                if ($middle[0]==="userrool"){
                    $_SESSION["userRool"]=trim($middle[1]);
                }
            }
 
            //header('Location:../index.php');
?>