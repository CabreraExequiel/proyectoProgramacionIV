<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     // GET /api/categorias
     // Devuelve una lista de todas las categorías
     return Categoria::all();
    //o
    $datos = Categoria::all();
     return response()->json([
     'success' => true,
     'data' => $datos
     ]);
    //o
    return Categoria::select('id', 'nombre')->get();
    //o
    $datos = Categoria::orderBy('nombre', 'desc')->select('id', 'nombre')->get();
     return response()->json($datos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar que venga el nombre
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Crear la categoría
        $categoria = Categoria::create([
            'nombre' => $request->nombre,
        ]);

        // Devolver la categoría creada en JSON
        return response()->json([
            'success' => true,
            'data' => $categoria
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        //
    }
}
