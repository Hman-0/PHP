<?php
require_once 'db.php';

try {
    echo "=== BÁO CÁO NÂNG CAO ===<br><br>";
    
    // Tính tổng tiền từng đơn hàng
    echo "1. Tổng tiền từng đơn hàng:<br>";
    $stmt = $pdo->query("
        SELECT 
            o.id,
            o.customer_name,
            o.order_date,
            SUM(oi.quantity * oi.price_at_order_time) as total_amount
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id, o.customer_name, o.order_date
        ORDER BY o.order_date DESC
    ");
    
    while($row = $stmt->fetch()) {
        echo "- Đơn hàng #{$row['id']}: {$row['customer_name']} - " . number_format($row['total_amount']) . " VNĐ ({$row['order_date']})<br>";
    }
    echo "<br>";
    
    // Chi tiết đơn hàng với thông tin sản phẩm
    echo "2. Chi tiết đơn hàng với thông tin sản phẩm:<br>";
    $stmt = $pdo->query("
        SELECT 
            o.id as order_id,
            o.customer_name,
            p.product_name,
            oi.quantity,
            oi.price_at_order_time,
            (oi.quantity * oi.price_at_order_time) as line_total
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        ORDER BY o.id, p.product_name
    ");
    
    $current_order = null;
    while($row = $stmt->fetch()) {
        if($current_order != $row['order_id']) {
            if($current_order !== null) echo "<br>";
            echo "Đơn hàng #{$row['order_id']} - {$row['customer_name']}:<br>";
            $current_order = $row['order_id'];
        }
        echo "  + {$row['product_name']}: {$row['quantity']} x " . number_format($row['price_at_order_time']) . " = " . number_format($row['line_total']) . " VNĐ<br>";
    }
    echo "<br>";
    
    // Thống kê tồn kho
    echo "3. Thống kê tồn kho:<br>";
    $stmt = $pdo->query("
        SELECT 
            product_name,
            stock_quantity,
            unit_price,
            (stock_quantity * unit_price) as inventory_value
        FROM products 
        ORDER BY inventory_value DESC
    ");
    
    $total_inventory_value = 0;
    while($row = $stmt->fetch()) {
        echo "- {$row['product_name']}: {$row['stock_quantity']} cái (Giá trị: " . number_format($row['inventory_value']) . " VNĐ)<br>";
        $total_inventory_value += $row['inventory_value'];
    }
    echo "<br>Tổng giá trị tồn kho: " . number_format($total_inventory_value) . " VNĐ<br>";
    
} catch(PDOException $e) {
    echo "Lỗi tạo báo cáo: " . $e->getMessage() . "<br>";
}
?>