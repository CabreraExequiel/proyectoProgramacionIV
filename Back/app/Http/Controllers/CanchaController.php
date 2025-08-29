<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use Illuminate\Http\Request;

class CanchaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
            return response()->json(Cancha::all());  //Lista de las cnachas que hay
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) //Crear una nueva cancha en caso de ser posible
    {
        $request -> validate([
            'nombre' => 'required|string|max:100'
        ]);

        $cancha = Cancha::create([
            'nombre'=>$request->nombre
        ]);

        return response()->json($cancha);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
