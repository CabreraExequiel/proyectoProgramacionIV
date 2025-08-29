<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Reservas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        header { background: #333; color: white; padding: 10px; }
        nav a { color: white; margin-right: 10px; text-decoration: none; }
        main { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        button { margin: 2px; padding: 5px 10px; }
    </style>
</head>
<body>
    <header>
        <h1>Sistema de Reservas de Canchas</h1>
        <nav>
            <a href="{{ url('/inicio') }}">Inicio</a>
            <a href="{{ url('/clientes') }}">Clientes</a>
            <a href="{{ url('/reservas') }}">Reservas</a>
            <a href="{{ url('/canchas') }}">Canchas</a>
            <a href="{{ url('/informacion') }}">Informacion</a>

        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
