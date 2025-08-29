<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente',
        'telefono',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'cancha_id',
    ];

    // Relación con Cancha
    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }
}
