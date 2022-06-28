<?php

use Illuminate\Support\Facades\Auth;

require_once './models/Mesa.php';
// require_once './interfaces/IApiUsable.php';

class MesaController //implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        if(AutentificadorJWT::GetUserRole($request) == 'socio')
        {
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
            $codigoAlfanumerico = substr(str_shuffle($permitted_chars), 0, 10);
            $parametros = $request->getParsedBody();
    
            $numero = $parametros['numero'];
            $estado = $parametros['estado'];
            
            // Creamos el usuario
            $mesa = new Mesa();
            $mesa->numero = $numero;
            $mesa->estado = $estado;
            $mesa->codigo = $codigoAlfanumerico;
    
            $mesa->save();
    
            $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
        }
        else{
            $payload = json_encode(array("mensaje" => "Solamente los socios pueden dar de alta una mesa."));
        }



        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $mesas = Mesa::all();
        $mesa = $mesas->find($args['id']);

        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $mesas = Mesa::all();
        $payload = json_encode(array("listaMesas" => $mesas));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function Modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function Borrar($request, $response, $args)
    {
        $res = false;
        try {
            $mesas = Mesa::all();
            $mesa = $mesas->find($args['id']);
            $res = $mesa->delete();
        } catch (\Throwable $th) {
            $payload = json_encode(array("mensaje" => "Error al borrar mesa: ". $th->getMessage()));
        }

        if($res){
            $payload = json_encode(array("mensaje" => "Mesa borrado con exito"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}

?>