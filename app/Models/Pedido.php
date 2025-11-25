<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedido';
    protected $primaryKey = 'idPedido';
    public $timestamps = true;

    protected $fillable = [
        'fecha',
        'estado',
        'total',
        'idCliente',
        'idQR',
        'activo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'float',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function cliente() {
        return $this->belongsTo(Usuario::class, 'idCliente','idUsuario');
    }

    public function qr() {
        return $this->belongsTo(Qr::class, 'idQR','idQR');
    }

    public function detalles() {
        return $this->hasMany(\App\Models\DetallePedido::class, 'idPedido','idPedido');
    }
}
