<?php
// Asegúrate de que la ruta al archivo QR sea correcta
require_once 'phpqrcode/qrlib.php';

// Configura la IP de tu servidor manualmente
$ip = '172.19.115.21';  // Reemplaza con la IP de tu servidor
$path = "productos.php"; // O la página a la que deseas redirigir

// Construye la URL con la IP
$url = "http://$ip/$path";

// Generar el código QR con la URL que contiene la IP del servidor
QRcode::png($url);
?>
