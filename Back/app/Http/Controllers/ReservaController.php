<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cancha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reservas",
     *     summary="Listar reservas",
     *     tags={"Reservas"},
     *     @OA\Parameter(name="user_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="estado", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Lista de reservas")
     * )
     */  
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

    /**
     * @OA\Post(
     *     path="/api/reservas",
     *     summary="Crear una nueva reserva",
     *     tags={"Reservas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cliente","telefono","fecha","hora_inicio","hora_fin","cancha_id","estado"},
     *             @OA\Property(property="cliente", type="string"),
     *             @OA\Property(property="telefono", type="string"),
     *             @OA\Property(property="fecha", type="string", format="date"),
     *             @OA\Property(property="hora_inicio", type="string"),
     *             @OA\Property(property="hora_fin", type="string"),
     *             @OA\Property(property="cancha_id", type="integer"),
     *             @OA\Property(property="estado", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Reserva creada correctamente"),
     *     @OA\Response(response=401, description="Usuario no autenticado"),
     *     @OA\Response(response=409, description="Conflicto de horario")
     * )
     */
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
    // Validación de rango horario
    if ($request->hora_inicio >= $request->hora_fin) {
        return response()->json(['error' => 'La hora de inicio debe ser menor que la hora de fin'], 422);
    }

    // Validación de solapamiento
    $conflicto = Reserva::where('fecha', $request->fecha)
        ->where('cancha_id', $request->cancha_id)
        ->where(function ($q) use ($request) {
            $q->where('hora_inicio', '<', $request->hora_fin)
              ->where('hora_fin', '>', $request->hora_inicio);
        })
        ->exists();

    if ($conflicto) {
        return response()->json(['error' => 'Ya existe una reserva en ese horario'], 409);
    }
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

    /**
     * @OA\Get(
     *     path="/api/horarios",
     *     summary="Obtener horarios disponibles",
     *     tags={"Reservas"},
     *     @OA\Parameter(name="fecha", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="canchaId", in="query", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lista de horarios disponibles"),
     *     @OA\Response(response=400, description="Fecha y cancha son requeridas")
     * )
     */
public function getHorarios(Request $request)
{
    $fecha = $request->query('fecha');
    $canchaId = $request->query('canchaId');

    if (!$fecha || !$canchaId) {
        return response()->json(['error' => 'Fecha y cancha son requeridas'], 400);
    }

    // Lista de horas (00:00 a 23:00)
    $horarios = [];
    for ($h = 0; $h < 24; $h++) {
        $horarios[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    }

    $reservas = Reserva::where('fecha', $fecha)
        ->where('cancha_id', $canchaId)
        ->get();

    foreach ($reservas as $reserva) {
        $horaInicio = intval(substr($reserva->hora_inicio, 0, 2));
        $horaFin = intval(substr($reserva->hora_fin, 0, 2));

        for ($h = $horaInicio; $h < $horaFin; $h++) {
            $horaStr = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            if (($key = array_search($horaStr, $horarios)) !== false) {
                unset($horarios[$key]);
            }
        }
    }

    // Reindexar y devolver
    return response()->json(array_values($horarios));
}
    /**
     * @OA\Get(
     *     path="/api/reservas/metrics",
     *     summary="Obtener métricas de ocupación",
     *     tags={"Estadísticas"},
     *     @OA\Response(response=200, description="Métricas de ocupación")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/reservas/activas",
     *     summary="Obtener reservas activas",
     *     tags={"Reservas"},
     *     @OA\Response(response=200, description="Lista de reservas activas")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Obtener reservas activas por usuario",
     *     tags={"Reservas"},
     *     @OA\Parameter(name="user_id", in="query", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Reservas activas del usuario")
     * )
     */

public function getReservasActivasPorUsuario(Request $request)
{
    $userId = $request->query('user_id');

    $reservas = Reserva::with('cancha')
        ->where('user_id', $userId)
        ->whereIn('estado', ['activa', 'aprobada'])
        ->get();

    return response()->json($reservas);
}


    /**
     * @OA\Get(
     *     path="/api/reservas/pendientes",
     *     summary="Obtener reservas pendientes",
     *     tags={"Reservas"},
     *     @OA\Response(response=200, description="Lista de reservas pendientes")
     * )
     */
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
    /**
     * @OA\Put(
     *     path="/api/reservas/{id}/estado",
     *     summary="Actualizar estado de una reserva",
     *     tags={"Reservas"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"estado"},
     *             @OA\Property(property="estado", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Estado actualizado")
     * )
     */
public function actualizarEstado(Request $request, $id)
{
    $reserva = Reserva::findOrFail($id);
    $reserva->estado = $request->estado;
    $reserva->save();

    return response()->json(['message' => 'Estado actualizado']);
}
    /**
     * @OA\Get(
     *     path="/api/reservas/ingresos",
     *     summary="Obtener ingresos mensuales",
     *     tags={"Estadísticas"},
     *     @OA\Response(response=200, description="Ingresos mensuales calculados")
     * )
     */
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

