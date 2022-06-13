<?php
interface IApiUsable
{
	public function TraerUno($request, $response, $args);
	public function TraerTodos($request, $response, $args);
	public function CargarUno($request, $response, $args);
	public function Borrar($request, $response, $args);
	public function Modificar($request, $response, $args);
}
