<?php

require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class auth extends conexion{

    // creamos la funcion login que va a manejar repuestas en funcion de los parametros de entrada
    public function login($json){
        $_respuestas = new respuestas();
    // convertimos el json en un array y ponemos el true para convertirlo en array asociativo 
        $datos = json_decode($json,true);


        //Si no existe usuario o contraseña le mandamoes el error 400s
    if(!isset($datos['usuario']) || !isset($datos["password"])){
        
        return $_respuestas->error_400();
    }else{
        //Guardamos la informacion en las variables
        $usuario = $datos['usuario'];
        $password = $datos['password'];
        //encriptamos la contraseña que nos envia el usuario
        $password = parent::encriptar($password);
        // Guardamos los datos del metodo en la variable $datos y le pasamos por parametro el usuario
        $datos = $this->obtenerDatosUsuario($usuario);

        //Si existen los datos
        if($datos){
            //verificar si la contraseña es igual
                if($password == $datos[0]['Password']){
                    //Si el estado es igual a activo
                        if($datos[0]['Estado'] == "Activo"){
                            //creamos el token
                            $verificar  = $this->insertarToken($datos[0]['UsuarioId']);
                            if($verificar){
                                    // si se guardo
                                     // pillamos los datos de reponse y lo igualamos a la variable result
                                    $result = $_respuestas->response;
                                    //esto es lo que le vamos a pasar al usuario un token
                                    $result["result"] = array(
                                        //cojemos la informacion de la variable que anteiromente allamado al metodo
                                        // y le ha pasado un token, ahora guardamos el token en un array.
                                        "token" => $verificar
                                    );
                                    return $result;
                            }else{
                                    //error al guardar
                                    return $_respuestas->error_500("Error interno, No hemos podido guardar");
                            }
                        }else{
                            //el usuario esta inactivo
                            return $_respuestas->error_200("El usuario esta inactivo");
                        }
                }else{
                    //la contraseña no es igual
                    return $_respuestas->error_200("El password es invalido");
                }
        }else{
            //no existe el usuario
            return $_respuestas->error_200("El usuaro $usuario  no existe ");
        }
    }
        }

        //Realizamos una consulta a la BD y si encuentra un usario que tiene el mismo correo nos devuelve los datos de ese usuario
        private function obtenerDatosUsuario($correo){
            $query = "SELECT UsuarioId,Password,Estado FROM usuarios WHERE Usuario = '$correo'";
            // Parent sirve para indicar que es un metodo de la clase padre(conexion)
            $datos = parent::obtenerDatos($query);
            if(isset($datos[0]["UsuarioId"])){
                return $datos;
            }else{
                return 0;
            }
        }

        private function insertarToken($usuarioid){
            $val = true;
            $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
            $date = date("Y-m-d H:i");
            $estado = "Activo";
            $query = "INSERT INTO usuarios_token (UsuarioId,Token,Estado,Fecha)VALUES('$usuarioid','$token','$estado','$date')";
            $verifica = parent::nonQuery($query);
            if($verifica){
                return $token;
            }else{
                return 0;
            }
        }


}

