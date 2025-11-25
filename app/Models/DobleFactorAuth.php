<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DobleFactorAuth extends Model
{
    use HasFactory;

    protected $table = 'doblefactorauth';
    protected $primaryKey = 'idAuth';
    public $timestamps = true;

    protected $fillable = [
        'codigo',
        'fechaEnvio',
        'estado',
        'idUsuario',
        'activo',
    ];

    protected $casts = [
        'fechaEnvio' => 'datetime',
        'estado' => 'boolean',
        'activo' => 'boolean',
    ];
}
