<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'productos';
    public $incrementing = true;
    public $timestamps = false;

    const UPDATED_AT = 'fechaModificacion';
    const DELETED_AT = 'fechaBaja';
    const CREATED_AT = 'fechaAlta';

    protected $fillable = [
        'precio', 'tipo', 'nombre',
        'tiempoProducto', 'stock', 
        'fechaModificacion', 'fechaBaja',
        'fechaAlta'
    ];

    
}


?>