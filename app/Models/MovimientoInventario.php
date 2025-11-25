<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientoinventario';
    protected $primaryKey = 'idMovimiento';
    public $timestamps = true;

    protected $fillable = [
        'idProducto',
        'tipo',
        'cantidad',
        'descripcion',
        'fecha',
        'idUsuario',
        'activo',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function producto() {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }
}
