@extends('layouts.app')

@section('content')
<h1>Nueva Reserva</h1>

<form action="{{ route('reservas.store') }}" method="POST">
    @csrf
    <label>Cliente:</label>
    <input type="text" name="cliente" required><br>

    <label>Teléfono:</label>
    <input type="text" name="telefono"><br>

    <label>Fecha:</label>
    <input type="date" name="fecha" required><br>

    <label>Hora Inicio:</label>
    <input type="time" name="hora_inicio" required><br>

    <label>Hora Fin:</label>
    <input type="time" name="hora_fin" required><br>

    <label>Cancha:</label>
    <select name="cancha_id" required>
        @foreach($canchas as $cancha)
            <option value="{{ $cancha->id }}">{{ $cancha->nombre }} - {{ $cancha->tipo }}</option>
        @endforeach
    </select><br>

    <label>Estado:</label>
    <select name="estado">
        <option value="activa">Activa</option>
        <option value="pendiente">Pendiente</option>
        <option value="cancelada">Cancelada</option>
    </select><br>


    <button type="submit">Guardar</button>
</form>
@endsection
