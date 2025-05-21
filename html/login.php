<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=tiendesilla", 'root', 'cristian');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
            $_SESSION['usuario'] = $usuario['nombre'];
	     $_SESSION['usuario_id'] = $usuario['id'];
            header("Location: productos.php");
            exit;
        } else {
            echo "Credenciales incorrectas.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<head>
<link rel="stylesheet" href="estilos.css">
</head>
<form method="POST">
    Email: <input type="email" name="email" required><br>
    Contraseña: <input type="password" name="contraseña" required><br>
    <button type="submit">Iniciar sesión</button>
</form>
