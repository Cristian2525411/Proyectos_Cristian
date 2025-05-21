<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verifica si se ha especificado un ID de producto
if (!isset($_GET['id'])) {
    echo "Producto no especificado.";
    exit;
}

$id = (int) $_GET['id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=tiendesilla", "root", "cristian");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener información del producto
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        echo "Producto no encontrado.";
        exit;
    }

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

    // Aplicar descuento si corresponde
    $categoriaProducto = strtolower(trim($producto['categoria']));
    if (isset($descuentos[$categoriaProducto])) {
        $descuento = $descuentos[$categoriaProducto];
        $producto['precio_original'] = $producto['precio'];
        $producto['precio_con_descuento'] = round($producto['precio'] * (1 - $descuento / 100), 2);
    }

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($producto['nombre']) ?> | Tiendesilla</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="futurista">

<div class="detalle">
    <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
    <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>

    <?php if (isset($producto['precio_con_descuento'])): ?>
        <p>
            <strong>Precio original:</strong> <span style="text-decoration: line-through;">$<?= number_format($producto['precio_original'], 2) ?></span><br>
            <strong>Precio con descuento:</strong> $<?= number_format($producto['precio_con_descuento'], 2) ?> (<?= $descuento ?>% desc)
        </p>
    <?php else: ?>
        <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
    <?php endif; ?>

    <p><strong>Stock disponible:</strong> <?= $producto['stock'] ?></p>

    <div class="navegacion">
        <a href="productos.php" class="boton">← Volver a productos</a>
    </div>
</div>

<div class="navegacion" style="text-align:center; margin-bottom: 20px;">
    <a href="carrito.php" class="boton">🛒 Ver carrito</a>
</div>

<form action="agregar_al_carrito.php" method="post">
    <input type="hidden" name="id" value="<?= $producto['id'] ?>">
    <label for="cantidad">Cantidad:</label>
    <input type="number" name="cantidad" id="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" required>
    <input type="submit" value="Añadir al carrito" class="boton">
</form>

</body>
</html>
