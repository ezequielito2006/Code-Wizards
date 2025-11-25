<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DetallePedido extends Model
{
    

    protected $table = 'detallepedido';
    protected $primaryKey = 'idDetalle';
    public $timestamps = true;

    protected $fillable = [
        'idPedido',
        'idProducto',
        'cantidad',
        'subTotal',
        'activo',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'subTotal' => 'float',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function pedido() {
        return $this->belongsTo(Pedido::class, 'idPedido','idPedido');
    }

    public function producto() {
        return $this->belongsTo(Producto::class, 'idProducto','idProducto');
    }
}
