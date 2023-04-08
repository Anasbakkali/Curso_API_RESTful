<?php


require_once 'clases/auth.class.php';
require_once 'clases/respuestas.class.php';

// instanciamos las clases 
$_auth = new auth;
$_respuestas = new respuestas;

//si el metodo utlizado es post entra
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // coje la información enviada atraves del servidor y lo guarda en una variable
    $postBody = file_get_contents("php://input");

    //le mandamos la información al login para que la verifique y guardamos la repuesta en la variable $datosArray
    $datosArray = $_auth->login($postBody);

    //le enviamos al cliente un header con ese contenido
    header('Content-Type: application/json');
    // si existe error_id entonces ha habido algun problema y le pasa el rpoblema
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
        // todo bien 
        http_response_code(200);
    }
    // le enviamos la información
    echo json_encode($datosArray);
}
else{
    // si el cliente le envia otro metodo
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}