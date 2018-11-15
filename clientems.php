<?php
    date_default_timezone_set("America/Mexico_City");
    include("nusoap.php");
        function suma($url, $data) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            

            $res = json_decode($output, true);
            return $res;
        }

        function wservice($urlWsdl, $method, $datos){
            $cliente = new nusoap_client($urlWsdl,'wsdl');
            $error = $cliente->getError();
            if ($error) 
            {
                echo "<strong>Error desde la apertura</strong>".$error;
            }
              
            $result = $cliente->call($method, $datos);

            //print_r($cliente->response);
            if ($cliente->fault) {
                echo "Fault: ";
                echo $result;
            }
            else {
                $error = $cliente->getError();
                if ($error) {
                    echo "Error ".$error;
                }
                else {
                    $result = json_decode($result, true);
                }
            } 

            return $result;
        }


    //Configuracion de la llamada a cada microservicio
    switch($_POST['operacion']) {
        case 'suma':
             $datos = array(
                'user' => 'user',
                'password' => '12345',
                'a' => $_POST['num1'],
                'b' =>  $_POST['num2'],
            );
            $resultado = suma('http://132.248.63.20/sitio2/public/index.php/suma', $datos);
            
            if($resultado['status'] != 0){
                echo $resultado['msj'];
            } else {
                echo $resultado['data'];
            }


            break;
        case 'resta':
            $urlWsdl = 'http://132.248.63.141/ms/server.php?wsdl';
            $method = 'resta';
            $datos = array(
                'username' => 'karla',
                'password' => '1234',
                'num1' => $_POST['num1'],
                'num2' =>  $_POST['num2'],
            );

            $datos = array('datos' => json_encode($datos));

            $resultado = wservice($urlWsdl, $method, $datos);

            if($resultado['code'] != 0){
                echo $resultado['error'];
            } else {
                echo $resultado['datos'];
            }

            break;
        case 'multiplica':
            $urlWsdl = 'http://132.248.63.140/ms/server.php?wsdl';
            $method = 'multiplicacion';
            $datos = array(
                'numeros' => $_POST['num1'].','. $_POST['num2'],
                'datos' => json_encode(array(
                    'username' => 'admin',
                    'password' => '9542931e640c671a60ea44a954b249c179da1239'
                ))
            );

            $resultado = wservice($urlWsdl, $method, $datos);

            if($resultado['codigo'] != 0){
                echo $resultado['mensaje'];
            } else {
                echo $resultado['datos'];
            }
            break;
        case 'divide':
            $urlWsdl = 'http://132.248.63.140/ms/server.php?wsdl';
            $method = 'division';
            $datos = array(
                'numeros' => $_POST['num1'].','. $_POST['num2'],
                'datos' => json_encode(array(
                    'username' => 'admin',
                    'password' => '9542931e640c671a60ea44a954b249c179da1239'
                ))
            );

            $resultado = wservice($urlWsdl, $method, $datos);

            if($resultado['codigo'] != 0){
                echo $resultado['mensaje'];
            } else {
                echo $resultado['datos'];
            }
            break;
        case 'raiz':
            $urlWsdl = 'http://orion.dgsca.unam.mx/ms/server.php?wsdl';
            $method = 'calcularRaiz';
            $datos = array(
                'usu_email' => 'malag@unam.mx',
                'usu_passwd' => '1234',
                'numero' => $_POST['num1'],
            );
            $datos = array('datos' => json_encode($datos));
            $resultado = wservice($urlWsdl, $method, $datos);

            if($resultado['exito'] != 1){
                echo $resultado['mensaje'];
            } else {
                echo $resultado['resultado'];
            }
            break;
        case 'exponencial':
            $urlWsdl = 'https://www.althek.com/ws/server.php?wsdl';
            $method = 'obtenerRegistros';
            $datos = array(
                'username' => 'usrexpo',
                'password' => '9542931e640c671a60ea44a954b249c179da1240',
                'valor' => $_POST['num1'],
                'exponente' =>  $_POST['num2'],
            );
            $datos = array('datos' => json_encode($datos));
            $resultado = wservice($urlWsdl, $method, $datos);

            if($resultado['codigo'] != 0){
                echo $resultado['mensaje'];
            } else {
                echo $resultado['datos'];
            }

            break;
    }
?>