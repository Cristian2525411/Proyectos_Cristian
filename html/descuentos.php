<?php
require_once 'conexion.php';

$token = $_GET['token'] ?? '';

// Validar el token (solo caracteres alfanuméricos)
if (!preg_match('/^[a-zA-Z0-9]+$/', $token)) {
    die("Token inválido.");
}

// Obtener usuario por token
$stmt = $pdo->prepare("SELECT * FROM tiendesilla.usuarios WHERE id_qr = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Token inválido o expirado.");
}

$nombre = htmlspecialchars($user['nombre']);
$usuario_id = $user['id'];

// Obtener descuentos personalizados
$stmt_descuentos = $pdo->prepare("SELECT categoria, porcentaje FROM tiendesilla.descuentos_usuario WHERE usuario_id = ?");
$stmt_descuentos->execute([$usuario_id]);
$descuentos = $stmt_descuentos->fetchAll(PDO::FETCH_ASSOC);

// Mostrar HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descuentos Personalizados</title>
</head>
<body>
    <h1>Bienvenido, <?= $nombre ?></h1>
    <p>Estos son tus descuentos personalizados de hoy:</p>

    <?php if (count($descuentos) > 0): ?>
        <ul>
            <?php foreach ($descuentos as $d): ?>
                <li><?= htmlspecialchars($d['categoria']) ?>: <strong><?= $d['porcentaje'] ?>%</strong> de descuento</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No tienes descuentos asignados actualmente.</p>
    <?php endif; ?>
</body>
</html>
