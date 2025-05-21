<?php
// Autenticación de administrador omitida por simplicidad
$conn = new PDO("mysql:host=localhost;dbname=tiendesilla", 'cliente', 'cristian');

if (isset($_GET['aprobar'])) {
    $id = $_GET['aprobar'];

    // Copiar a productos y eliminar de pendientes
    $stmt = $conn->prepare("SELECT * FROM productos_pendientes WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        $insert = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
        $insert->execute([
            $producto['nombre'],
            $producto['descripcion'],
            $producto['precio'],
            $producto['stock']
        ]);

        $delete = $conn->prepare("DELETE FROM productos_pendientes WHERE id = ?");
        $delete->execute([$id]);

        echo "✅ Producto aprobado y añadido a la tienda.";
    }
}

if (isset($_GET['rechazar'])) {
    $id = $_GET['rechazar'];
    $conn->prepare("DELETE FROM productos_pendientes WHERE id = ?")->execute([$id]);
    echo "❌ Producto rechazado.";
}

// Mostrar pendientes
$pendientes = $conn->query("SELECT * FROM productos_pendientes")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Solicitudes pendientes</h2>
<?php foreach ($pendientes as $p): ?>
    <div style="border:1px solid #ccc; margin:10px; padding:10px;">
        <strong><?= htmlspecialchars($p['nombre']) ?></strong><br>
        <?= htmlspecialchars($p['descripcion']) ?><br>
        Precio: €<?= $p['precio'] ?> - Stock: <?= $p['stock'] ?><br>
        Solicitado por: <?= $p['usuario_email'] ?><br>
        <a href="?aprobar=<?= $p['id'] ?>">✅ Aprobar</a> | 
        <a href="?rechazar=<?= $p['id'] ?>">❌ Rechazar</a>
    </div>
<?php endforeach; ?>
