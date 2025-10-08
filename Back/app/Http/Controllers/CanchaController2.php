<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cancha;
use Illuminate\Http\Request;

class CanchaController2 extends Controller
{
    // ⚡ Nuevo controlador API para Canchas
    // 👉 Lo hice separado del que ya estaba, así no rompemos las vistas Blade que armó el equipo.

    /**
     * Devuelve todas las canchas en formato JSON
     */

    /**
     * @OA\Get(
     *     path="/api/canchas",
     *     summary="Obtener todas las canchas",
     *     tags={"Canchas"},
     *     @OA\Response(response=200, description="Lista de canchas")
     * )
     */
    public function index()
    {
        // ✔ Cambié return view(...) por return response()->json(...)
        return response()->json(Cancha::all());
    }

    /**
     * Devuelve una cancha por id
     */

    /**
     * @OA\Get(
     *     path="/api/canchas/{id}",
     *     summary="Obtener una cancha por ID",
     *     tags={"Canchas"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Datos de la cancha"),
     *     @OA\Response(response=404, description="Cancha no encontrada")
     * )
     */
    public function show($id)
    {
        $cancha = Cancha::find($id);

        if (!$cancha) {
            return response()->json(['message' => 'Cancha no encontrada'], 404);
        }

        return response()->json($cancha);
    }


    /**
     * Crea una cancha nueva
     */

    /**
     * @OA\Post(
     *     path="/api/canchas",
     *     summary="Crear una nueva cancha",
     *     tags={"Canchas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "tipo"},
     *             @OA\Property(property="nombre", type="string", example="Cancha 1"),
     *             @OA\Property(property="tipo", type="string", example="Fútbol 5"),
     *             @OA\Property(property="precio_hora", type="number", example=1200),
     *             @OA\Property(property="cant_jugadores", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Cancha creada correctamente"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        // ✔ Valido datos básicos (se puede extender después)
        $validated = $request->validate([
            'nombre' => 'required|string',
            'tipo'   => 'required|string',
            'precio_hora' => 'numeric',
            'cant_jugadores'=> 'integer',
        ]);

        $cancha = Cancha::create($validated);

        return response()->json($cancha, 201);
    }

    /**
     * Actualiza una cancha existente
     */

    /**
     * @OA\Put(
     *     path="/api/canchas/{id}",
     *     summary="Actualizar una cancha existente",
     *     tags={"Canchas"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Cancha 1"),
     *             @OA\Property(property="tipo", type="string", example="Fútbol 7"),
     *             @OA\Property(property="precio_hora", type="number", example=1500),
     *             @OA\Property(property="cant_jugadores", type="integer", example=14)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cancha actualizada correctamente"),
     *     @OA\Response(response=404, description="Cancha no encontrada"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function update(Request $request, $id)
    {
        $cancha = Cancha::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'string',
            'tipo'   => 'string',
            'precio_hora' => 'numeric',
            'cant_jugadores'=> 'integer',

        ]);

        $cancha->update($validated);

         return response()->json([
        'message' => 'Cancha actualizada correctamente',
        'cancha' => $cancha
    ]);
    }

    /**
     * Elimina una cancha
     */

    /**
     * @OA\Delete(
     *     path="/api/canchas/{id}",
     *     summary="Eliminar una cancha",
     *     tags={"Canchas"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Cancha eliminada correctamente"),
     *     @OA\Response(response=404, description="Cancha no encontrada")
     * )
     */
    public function destroy($id)
    {
        $cancha = Cancha::findOrFail($id);
        $cancha->delete();

        return response()->json(['message' => 'Cancha eliminada correctamente']);
    }
}
