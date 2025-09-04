<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cancha;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function index()
    {
        $reservas = Reserva::with('cancha')->get();
        return view('reservas.index', compact('reservas'));
    }

    public function create()
    {
        $canchas = Cancha::all();
        return view('reservas.create', compact('canchas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente' => 'required|string|max:255',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'cancha_id' => 'required|integer',
            'estado' => 'required|string|max:50',
        ]);

       $reserva = Reserva::create($validated);

       return response()->json([
           'message' => 'Reserva creada correctamente',
           'reserva' => $reserva
        ], 201);

    }

    public function edit(Reserva $reserva)
    {
        $canchas = Cancha::all();
        return view('reservas.edit', compact('reserva', 'canchas'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'cliente' => 'required|string|max:255',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'cancha_id' => 'required|integer',
            'estado' => 'required|string|max:50',
        ]);

        $reserva->update($request->all());
        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        $reserva->delete();
        return redirect()->route('reservas.index')->with('success', 'Reserva eliminada.');
    }

    public function getCanchas()
    {
        return response()->json(Cancha::all());
    }

public function getHorarios(Request $request)
{
    $fecha = $request->query('fecha');

    $horarios = [];
    for ($h = 0; $h < 24; $h++) {
        $horarios[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    }

    $reservados = Reserva::where('fecha', $fecha)
        ->pluck('hora_inicio')
        ->toArray();

    $disponibles = array_values(array_diff($horarios, $reservados));

    return response()->json($disponibles);
}

    public function storeApi(Request $request)
    {
        $request->validate([
            'cliente' => 'required|string|max:255',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'cancha_id' => 'required|integer',
            'estado' => 'required|string|max:50',
        ]);

        $reserva = Reserva::create($request->all());

        return response()->json([
            'message' => 'Reserva creada correctamente.',
            'reserva' => $reserva
        ]);
    }

    public function getMetrics()
    {
        $totalCanchas = \App\Models\Cancha::count();
        $reservasActivas = \App\Models\Reserva::where('estado', 'activa')->count();

        $ocupacion = $totalCanchas > 0 ? round(($reservasActivas / $totalCanchas) * 100, 2) : 0;

        return response()->json([
            'ocupacion' => $ocupacion,
            'reservas_activas' => $reservasActivas
        ]);
    }

    public function getReservasActivas()
    {
        $reservas = \App\Models\Reserva::with('cancha')
            ->where('estado', 'activa')
            ->get()
            ->map(function ($reserva) {
                return [
                    'cliente' => $reserva->cliente,
                    'cancha' => $reserva->cancha->nombre,
                    'hora_inicio' => $reserva->hora_inicio,
                    'hora_fin' => $reserva->hora_fin,
                    'estado' => $reserva->estado
                ];
            });

        return response()->json($reservas);
    }
}
