<?php
$host = 'localhost';
$user = 'root';
$pass = 'cristian';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        DROP DATABASE IF EXISTS tiendesilla;
        CREATE DATABASE tiendesilla CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
        USE tiendesilla;

        CREATE TABLE usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            contraseña VARCHAR(255) NOT NULL,
            id_qr VARCHAR(255) DEFAULT NULL
        );

        CREATE TABLE productos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT,
            precio DECIMAL(10,2) NOT NULL,
            stock INT NOT NULL DEFAULT 0,
            categoria VARCHAR(50) NOT NULL
        );

        INSERT INTO productos (nombre, descripcion, precio, stock, categoria) VALUES
        -- Comida (10)
        ('Sandwich mixto', 'Pan con jamón y queso', 2.50, 20, 'comida'),
        ('Empanada de pollo', 'Empanada casera rellena de pollo', 3.00, 15, 'comida'),
        ('Pizza individual', 'Porción de pizza de queso', 2.00, 25, 'comida'),
        ('Hamburguesa básica', 'Hamburguesa con carne y pan', 3.50, 10, 'comida'),
        ('Pan con tomate', 'Pan tostado con tomate y aceite de oliva', 1.50, 20, 'comida'),
        ('Croissant', 'Croissant de mantequilla', 1.80, 30, 'comida'),
        ('Bocadillo de atún', 'Pan con atún y mayonesa', 2.20, 18, 'comida'),
        ('Palmera de chocolate', 'Hojaldre con chocolate', 1.70, 22, 'comida'),
        ('Tortilla francesa', 'Tortilla de huevo sencilla', 2.00, 12, 'comida'),
        ('Galletas surtidas', 'Paquete de galletas variadas', 1.50, 40, 'comida'),

        -- Útiles escolares (7)
        ('Lápiz HB', 'Lápiz clásico de grafito', 0.50, 100, 'utiles'),
        ('Bolígrafo azul', 'Bolígrafo de tinta azul', 0.70, 80, 'utiles'),
        ('Cuaderno A5', 'Cuaderno de 80 hojas tamaño A5', 1.50, 50, 'utiles'),
        ('Pegamento en barra', 'Pegamento no tóxico de barra', 1.00, 40, 'utiles'),
        ('Tijeras pequeñas', 'Tijeras para uso escolar', 1.80, 30, 'utiles'),
        ('Caja de colores', 'Caja con 12 lápices de colores', 2.50, 25, 'utiles'),
        ('Regla 30cm', 'Regla de plástico transparente', 0.90, 60, 'utiles'),

        -- Bebidas (20)
        ('Agua mineral', 'Botella de agua 500ml', 1.00, 50, 'bebida'),
        ('Zumo de naranja', 'Zumo natural embotellado', 1.50, 30, 'bebida'),
        ('Refresco de cola', 'Lata de 330ml', 1.20, 45, 'bebida'),
        ('Refresco de limón', 'Lata de 330ml sabor limón', 1.20, 40, 'bebida'),
        ('Bebida energética', 'Lata de bebida energética', 2.00, 25, 'bebida'),
        ('Leche chocolatada', 'Botella de leche con chocolate', 1.80, 35, 'bebida'),
        ('Batido de fresa', 'Batido lácteo de fresa', 1.70, 30, 'bebida'),
        ('Té frío limón', 'Botella de té frío sabor limón', 1.60, 20, 'bebida'),
        ('Té frío melocotón', 'Té embotellado sabor melocotón', 1.60, 20, 'bebida'),
        ('Agua con gas', 'Agua mineral con gas', 1.10, 15, 'bebida'),
        ('Café en lata', 'Café listo para beber', 2.20, 15, 'bebida'),
        ('Smoothie mango', 'Bebida de frutas natural', 2.50, 10, 'bebida'),
        ('Bebida isotónica', 'Rehidratante con electrolitos', 1.90, 20, 'bebida'),
        ('Zumo multifrutas', 'Mezcla de frutas variadas', 1.50, 20, 'bebida'),
        ('Refresco sin azúcar', 'Refresco de cola sin azúcar', 1.20, 25, 'bebida'),
        ('Batido de chocolate', 'Batido de cacao', 1.70, 30, 'bebida'),
        ('Cerveza sin alcohol', 'Bebida sin alcohol', 1.50, 10, 'bebida'),
        ('Leche entera', 'Botella de leche entera', 1.00, 20, 'bebida'),
        ('Yogur líquido', 'Yogur bebible sabor vainilla', 1.30, 25, 'bebida'),
        ('Agua saborizada', 'Agua con sabor a frutas', 1.20, 25, 'bebida');

        -- Tabla de descuentos por usuario
        CREATE TABLE descuentos_usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            categoria VARCHAR(50) NOT NULL,
            porcentaje INT NOT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        );

        -- Insertar usuarios de prueba
        INSERT INTO usuarios (nombre, email, contraseña, id_qr) VALUES
        ('Ana Torres', 'ana@example.com', '1234', 'qrana123'),
        ('Luis Martínez', 'luis@example.com', 'abcd', 'qrluis456');

        -- Insertar descuentos personalizados
        INSERT INTO descuentos_usuario (usuario_id, categoria, porcentaje) VALUES
        (1, 'comida', 20),
        (1, 'bebida', 10),
        (2, 'utiles', 15),
        (2, 'bebida', 5);
    ";

    $pdo->exec($sql);
    echo "Base de datos, tablas, productos, usuarios y descuentos creados correctamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
