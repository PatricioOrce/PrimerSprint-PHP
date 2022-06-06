<?php

class Mesa
{
    public $id;
    public $codigo;
    public $numero;
    public $estado;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO mesas (numero, estado, codigo) VALUES (:numero, :estado, :codigo)');

        $consulta->bindValue(':numero', $this->numero);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', 'asd123', PDO::PARAM_STR);

    }

}







?>