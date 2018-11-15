<?php
    date_default_timezone_set("America/Mexico_City");
    include("../nusoap.php");

    function conectar()
    {
        try{
            $s = new PDO("mysql:host=127.0.0.1;dbname=mstest", 'root', '12345678');
           
            //$s = new PDO("pgsql:host=localhost;port=5432;dbname=mstest;user=postgres;password=postgres");
            return $s;
        }catch(PDOException $e) {
            echo 'Falló la Conexión: '.$e->getMessage();
        }
    }

    function login($datos)
    {
        $conn = conectar();
        try{
            $query = sprintf("SELECT * FROM users WHERE password='%s' AND username='%s'", $datos['password'],$datos['username']);
            $result = $conn->query($query);
            if ($result->fetchColumn() > 0)
            {
                $aux['error'] = ""; //Mensaje de Exito
                $aux['code'] = "0"; //Código de Exito
            }
            else
            {
                $aux['error'] = "Datos invalidos"; 
                $aux['code'] = "-1";                
            }
            $conn = null; //Para cerrar la conexión a la base de datos.
            return $aux;
        }catch(PDOException $e) {
            $aux['error'] = $e->getMessage(); //Mensaje de Error en la Consulta
            $aux['code'] = "-1"; //Error de Consulta            
            $conn = null; //Para cerrar la conexión a la base de datos.
            return $aux;
        }
    }
      
    function consultar($id)
    {
        $conn = conectar();
        
        try{
            $query = sprintf("SELECT * FROM registros WHERE idregistros=%d LIMIT 1",(int)$id);
            $result = $conn->query($query);
            if ($result->rowCount() > 0)
            {
                while($row = $result->fetch())
                {
                    $aux['datos'] = array(
                        'nombre'=>$row['nombre'],
                        'apePat'=>$row['apePat'],
                        'apeMat'=>$row['apeMat'],
                        'domicilio'=>$row['domicilio'],
                        'genero'=>$row['genero']
                    );
                }
                $aux['error'] = ""; //Mensaje de Error
                $aux['code'] = "0"; //Código de Exito
            }
            else
            {
                $aux['datos'] = "";
                $aux['error'] = "¡No hay registro con ese ID!"; 
                $aux['code'] = "-1";                
            }
        }catch(PDOException $e) {
            $aux['datos'] = "";
            $aux['error'] = $e->getMessage(); //Mensaje de Error en la Consulta
            $aux['code'] = "-2"; //Error de Consulta            
        }

        return $aux;
        $conn = null; //Para cerrar la conexión a la base de datos.        
    }

    function obtenerRegistros($datos) 
    {
        return $datos;
        $datos = json_decode($datos,TRUE);

        $login = login($datos);

        return json_encode($login);

        if ($login['code']=="0")
        {
            $respuesta['datos'] = consultar($datos['idregistro']);
            $respuesta['codigo'] = "0";
            $respuesta['mensaje'] = "";
        }
        else if ($login['code']=="-1")
        {
            $respuesta['datos'] = "";
            $respuesta['codigo'] = "-1";
            $respuesta['mensaje'] = $login['error'];
        }
        else if ($login['code']=="-2")
        {
            $respuesta['datos'] = "";
            $respuesta['codigo'] = "-2";
            $respuesta['mensaje'] = $login['error'];            
        }

        return json_encode($respuesta);            
    }

    function consultarNombre($datos)
    {
        return json_encode($datos);
    }

    function resta($datos)
    {
        $datos = json_decode($datos, true);
        $respuesta = login($datos);
 
        if($respuesta['code'] == 0){

            if(!isset($datos['num1']) || !is_numeric($datos['num1'])){
                $respuesta['datos'] = "";
                $respuesta['code'] = "-2";
                $respuesta['error'] = 'No se recibio el primer numero';  
            }
            elseif(!isset($datos['num2']) || !is_numeric($datos['num2'])){
                $respuesta['datos'] = "";
                $respuesta['code'] = "-2";
                $respuesta['error'] = 'No se recibio el segundo numero';  
            }else{
                $respuesta['datos'] = $datos['num1'] - $datos['num2'];
            }
        }

        return json_encode($respuesta);
    }
      
    $server = new soap_server();
    $server->configureWSDL("registros", "urn:registros");
    $server->register("obtenerRegistros",
        array("datos" => "xsd:string"),
        array("return" => "xsd:string"),
        "urn:registros",
        "urn:registros#obtenerRegistros",
        "rpc",
        "encoded",
        "Propociona los registros de una tabla");

    //$server->configureWSDL("registros", "urn:registros");
    $server->register("consultarNombre",
        array("datos" => "xsd:string"),
        array("return" => "xsd:string"),
        "urn:registros",
        "urn:registros#consultarNombre",
        "rpc",
        "encoded",
        "Consulta el Nombre");

    $server->register("resta",
        array("datos" => "xsd:string"),
        array("return" => "xsd:string"),
        "urn:registros",
        "urn:registros#resta",
        "rpc",
        "encoded",
        "Diferencia de dos numeros");


    $server->service(file_get_contents("php://input"));


//$res =  login(array("username" => "karla", "password" =>'1234'));
//$res =  resta(json_encode(array("username" => "karla", "password" =>'1234', 'num1' => 10, 'num2' => 2)));
//print_r($res);
?>