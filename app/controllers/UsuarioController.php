<?php
require_once './models/Usuario.php';


class UsuarioController //implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $username = $parametros['username'];
        $clave = $parametros['clave'];
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $edad = $parametros['edad'];
        $rol = $parametros['rol'];
        $estado = $parametros['estado'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->username = $username;
        $usr->clave = password_hash($clave, PASSWORD_DEFAULT);
        $usr->nombre = $nombre;
        $usr->apellido = $apellido;
        $usr->edad = $edad;
        $usr->rol = $rol;
        $usr->estado = $estado;
        $usr->save();

        $payload = json_encode(array("mensaje" => "Usuario creado con exitro"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $users = Usuario::all();
        $user = $users->find($args['id']);

        $payload = json_encode($user);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $users = Usuario::all();
        $payload = json_encode(array("listaUsuario" => $users));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function Modificar($request, $response, $args)
    {
        $id = $args['id'];
        $parametros = $request->getParsedBody();
        // $body = json_decode(file_get_contents("php://input"), true);
        $user = Usuario::where('id', '=', $id)->first();

        var_dump($parametros);

        if($user != null)
        {
            $user->username = $parametros['username'];
            $user->clave = $parametros['clave'] == (''||null) ? password_hash($user->clave, PASSWORD_DEFAULT) : password_hash($parametros['clave'], PASSWORD_DEFAULT);
            $user->nombre = $parametros['nombre'] == (''||null) ? $user->nombre : $parametros['nombre'];
            $user->apellido = $parametros['apellido'] == (''||null) ? $user->apellido : $parametros['apellido'];
            $user->edad = $parametros['edad'] == (''||null) ? $user->edad : $parametros['edad'];
            $user->rol = $parametros['rol'] == (''||null) ? $user->rol : $parametros['rol'];
            $user->estado = $parametros['estado'] == (''||null) ? $user->estado : $parametros['estado'];

            $user->save();
        }

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function Borrar($request, $response, $args)
    {
        $res = false;
        try {
            $users = Usuario::all();
            $user = $users->find($args['id']);
            $res = $user->delete();
        } catch (\Throwable $th) {
            $payload = json_encode(array("mensaje" => "Error al borrar usuario: ". $th->getMessage()));
        }

        if($res){
            $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
