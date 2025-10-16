<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Cancha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Para manejo de fechas y horas

class ReservaController extends Controller
{
    /**
     * Listar todas las reservas (Solo ADMIN)
     *
     * @OA\Get(
     * path="/api/reservas",
     * summary="Listar todas las reservas (Administrador)",
     * security={{"bearerAuth":{}}},
     * tags={"Reservas"},
     * @OA\Parameter(name="user_id", in="query", @OA\Schema(type="integer")),
     * @OA\Parameter(name="estado", in="query", @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Lista de reservas"),
     * @OA\Response(response=401, description="Usuario no autenticado"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Admin)")
     * )
     */
    public function index(Request $request)
    {
        if (auth()->user() && !in_array(auth()->user()->role, ['master', 'administrador'])) {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador o master.'], 403);
        }

        $query = Reserva::with('cancha');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        return response()->json($query->get());
    }


    /**
     * Crear una nueva reserva (Usuario Autenticado)
     *
     * @OA\Post(
     * path="/api/reservas",
     * summary="Crear una nueva reserva",
     * tags={"Reservas"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"cliente","telefono","fecha","hora_inicio","hora_fin","cancha_id"},
     * @OA\Property(property="cliente", type="string"),
     * @OA\Property(property="telefono", type="string"),
     * @OA\Property(property="fecha", type="string", format="date"),
     * @OA\Property(property="hora_inicio", type="string", example="10:00"),
     * @OA\Property(property="hora_fin", type="string", example="11:00"),
     * @OA\Property(property="cancha_id", type="integer"),
     * @OA\Property(property="estado", type="string", example="pendiente")
     * )
     * ),
     * @OA\Response(response=201, description="Reserva creada correctamente"),
     * @OA\Response(response=401, description="Usuario no autenticado"),
     * @OA\Response(response=409, description="Conflicto de horario"),
     * @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json([
            'error' => 'No se detectó usuario autenticado',
            'token' => $request->header('Authorization')
        ], 401);
    }

    $validated = $request->validate([
        'cliente' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'fecha' => 'required|date',
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fin' => [
            'required',
            'date_format:H:i',
            function($attr, $value, $fail) use ($request) {
                $inicio = Carbon::parse($request->hora_inicio);
                $fin = Carbon::parse($value);

                if ($fin <= $inicio && !($request->hora_inicio == '23:00' && $value == '00:00')) {
                    $fail('La hora de fin debe ser posterior a la hora de inicio.');
                }
            }
        ],
        'cancha_id' => 'required|integer|exists:canchas,id',
        'estado' => 'sometimes|string|in:pendiente,aprobada,cancelada,activa',
    ]);

    // Ajustar Carbon para hora_fin que cruza medianoche
    $horaInicio = Carbon::parse($request->hora_inicio);
    $horaFin = Carbon::parse($request->hora_fin);
    if ($horaFin <= $horaInicio) {
        $horaFin->addDay();
    }

    // Validación de solapamiento con todas las reservas activas
    $conflicto = Reserva::where('fecha', $request->fecha)
        ->where('cancha_id', $request->cancha_id)
        ->whereIn('estado', ['pendiente', 'aprobada', 'activa'])
        ->get()
        ->filter(function($reserva) use ($horaInicio, $horaFin) {
            $resInicio = Carbon::parse($reserva->hora_inicio);
            $resFin = Carbon::parse($reserva->hora_fin);
            if ($resFin <= $resInicio) $resFin->addDay(); 

            return $horaInicio < $resFin && $horaFin > $resInicio;
        })
        ->isNotEmpty();

    if ($conflicto) {
        return response()->json(['error' => 'Ya existe una reserva en ese horario'], 409);
    }

    // Crear reserva
    $reserva = Reserva::create(array_merge($validated, [
        'user_id' => $user->id,
        'estado' => $request->estado ?? 'pendiente'
    ]));

    return response()->json([
        'message' => 'Reserva creada correctamente.',
        'reserva' => $reserva
    ], 201);
}

    /**
     * @OA\Put(
     * path="/api/reservas/{reserva}",
     * summary="Actualizar una reserva (Solo ADMIN)",
     * tags={"Reservas"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *     name="reserva",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         @OA\Property(property="cliente", type="string"),
     *         @OA\Property(property="telefono", type="string"),
     *         @OA\Property(property="fecha", type="string", format="date"),
     *         @OA\Property(property="hora_inicio", type="string", example="10:00"),
     *         @OA\Property(property="hora_fin", type="string", example="11:00"),
     *         @OA\Property(property="cancha_id", type="integer"),
     *         @OA\Property(property="estado", type="string", example="pendiente")
     *     )
     * ),
     * @OA\Response(response=200, description="Reserva actualizada correctamente"),
     * @OA\Response(response=403, description="Acceso denegado. Solo administradores"),
     * @OA\Response(response=404, description="Reserva no encontrada")
     * )
     */
    public function update(Request $request, Reserva $reserva)
{
    $user = auth()->user();

    if (!$user || !in_array($user->role, ['master', 'administrador'])) {
        return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador o master.'], 403);
    }

    $validated = $request->validate([
        'cliente' => 'sometimes|required|string|max:255',
        'telefono' => 'sometimes|required|string|max:20',
        'fecha' => 'sometimes|required|date',
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fin' => [
            'required',
            'date_format:H:i',
            function($attr, $value, $fail) use ($request) {
                $inicio = Carbon::parse($request->hora_inicio);
                $fin = Carbon::parse($value);

                if ($fin <= $inicio && !($request->hora_inicio == '23:00' && $value == '00:00')) {
                    $fail('La hora de fin debe ser posterior a la hora de inicio.');
                }
            }
        ],
        'cancha_id' => 'sometimes|required|integer|exists:canchas,id',
        'estado' => 'sometimes|required|string|max:50|in:pendiente,aprobada,cancelada,activa',
    ]);

    $horaInicio = Carbon::parse($request->hora_inicio);
    $horaFin = Carbon::parse($request->hora_fin);
    if ($horaFin <= $horaInicio) {
        $horaFin->addDay();
    }

    // Validación de solapamiento con otras reservas
    $conflicto = Reserva::where('fecha', $request->fecha ?? $reserva->fecha)
        ->where('cancha_id', $request->cancha_id ?? $reserva->cancha_id)
        ->whereIn('estado', ['pendiente', 'aprobada', 'activa'])
        ->where('id', '!=', $reserva->id) // Excluir la reserva actual
        ->get()
        ->filter(function($r) use ($horaInicio, $horaFin) {
            $resInicio = Carbon::parse($r->hora_inicio);
            $resFin = Carbon::parse($r->hora_fin);
            if ($resFin <= $resInicio) $resFin->addDay(); 

            return $horaInicio < $resFin && $horaFin > $resInicio;
        })
        ->isNotEmpty();

    if ($conflicto) {
        return response()->json(['error' => 'Ya existe una reserva en ese horario'], 409);
    }

    // Actualizar reserva
    $reserva->update($validated);

    return response()->json([
        'message' => 'Reserva actualizada correctamente.',
        'reserva' => $reserva
    ], 200);
}

    /**
     * @OA\Delete(
     * path="/api/reservas/{reserva}",
     * summary="Eliminar una reserva (Solo ADMIN)",
     * tags={"Reservas"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *     name="reserva",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Reserva eliminada correctamente"),
     * @OA\Response(response=403, description="Acceso denegado. Solo administradores"),
     * @OA\Response(response=404, description="Reserva no encontrada")
     * )
     */
    public function destroy(Reserva $reserva)
    {
        $user = auth()->user();

   if (auth()->user() && !in_array(auth()->user()->role, ['master', 'administrador'])) {
        return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador o master.'], 403);
    }
        $reserva->delete();

        return response()->json(['message' => 'Reserva eliminada correctamente.'], 200);
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
        // reservaController.php

public function getHorarios(Request $request)
{
    $fecha = $request->query('fecha');
    $canchaId = $request->query('canchaId');

    if (!$fecha || !$canchaId) {
        return response()->json(['error' => 'Fecha y cancha son requeridas'], 400);
    }

    // Horarios posibles (16:00 a 23:00) + 00:00 como límite
    $todosHorarios = [];
    for ($h = 16; $h <= 23; $h++) {
        $todosHorarios[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    }
    $todosHorarios[] = '00:00';

    // Obtener reservas activas para la cancha y fecha
    $reservas = Reserva::where('fecha', $fecha)
        ->where('cancha_id', $canchaId)
        ->whereIn('estado', ['pendiente', 'aprobada', 'activa'])
        ->get(['hora_inicio', 'hora_fin']);

    // array de horas ocupadas
    $horasOcupadas = [];
    foreach ($reservas as $reserva) {
        $inicio = Carbon::parse($reserva->hora_inicio);
        $fin = Carbon::parse($reserva->hora_fin);
        if ($fin <= $inicio) $fin->addDay(); 

        $hora = clone $inicio;
        while ($hora < $fin) {
            $horasOcupadas[] = $hora->format('H:00');
            $hora->addHour();
        }
    }

    // Filtrar horarios libres
    $horariosLibres = array_values(array_filter($todosHorarios, function($h) use ($horasOcupadas) {
        return !in_array($h, $horasOcupadas);
    }));

    return response()->json($horariosLibres);
}
    /**
     * Obtener métricas de ocupación (Solo ADMIN)
     *
     * @OA\Get(
     * path="/api/reservas/metrics",
     * summary="Obtener métricas de ocupación (Administrador)",
     * tags={"Estadísticas"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Métricas de ocupación"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Admin)")
     * )
     */
    public function getMetrics()
    {
        
        if (auth()->user() && !in_array(auth()->user()->role, ['master', 'administrador'])) {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador o master.'], 403);
        }
        $totalCanchas = Cancha::count();
        $reservasActivas = Reserva::whereIn('estado', ['aprobada', 'activa'])->count(); 
        $ocupacion = $totalCanchas > 0 ? round(($reservasActivas / $totalCanchas) * 100, 2) : 0;

        return response()->json([
            'total_canchas' => $totalCanchas,
            'reservas_activas' => $reservasActivas,
            'ocupacion' => $ocupacion 
        ]);
    }

    /**
     * Obtener reservas activas (Solo ADMIN)
     *
     * @OA\Get(
     * path="/api/reservas/activas",
     * summary="Obtener todas las reservas activas (Administrador)",
     * tags={"Reservas"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Lista de reservas activas"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Admin)")
     * )
     */
    public function getReservasActivas()
    {
        if (auth()->user() && !in_array(auth()->user()->role, ['master', 'administrador'])) {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador o master.'], 403);
        }

        $reservas = Reserva::with('cancha')
            ->whereIn('estado', ['activa', 'aprobada'])
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'cliente' => $reserva->cliente,
                    'telefono' => $reserva->telefono, // 
                    'cancha' => $reserva->cancha->nombre,
                    'fecha' => $reserva->fecha,
                    'hora_inicio' => $reserva->hora_inicio,
                    'hora_fin' => $reserva->hora_fin,
                    'estado' => $reserva->estado
                ];
            });

        return response()->json($reservas);
    }

    /**
     * Obtener reservas activas por usuario (Usuario Autenticado)
     *
     * @OA\Get(
     * path="/api/reservations",
     * summary="Obtener reservas activas del usuario autenticado",
     * tags={"Reservas"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Reservas activas del usuario"),
     * @OA\Response(response=401, description="Usuario no autenticado")
     * )
     */
    public function getReservasActivasPorUsuario(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }

        $reservas = Reserva::with('cancha')
            ->where('user_id', $userId)
            ->whereIn('estado', ['activa', 'aprobada'])
            ->get();

        return response()->json($reservas);
    }


    /**
     * Obtener reservas pendientes (Solo ADMIN)
     *
     * @OA\Get(
     * path="/api/reservas/pendientes",
     * summary="Obtener reservas pendientes (Administrador)",
     * tags={"Reservas"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Lista de reservas pendientes"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Admin)")
     * )
     */
    public function getReservasPendientes()
    {
      if (auth()->user() && !in_array(auth()->user()->role, ['master', 'administrador'])) {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador o master.'], 403);
        }
        $reservas = Reserva::with('cancha')
            ->where('estado', 'pendiente')
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'cliente' => $reserva->cliente,
                    'cancha' => $reserva->cancha->nombre,
                    'fecha' => $reserva->fecha,
                    'hora_inicio' => $reserva->hora_inicio,
                    'hora_fin' => $reserva->hora_fin,
                    'estado' => $reserva->estado
                ];
            });

        return response()->json($reservas);
    }

    /**
     * @OA\Put(
     * path="/api/reservas/{id}/estado",
     * summary="Actualizar estado de una reserva (Solo ADMIN)",
     * tags={"Reservas"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"estado"},
     * @OA\Property(property="estado", type="string", example="aprobada")
     * )
     * ),
     * @OA\Response(response=200, description="Estado actualizado"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Admin)"),
     * @OA\Response(response=404, description="Reserva no encontrada")
     * )
     */
    public function actualizarEstado(Request $request, $id)
    {
           if (auth()->user() && !in_array(auth()->user()->role, ['master', 'administrador'])) {
                return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador o master.'], 403);
            }

        $request->validate([
            'estado' => 'required|string|in:pendiente,aprobada,cancelada,activa',
        ]);

        $reserva = Reserva::findOrFail($id);
        $reserva->estado = $request->estado;
        $reserva->save();

        return response()->json(['message' => 'Estado actualizado', 'reserva' => $reserva]);
    }

    /**
     * @OA\Get(
     * path="/api/reservas/ingresos",
     * summary="Obtener ingresos mensuales (Solo ADMIN)",
     * tags={"Estadísticas"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Ingresos mensuales calculados"),
     * @OA\Response(response=403, description="Acceso denegado (Requiere Admin)")
     * )
     */
    public function getIngresosMensuales()
    {
        if (auth()->user() && !in_array(auth()->user()->role, ['master', 'administrador'])) {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador o master.'], 403);
        }

        $reservas = Reserva::with('cancha')
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->whereIn('estado', ['activa', 'aprobada'])
            ->get();

        $ingresos = 0;

        foreach ($reservas as $reserva) {
            try {
                $inicio = Carbon::parse($reserva->hora_inicio);
                $fin = Carbon::parse($reserva->hora_fin);
            } catch (\Exception $e) {
                continue;
            }

            $duracionHoras = $inicio->diffInMinutes($fin) / 60;

            $precioHora = $reserva->cancha->precio_hora ?? 0;
            $ingresos += $duracionHoras * $precioHora;
                        
        }

        return response()->json(['ingresos' => round($ingresos, 2)]);
    }
}
