<?php

use Firebase\JWT\JWT;

class AutentificadorJWT
{
    private static $claveSecreta = 'ClaveSecretaPatoMasterCrack';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datosRecibidos)
    {
        $ahora = time();

        //Duracion de token de 60 minutos
        $payload = array(
            'iat' => $ahora, //Cuando fue creado
            'exp' => $ahora + (60000), //Cuando expira
            'aud' => self::Aud(), //Identifica a los receptores del JWT (audiencia)
            'data' => $datosRecibidos, //La data que movemos
            'app' => "Token de login"
        );

        //Aca se hace el encode del JWT con nuestra firma/clave-secreta definida
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("Token Invalido");
        }
    }


    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    static function GetUserRole($request)
    {
        $header = $request->getHeaderLine('Authorization');

        if ($header != null)
        {   
            $token = trim(explode("Bearer", $header)[1]);

            $tokenInfo = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
            $userInfo = json_decode($tokenInfo);
            
            $username = $userInfo->datos->user;

            $usuario = new Usuario();
            $users = $usuario->where('username', '=',$username)->get();

            if ($users != null)
            {
                $tipoUsuarioResponsable = $users[0]->rol;
            }
            return $tipoUsuarioResponsable;
        }
  
        return null;
    }

    static function GetUserState($request)
    {

    }

    static function VerificarUsuario($token)
    {
        $userExists = false;

        $dataToken = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
        $dataUser = json_decode($dataToken);

        //De la data decodificada (User y clave) me voy a guardar el user y buscarlo en la db.
        $username = $dataUser->datos->user;
        $password = $dataUser->datos->clave;
        if ($username != null && $password != null) {
            $user = new Usuario();
            $usuariosEncontrados = $user->where('username', '=', $username)->get();
            if ($usuariosEncontrados != null) {
                $contador = 0;

                foreach ($usuariosEncontrados as $usuario) {
                    $passwordVerified = password_verify($password, $usuario->clave);
                    if ($passwordVerified) {
                        $userExists = true;
                        return $userExists;
                    }
                    $contador++;
                }
            }
        }
        throw new Exception('Usuario Invalido.');
    }
}
