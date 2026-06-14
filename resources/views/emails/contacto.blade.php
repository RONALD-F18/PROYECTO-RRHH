<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Nuevo mensaje de contacto — Talent Sphere</h2>
    <p><strong>Nombre:</strong> {{ $nombre }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Asunto:</strong> {{ $asunto }}</p>
    <hr>
    <p><strong>Mensaje:</strong></p>
    <p>{!! $mensaje !!}</p>
</body>
</html>
