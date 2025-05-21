<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=tiendesilla", 'root', 'cristian');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$idUsuario = $_SESSION['usuario_id'];

// Obtener descuentos y normalizarlos
$stmtDescuentos = $pdo->prepare("SELECT categoria, porcentaje FROM descuentos_usuario WHERE usuario_id = ?");
$stmtDescuentos->execute([$idUsuario]);
$rawDescuentos = $stmtDescuentos->fetchAll(PDO::FETCH_ASSOC);

$descuentos = [];
foreach ($rawDescuentos as $row) {
    $cat = strtolower(trim($row['categoria']));
    $descuentos[$cat] = (float) $row['porcentaje'];
}

// Obtener productos
$categoria = $_GET['categoria'] ?? 'todos';
if ($categoria === 'todos') {
    $stmt = $pdo->query("SELECT * FROM productos");
} else {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE categoria = ?");
    $stmt->execute([$categoria]);
}
$productos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="estilos.css">
    <meta charset="UTF-8">
    <title>Productos - Tiendesilla</title>
</head>
<body class="futurista">
<h1>Bienvenido, <?= htmlspecialchars($_SESSION['usuario']) ?></h1>

<div class="categorias">
    <a href="productos.php?categoria=todos" class="<?= $categoria === 'todos' ? 'activo' : '' ?>">Todos</a>
    <a href="productos.php?categoria=comida" class="<?= $categoria === 'comida' ? 'activo' : '' ?>">Comida</a>
    <a href="productos.php?categoria=bebida" class="<?= $categoria === 'bebida' ? 'activo' : '' ?>">Bebidas</a>
    <a href="productos.php?categoria=utiles" class="<?= $categoria === 'utiles' ? 'activo' : '' ?>">Útiles</a>
</div>

<div class="grid">
    <?php if (count($productos) > 0): ?>
        <?php foreach ($productos as &$producto): ?>
            <?php
            $cat = strtolower(trim($producto['categoria']));
            if (isset($descuentos[$cat])) {
                $descuento = $descuentos[$cat];
                $producto['precio_original'] = $producto['precio'];
                $producto['precio_con_descuento'] = round($producto['precio'] * (1 - $descuento / 100), 2);
            }
            ?>
            <a href="producto.php?id=<?= $producto['id'] ?>" class="card">
                <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                <p><?= htmlspecialchars($producto['descripcion']) ?></p>

                <?php if (isset($producto['precio_con_descuento'])): ?>
                    <p><strong style="text-decoration: line-through;">$<?= number_format($producto['precio_original'], 2) ?></strong></p>
                    <p><strong>$<?= number_format($producto['precio_con_descuento'], 2) ?> (<?= $descuento ?>% desc)</strong></p>
                <?php else: ?>
                    <p><strong>$<?= number_format($producto['precio'], 2) ?></strong></p>
                <?php endif; ?>

                <p>Stock: <?= $producto['stock'] ?></p>
                <img src="imagenes/<?= $producto['id'] ?>.jpg" alt="<?= htmlspecialchars($producto['nombre']) ?>">
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No se han encontrado productos en esta categoría.</p>
    <?php endif; ?>
</div>

<div class="logout">
    <a href="logout.php">Cerrar sesión</a>
</div>

<div class="navegacion" style="text-align:center; margin-bottom: 20px;">
    <a href="carrito.php" class="boton">🛒 Ver carrito</a>
</div>

<div class="navegacion" style="text-align:left; margin-bottom: 20px;">
    <a href="qr.php" class="boton">Tu código QR para tus descuentos</a>
</div>

<div class="navegacion" style="text-align:right; margin-bottom: 20px;">
    <a href="descuentos.php" class="boton">Tus descuentos</a>
</div>
</body>
</html>
