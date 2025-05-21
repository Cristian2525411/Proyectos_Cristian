<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=tiendesilla", "root", "cristian");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener descuentos del usuario
    $idUsuario = $_SESSION['usuario_id'];
    $stmtDescuentos = $pdo->prepare("SELECT categoria, porcentaje FROM descuentos_usuario WHERE usuario_id = ?");
    $stmtDescuentos->execute([$idUsuario]);
    $rawDescuentos = $stmtDescuentos->fetchAll(PDO::FETCH_ASSOC);

    $descuentos = [];
    foreach ($rawDescuentos as $row) {
        $cat = strtolower(trim($row['categoria']));
        $descuentos[$cat] = (float) $row['porcentaje'];
    }

    $productos = [];
    $total_general = 0;

    foreach ($carrito as $id => $cantidad) {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();

        if ($producto) {
            $categoria = strtolower(trim($producto['categoria']));

            $producto['cantidad'] = $cantidad;
            $producto['precio_original'] = $producto['precio'];

            // Aplicar descuento si existe
            if (isset($descuentos[$categoria])) {
                $descuento = $descuentos[$categoria];
                $producto['precio'] = round($producto['precio_original'] * (1 - $descuento / 100), 2);
                $producto['descuento_aplicado'] = $descuento;
            }

            $producto['subtotal'] = $producto['precio'] * $cantidad;
            $total_general += $producto['subtotal'];

            $productos[] = $producto;
        }
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de compras</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="futurista">
    <h1>🛒 Tu carrito</h1>

    <?php if (empty($productos)): ?>
        <p class="mensaje">Tu carrito está vacío.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($producto['nombre']) ?>
                            <a href="eliminar_del_carrito.php?id=<?= $producto['id'] ?>" class="boton">❌</a>
                            <?php if (isset($producto['descuento_aplicado'])): ?>
                                <br><small style="color: green;">Descuento aplicado: <?= $producto['descuento_aplicado'] ?>%</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($producto['descuento_aplicado'])): ?>
                                <span style="text-decoration: line-through;">$<?= number_format($producto['precio_original'], 2) ?></span><br>
                                $<?= number_format($producto['precio'], 2) ?>
                            <?php else: ?>
                                $<?= number_format($producto['precio'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= $producto['cantidad'] ?></td>
                        <td>$<?= number_format($producto['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Total general</strong></td>
                    <td><strong>$<?= number_format($total_general, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <div style="text-align:center; margin-top: 30px;">
            <a href="confirmar_compra.php" class="boton">✅ Finalizar compra</a>
        </div>
    <?php endif; ?>

    <div class="navegacion">
        <a href="productos.php" class="boton">← Volver a productos</a>
    </div>
</body>
</html>
