@extends('layouts.app')

@section('content')
<h1>Reservas</h1>
<a href="{{ route('reservas.create') }}">Nueva Reserva</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Cancha</th>
        <th>Acciones</th>
    </tr>
    @foreach($reservas as $reserva)
    <tr>
        <td>{{ $reserva->id }}</td>
        <td>{{ $reserva->cliente }}</td>
        <td>{{ $reserva->fecha }}</td>
        <td>{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</td>
        <td>{{ $reserva->cancha->nombre ?? 'N/A' }}</td>
        <td>
            <a href="{{ route('reservas.edit', $reserva) }}">Editar</a>
            <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection
