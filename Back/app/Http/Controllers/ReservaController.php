<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cancha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{

    public function index(Request $request)
{
    $query = Reserva::with('cancha');

    if ($request->has('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->has('estado')) {
        $query->where('estado', $request->estado);
    }

    return response()->json($query->get());
}


    public function create()
    {
        $canchas = Cancha::all();
        return view('reservas.create', compact('canchas'));
    }
public function store(Request $request)
{   
    
 \Log::info('Bearer token recibido:', ['token' => $request->bearerToken()]);
    $user = auth()->user();
    \Log::info('Usuario autenticado:', ['user' => $user]);

    if (!$user) {
        \Log::error('Usuario no autenticado', [
            'headers' => $request->headers->all(),
            'token' => $request->bearerToken()
        ]);
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }
    $validated = $request->validate([
        'cliente' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'fecha' => 'required|date',
        'hora_inicio' => 'required',
        'hora_fin' => 'required',
        'cancha_id' => 'required|integer',
        'estado' => 'required|string|max:50',
    ]);

    $reserva = new Reserva();
    $reserva->cliente = $request->cliente;
    $reserva->telefono = $request->telefono;
    $reserva->fecha = $request->fecha;
    $reserva->hora_inicio = $request->hora_inicio;
    $reserva->hora_fin = $request->hora_fin;
    $reserva->cancha_id = $request->cancha_id;
    $reserva->estado = $request->estado;
    $reserva->user_id = auth()->id(); 
    $reserva->save();

    return response()->json([
        'message' => 'Reserva creada correctamente.',
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

    public function getMetrics()
    {
        $totalCanchas = \App\Models\Cancha::count();
        $reservasActivas = Reserva::whereIn('estado', ['activa', 'aprobada'])->count();

        $ocupacion = $totalCanchas > 0 ? round(($reservasActivas / $totalCanchas) * 100, 2) : 0;

        return response()->json([
            'ocupacion' => $ocupacion,
            'reservas_activas' => $reservasActivas
        ]);
    }

   public function getReservasActivas()
{
    $reservas = Reserva::with('cancha')
        ->whereIn('estado', ['activa', 'aprobada']) 
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

public function getReservasActivasPorUsuario(Request $request)
{
    $userId = $request->query('user_id');

    $reservas = Reserva::with('cancha')
        ->where('user_id', $userId)
        ->whereIn('estado', ['activa', 'aprobada'])
        ->get();

    return response()->json($reservas);
}


public function getReservasPendientes()
{
    $reservas = Reserva::with('cancha')
        ->where('estado', 'pendiente')
        ->get()
        ->map(function ($reserva) {
            return [
                'id' => $reserva->id,
                'cliente' => $reserva->cliente,
                'cancha' => $reserva->cancha->nombre,
                'hora_inicio' => $reserva->hora_inicio,
                'hora_fin' => $reserva->hora_fin,
                'estado' => $reserva->estado
            ];
        });

    return response()->json($reservas);
}
public function actualizarEstado(Request $request, $id)
{
    $reserva = Reserva::findOrFail($id);
    $reserva->estado = $request->estado;
    $reserva->save();

    return response()->json(['message' => 'Estado actualizado']);
}
public function getIngresosMensuales()
{
    $reservas = \App\Models\Reserva::with('cancha')
        ->whereMonth('fecha', now()->month)
        ->whereYear('fecha', now()->year)
        ->whereIn('estado', ['activa', 'aprobada'])
        ->get();

    $ingresos = 0;

    foreach ($reservas as $reserva) {
        $inicio = \Carbon\Carbon::parse($reserva->hora_inicio);
        $fin = \Carbon\Carbon::parse($reserva->hora_fin);
        $duracionHoras = $inicio->diffInMinutes($fin) / 60;

        $precioHora = $reserva->cancha->precio_hora ?? 0;
        $ingresos += $duracionHoras * $precioHora;
    }

    return response()->json(['ingresos' => round($ingresos, 2)]);
}

}

