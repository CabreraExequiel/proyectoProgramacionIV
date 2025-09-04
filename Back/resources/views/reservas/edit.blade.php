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
    <select name="cancha_id" required>
        @foreach($canchas as $cancha)
            <option value="{{ $cancha->id }}" {{ $cancha->id == old('cancha_id', $reserva->cancha_id) ? 'selected' : '' }}>
                {{ $cancha->nombre }} - {{ $cancha->tipo }}
            </option>
        @endforeach
    </select><br>
    <label>Estado:</label>
    <select name="estado">
        <option value="activa" {{ old('estado', $reserva->estado) == 'activa' ? 'selected' : '' }}>Activa</option>
        <option value="pendiente" {{ old('estado', $reserva->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
        <option value="cancelada" {{ old('estado', $reserva->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
    </select><br>



    <button type="submit">Actualizar</button>
    <a href="{{ route('reservas.index') }}">Cancelar</a>
</form>
@endsection
