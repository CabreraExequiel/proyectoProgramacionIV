<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use Illuminate\Http\Request;

class CanchaController extends Controller
{
    public function index()
    {
        $canchas = Cancha::all();
        return view('canchas.index', compact('canchas'));
    }

    public function create()
    {
        return view('canchas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:255',
            'precio_hora' => 'nullable|numeric',
        ]);

        Cancha::create($request->all());
        return redirect()->route('canchas.index')->with('success', 'Cancha creada correctamente.');
    }

    public function edit(Cancha $cancha)
    {
        return view('canchas.edit', compact('cancha'));
    }

    public function update(Request $request, Cancha $cancha)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:255',
            'precio_hora' => 'nullable|numeric',
        ]);

        $cancha->update($request->all());
        return redirect()->route('canchas.index')->with('success', 'Cancha actualizada correctamente.');
    }

    public function destroy(Cancha $cancha)
    {
        $cancha->delete();
        return redirect()->route('canchas.index')->with('success', 'Cancha eliminada.');
    }
}

