<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administrador';
    protected $primaryKey = 'idAdmin';
    public $timestamps = true;

    protected $fillable = [
        'area',
        'idAuth',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function dobleFactorAuth() {
        return $this->belongsTo(DobleFactorAuth::class, 'idAuth');
    }

    public function clientes() {
        return $this->hasMany(Cliente::class, 'idAdmin');
    }
}
