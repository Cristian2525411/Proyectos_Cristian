<?php
session_start();
if (!isset($_SESSION['usuario_13'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $email = $_SESSION['usuario_13']; // asumimos que guardaste el email en sesión

    try {
        $conn = new PDO("mysql:host=localhost;dbname=tiendesilla", 'cliente', 'cristian');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("INSERT INTO productos_pendientes (nombre, descripcion, precio, stock, usuario_email) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $email]);

        echo "✅ Solicitud enviada. Esperando aprobación del administrador.";
    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage();
    }
}
?>

<form method="POST">
    <h2>Solicitar nuevo producto</h2>
    Nombre: <input type="text" name="nombre" required><br>
    Descripción: <textarea name="descripcion" required></textarea><br>
    Precio: <input type="number" step="0.01" name="precio" required><br>
    Stock: <input type="number" name="stock" required><br>
    <input type="submit" value="Enviar solicitud">
</form>
