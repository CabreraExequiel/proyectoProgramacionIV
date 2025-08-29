<!DOCTYPE html>
<html>
<head><title>Editar Usuario</title></head>
<body>
    <h1>Editar Usuario</h1>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        Nombre: <input type="text" name="name" value="{{ $user->name }}"><br>
        Email: <input type="email" name="email" value="{{ $user->email }}"><br>
        Password: <input type="password" name="password"><br>
        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
