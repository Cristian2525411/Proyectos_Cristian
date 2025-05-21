<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];

if (empty($carrito)) {
    echo "<p class='mensaje'>Tu carrito está vacío.</p>";
    echo "<a href='productos.php' class='boton'>Volver</a>";
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=tiendesilla", "root", "cristian");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    foreach ($carrito as $id => $cantidad) {
        // Obtener producto actual
        $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();

        if (!$producto || $producto['stock'] < $cantidad) {
            throw new Exception("No hay stock suficiente para el producto con ID $id.");
        }

        // Actualizar stock
        $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$cantidad, $id]);
    }

    $pdo->commit();
    $_SESSION['carrito'] = []; // Vaciar el carrito

    $mensaje = "✅ ¡Compra realizada con éxito! Gracias por confiar en Tiendesilla.";

} catch (Exception $e) {
    $pdo->rollBack();
    $mensaje = "❌ Error al realizar la compra: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra finalizada</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="futurista">
    <h1>Finalizar compra</h1>
    <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="productos.php" class="boton">← Volver a productos</a>
    </div>
</body>
</html>
