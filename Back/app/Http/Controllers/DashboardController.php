<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use App\Models\Reserva;

class DashboardController extends Controller
{
    public function metrics()
    {
        $totalCanchas = Cancha::count();
        $canchasOcupadas = Reserva::whereDate('fecha', now()->toDateString())
                                  ->where('estado', 'activa') 
                                  ->distinct('cancha_id')
                                  ->count('cancha_id');

        $ocupacion = $totalCanchas > 0 ? round(($canchasOcupadas / $totalCanchas) * 100) : 0;

        $reservasActivas = Reserva::where('estado', 'activa')->count();

        return response()->json([
            'ocupacion' => $ocupacion,
            'reservas_activas' => $reservasActivas
        ]);
    }
}
