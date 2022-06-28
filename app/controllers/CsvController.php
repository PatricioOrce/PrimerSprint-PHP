<?php
require_once "./models/CSV.php";
class CsvController{

    public static function SaveProductsToDB($request, $response, $args)
    {
        $userRole = AutentificadorJWT::GetUserRole($request);

        // if ($userRole == "socio")
        // {
            $nombreCSVRecibido = "files/".$_FILES["productosCSV"]["name"];
            
            $nombreCSVReciciboSinExt = explode(".",$nombreCSVRecibido);
            $destino = $nombreCSVReciciboSinExt[0] .".csv";
            echo($destino);
            // move_uploaded_file($_FILES["productosCSV"]["tmp_name"],$destino);
            
            $lecturaSalioBien = CSV::LeerProductosCSV($destino);

            $payload = json_encode(array("mensajeFinal" => $lecturaSalioBien));
        // }
        
        //Retorno la respuesta con el body que contiene un mensaje.
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}










?>