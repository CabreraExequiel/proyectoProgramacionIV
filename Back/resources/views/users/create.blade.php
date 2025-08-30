<!DOCTYPE html>
<html>
<head><title>Crear Usuario</title></head>
<body>
    <h1>Crear Usuario</h1>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        Nombre: <input type="text" name="name"><br>
        Email: <input type="email" name="email"><br>
        Password: <input type="password" name="password"><br>
        <button type="submit">Guardar</button>
    </form>
</body>
</html>
