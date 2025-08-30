@extends('layouts.app')

@section('content')
<h2>Agregar Cancha</h2>

<form action="{{ route('canchas.store') }}" method="POST">
    @csrf
    <label>Nombre:</label>
    <input type="text" name="nombre" required><br>

    <label>Tipo:</label>
    <input type="text" name="tipo"><br>

    <label>Precio por hora:</label>
    <input type="number" step="0.01" name="precio_hora"><br>

    <button type="submit">Guardar</button>
    <a href="{{ route('canchas.index') }}">Cancelar</a>
</form>
@endsection
