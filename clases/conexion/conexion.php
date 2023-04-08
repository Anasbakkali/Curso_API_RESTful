<?php

class conexion{

    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $conexion;
    function __construct(){
        // esta variable almecena la información de la funcion a la que llama

    
        $listadatos = $this->datosConexion();

        //recoremos listadatos y almaecnamos la info en los atributos
        foreach ($listadatos as $key => $value) {
            $this->server = $value['server'];
            $this->user = $value['user'];
            $this->password = $value['password'];
            $this->database = $value['database'];
            $this->port = $value['port'];
        }

        $this->conexion = new mysqli($this->server,$this->user,$this->password,$this->database,$this->port);
        if($this->conexion->connect_errno){
            echo "Fallo de conexion";
            die();
        }

    }

    // En esta funcion cojemos los datos del archivo config y lo devolvemos
 private function datosConexion(){
        // aqui obtenemos la ruta
        $direccion = dirname(__FILE__);
        // aqui añadimos a un array de datos la ruta completa del archivo config
        $jsondata = file_get_contents($direccion . "/" . "config");
        return json_decode($jsondata, true);
    }


    //Esta funcion lo que hace es convertir un array en UTF-8
    private function convertirUTF8($array){
        //recorre el array y si encuentra algun item sin codificar lo codifica a utf-8
        array_walk_recursive($array,function(&$item,$key){
            if(!mb_detect_encoding($item,'utf-8',true)){
                $item = $item;
            }
        });
        return $array;
    }

    // esta funcion devuelve los datos de una consulta a la BD convertidos a Utf-8
    public function obtenerDatos($sqlstr){
        $results = $this->conexion->query($sqlstr);
        $resultArray = array();
        foreach ($results as $key) {
            $resultArray[] = $key;
        }
        return $this->convertirUTF8($resultArray);

    }
    // devuelve el numero de filas afectadas en la base de datos
    public function nonQuery($sqlstr){
        $results = $this->conexion->query($sqlstr);
        return $this->conexion->affected_rows;
    }


    //inserta en la bd y nos devuelve el id que ha ingresado
    public function nonQueryId($sqlstr){
        $results = $this->conexion->query($sqlstr);
         $filas = $this->conexion->affected_rows;
         if($filas >= 1){
            return $this->conexion->insert_id;
         }else{
             return 0;
         }
    }

    protected function encriptar($string){
        return md5($string);
    }


   
}

