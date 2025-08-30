<!DOCTYPE html>
<html>
<head><title>Usuarios</title></head>
<body>
    <h1>Usuarios</h1>
    <a href="{{ route('users.create') }}">Nuevo Usuario</a>
    <ul>
        @foreach($users as $user)
            <li>
                {{ $user->name }} - {{ $user->email }}
                <a href="{{ route('users.edit', ['user' => $user->id]) }}">Editar</a>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
