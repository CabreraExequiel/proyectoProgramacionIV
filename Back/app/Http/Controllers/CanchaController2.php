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
    public function index()
    {
        // ✔ Cambié return view(...) por return response()->json(...)
        return response()->json(Cancha::all());
    }

    /**
     * Devuelve una cancha por id
     */
    public function show($id)
    {
        // ✔ Uso findOrFail para que si no existe devuelva 404 automáticamente
        $cancha = Cancha::findOrFail($id);
        return response()->json($cancha);
    }

    /**
     * Crea una cancha nueva
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

        return response()->json($cancha);
    }

    /**
     * Elimina una cancha
     */
    public function destroy($id)
    {
        $cancha = Cancha::findOrFail($id);
        $cancha->delete();

        return response()->json(['message' => 'Cancha eliminada correctamente']);
    }
}
