<?php
require_once './models/Usuario.php';
// require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario //implements IApiUsable
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
        $usr->clave = $clave;
        $usr->nombre = $nombre;
        $usr->apellido = $apellido;
        $usr->edad = $edad;
        $usr->rol = $rol;
        $usr->estado = $estado;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    // public function TraerUno($request, $response, $args)
    // {
    //     // Buscamos usuario por nombre
    //     $usr = $args['usuario'];
    //     $usuario = Usuario::obtenerUsuario($usr);
    //     $payload = json_encode($usuario);

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }

    // public function TraerTodos($request, $response, $args)
    // {
    //     $lista = Usuario::obtenerTodos();
    //     $payload = json_encode(array("listaUsuario" => $lista));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }
    
    // public function ModificarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();

    //     $nombre = $parametros['nombre'];
    //     Usuario::modificarUsuario($nombre);

    //     $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }

    // public function BorrarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();

    //     $usuarioId = $parametros['usuarioId'];
    //     Usuario::borrarUsuario($usuarioId);

    //     $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }
}
