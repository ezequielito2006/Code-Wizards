<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventario';
    protected $primaryKey = 'idInventario';
    public $timestamps = true;

    protected $fillable = [
        'fechaActualizacion',
        'stockActual',
        'idProducto',
        'activo',
    ];

    protected $casts = [
        'fechaActualizacion' => 'date',
        'stockActual' => 'integer',
        'activo' => 'boolean',
    ];

    // Relación con Producto
    public function producto() {
        return $this->belongsTo(Producto::class, 'idProducto','idProducto');
    }
}
