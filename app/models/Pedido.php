<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Pedido extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'id';
    protected $table = 'pedidos';
    public $incrementing = true;

    const UPDATED_AT = 'fechaModificacion';
    const DELETED_AT = 'fechaBaja';
    const CREATED_AT = 'fechaAlta';
    
    protected $fillable = [
        'codigo', 'estado', 'tiempoEstimado',
        'nombreCliente', 'importe', 'codigoMesa',
        'idMesa', 'idPedido', 'idUsuario',
        'pathFoto', 'fechaModificacion', 'fechaBaja',
        'fechaAlta'
    ];

    public function usuarios()
    {
        return $this->hasOne(Usuario::class, 'idUsuario');
    }

    public function mesas()
    {
        return $this->hasOne(Mesa::class, 'idMesa');
    }

    // public function ventas()
    // {
    //     return $this->hasMany(Venta::class);
    // }


}


?>