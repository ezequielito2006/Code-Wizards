<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';
    protected $primaryKey = 'idProducto';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'categoria',
        'activo',
        'imagen',
    ];

    protected $casts = [
        'precio' => 'float',
        'stock' => 'integer',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function detallePedidos() {
        return $this->hasMany(DetallePedido::class, 'idProducto');
    }

    public function inventarios() {
        return $this->hasMany(Inventario::class, 'idProducto');
    }

    public function movimientosInventario() {
        return $this->hasMany(MovimientoInventario::class, 'idProducto');
    }
}
