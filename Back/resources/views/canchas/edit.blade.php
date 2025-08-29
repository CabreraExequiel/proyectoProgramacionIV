@extends('layouts.app')

@section('content')
<h2>Editar Cancha</h2>

<form action="{{ route('canchas.update', $cancha) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Nombre:</label>
    <input type="text" name="nombre" value="{{ old('nombre', $cancha->nombre) }}" required><br>

    <label>Tipo:</label>
    <input type="text" name="tipo" value="{{ old('tipo', $cancha->tipo) }}"><br>

    <label>Precio por hora:</label>
    <input type="number" step="0.01" name="precio_hora" value="{{ old('precio_hora', $cancha->precio_hora) }}"><br>

    <button type="submit">Actualizar</button>
    <a href="{{ route('canchas.index') }}">Cancelar</a>
</form>
@endsection
