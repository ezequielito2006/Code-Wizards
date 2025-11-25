<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Qr extends Model
{
    protected $table = 'qr';
    protected $primaryKey = 'idQR';
    public $timestamps = true;

    protected $fillable = [
        'codigoQR',
        'fechaGeneracion',
        'enlace',
        'activo',
    ];

    protected $casts = [
        'fechaGeneracion' => 'date',
        'activo' => 'boolean',
    ];

    // Relación con Pedido
    public function pedidos() {
        return $this->hasOne(Pedido::class, 'idQR','idQR');
    }
}
