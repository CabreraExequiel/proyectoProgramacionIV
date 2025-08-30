@extends('layouts.app')

@section('content')
<h1>Editar Reserva</h1>

<form action="{{ route('reservas.update', $reserva->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Cliente:</label>
    <input type="text" name="cliente" value="{{ old('cliente', $reserva->cliente) }}" required><br>

    <label>Teléfono:</label>
    <input type="text" name="telefono" value="{{ old('telefono', $reserva->telefono) }}"><br>

    <label>Fecha:</label>
    <input type="date" name="fecha" value="{{ old('fecha', $reserva->fecha) }}" required><br>

    <label>Hora Inicio:</label>
    <input type="time" name="hora_inicio" value="{{ old('hora_inicio', $reserva->hora_inicio) }}" required><br>

    <label>Hora Fin:</label>
    <input type="time" name="hora_fin" value="{{ old('hora_fin', $reserva->hora_fin) }}" required><br>

    <label>Cancha:</label>
    <input type="number" name="cancha_id" value="{{ old('cancha_id', $reserva->cancha_id) }}" required><br>

    <button type="submit">Actualizar</button>
    <a href="{{ route('reservas.index') }}">Cancelar</a>
</form>
@endsection
