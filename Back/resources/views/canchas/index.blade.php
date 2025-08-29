@extends('layouts.app')

@section('content')
<h2>Listado de Canchas</h2>
<a href="{{ route('canchas.create') }}">Agregar Cancha</a>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Precio/Hora</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($canchas as $cancha)
        <tr>
            <td>{{ $cancha->nombre }}</td>
            <td>{{ $cancha->tipo }}</td>
            <td>{{ $cancha->precio_hora }}</td>
            <td>
                <a href="{{ route('canchas.edit', $cancha) }}">Editar</a>
                <form action="{{ route('canchas.destroy', $cancha) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
