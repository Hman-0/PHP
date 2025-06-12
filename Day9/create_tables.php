<?php
require_once 'db.php';

try {
    // Tạo bảng products
    $sql_products = "
        CREATE TABLE IF NOT EXISTS products (
            id INT PRIMARY KEY AUTO_INCREMENT,
            product_name VARCHAR(100) NOT NULL,
            unit_price DECIMAL(10,2) NOT NULL,
            stock_quantity INT NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($sql_products);
    echo "Tạo bảng products thành công!\n";
    
    // Tạo bảng orders
    $sql_orders = "
        CREATE TABLE IF NOT EXISTS orders (
            id INT PRIMARY KEY AUTO_INCREMENT,
            order_date DATE NOT NULL,
            customer_name VARCHAR(100) NOT NULL,
            note TEXT
        )
    ";
    $pdo->exec($sql_orders);
    echo "Tạo bảng orders thành công!\n";
    
    // Tạo bảng order_items
    $sql_order_items = "
        CREATE TABLE IF NOT EXISTS order_items (
            id INT PRIMARY KEY AUTO_INCREMENT,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price_at_order_time DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )
    ";
    $pdo->exec($sql_order_items);
    echo "Tạo bảng order_items thành công!\n";
    
    echo "\nTất cả bảng đã được tạo thành công!\n";
    
} catch(PDOException $e) {
    echo "Lỗi tạo bảng: " . $e->getMessage() . "\n";
}
?>