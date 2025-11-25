<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'idCliente';
    public $timestamps = true;

    protected $fillable = [
        'direccion',
        'telefono',
        'idAdmin',
        'idAuth',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function administrador() {
        return $this->belongsTo(Administrador::class, 'idAdmin');
    }

    public function dobleFactorAuth() {
        return $this->belongsTo(DobleFactorAuth::class, 'idAuth');
    }

    public function pedidos() {
        return $this->hasMany(Pedido::class, 'idCliente');
    }
}
