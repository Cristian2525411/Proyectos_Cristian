<?php
session_start();

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
    }
}

header("Location: carrito.php");
exit;
