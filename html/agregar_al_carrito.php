<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int) $_POST["id"];
    $cantidad = (int) $_POST["cantidad"];

    if ($cantidad < 1) {
        header("Location: producto.php?id=$id");
        exit;
    }

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] += $cantidad;
    } else {
        $_SESSION['carrito'][$id] = $cantidad;
    }

    header("Location: carrito.php");
    exit;
}
?>
