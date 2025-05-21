<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=tiendesilla", 'root', 'cristian');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $email, $contraseña]);

        header("Location: index.html");
        exit;
    } catch (PDOException $e) {
        echo "Error al registrar usuario: " . $e->getMessage();
    }
}
?>
<link rel="stylesheet" href="estilos.css">
<form method="POST">
    Nombre: <input type="text" name="nombre" required><br>
    Email: <input type="email" name="email" required><br>
    Contraseña: <input type="password" name="contraseña" required><br>
    <button type="submit">Registrarse</button>
</form>
