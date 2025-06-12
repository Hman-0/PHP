<?php
require_once 'db.php';

try {
    echo "=== THAO TÁC DỮ LIỆU TECHFACTORY ===<br><br>";
    
    // 4.1 Insert - Thêm 5 sản phẩm mẫu
    echo "4.1 Thêm 5 sản phẩm mẫu:<br>";
    $products = [
        ['Động cơ servo AC', 2500000, 50],
        ['Cảm biến nhiệt độ', 850000, 100],
        ['Bảng điều khiển PLC', 15000000, 20],
        ['Biến tần 3 pha', 3200000, 30],
        ['Cảm biến áp suất', 1200000, 75]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO products (product_name, unit_price, stock_quantity) VALUES (?, ?, ?)");
    
    foreach($products as $product) {
        $stmt->execute($product);
        echo "- Thêm sản phẩm: {$product[0]}<br>";
    }
    
    // 4.2 Lấy ID cuối
    $lastId = $pdo->lastInsertId();
    echo "<br>4.2 ID sản phẩm cuối cùng được thêm: $lastId<br><br>";
    
    // 4.3 Insert nhiều bản ghi - Thêm đơn hàng và chi tiết
    echo "4.3 Thêm 3 đơn hàng với chi tiết:<br>";
    
    // Đơn hàng 1
    $stmt_order = $pdo->prepare("INSERT INTO orders (order_date, customer_name, note) VALUES (?, ?, ?)");
    $stmt_order->execute(['2024-01-15', 'Công ty TNHH ABC', 'Đơn hàng khẩn cấp']);
    $order1_id = $pdo->lastInsertId();
    
    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order_time) VALUES (?, ?, ?, ?)");
    $stmt_item->execute([$order1_id, 1, 2, 2500000]); // 2 động cơ servo
    $stmt_item->execute([$order1_id, 3, 1, 15000000]); // 1 bảng điều khiển
    echo "- Đơn hàng #$order1_id: Công ty TNHH ABC<br>";
    
    // Đơn hàng 2
    $stmt_order->execute(['2024-01-16', 'Nhà máy XYZ', 'Giao hàng trong tuần']);
    $order2_id = $pdo->lastInsertId();
    
    $stmt_item->execute([$order2_id, 2, 5, 850000]); // 5 cảm biến nhiệt độ
    $stmt_item->execute([$order2_id, 4, 1, 3200000]); // 1 biến tần
    $stmt_item->execute([$order2_id, 5, 3, 1200000]); // 3 cảm biến áp suất
    echo "- Đơn hàng #$order2_id: Nhà máy XYZ<br>";
    
    // Đơn hàng 3
    $stmt_order->execute(['2024-01-17', 'Tập đoàn DEF', null]);
    $order3_id = $pdo->lastInsertId();
    
    $stmt_item->execute([$order3_id, 1, 1, 2500000]); // 1 động cơ servo
    $stmt_item->execute([$order3_id, 2, 2, 850000]); // 2 cảm biến nhiệt độ
    echo "- Đơn hàng #$order3_id: Tập đoàn DEF<br><br>";
    
    // 4.4 Prepared Statement - Thêm sản phẩm mới
    echo "4.4 Sử dụng Prepared Statement thêm sản phẩm mới:<br>";
    $stmt_new_product = $pdo->prepare("
        INSERT INTO products (product_name, unit_price, stock_quantity) 
        VALUES (:name, :price, :quantity)
    ");
    
    $new_product = [
        'name' => 'Relay điều khiển',
        'price' => 450000,
        'quantity' => 200
    ];
    
    $stmt_new_product->execute($new_product);
    echo "- Thêm sản phẩm mới: {$new_product['name']}<br><br>";
    
    // 4.5 Select - Hiển thị toàn bộ sản phẩm
    echo "4.5 Danh sách toàn bộ sản phẩm:<br>";
    $stmt = $pdo->query("SELECT * FROM products");
    while($row = $stmt->fetch()) {
        echo "- ID: {$row['id']}, Tên: {$row['product_name']}, Giá: " . number_format($row['unit_price']) . " VNĐ, Tồn kho: {$row['stock_quantity']}<br>";
    }
    echo "<br>";
    
    // 4.6 Where - Lọc sản phẩm giá > 1.000.000
    echo "4.6 Sản phẩm có giá > 1.000.000 VNĐ:<br>";
    $stmt = $pdo->prepare("SELECT * FROM products WHERE unit_price > ?");
    $stmt->execute([1000000]);
    while($row = $stmt->fetch()) {
        echo "- {$row['product_name']}: " . number_format($row['unit_price']) . " VNĐ<br>";
    }
    echo "<br>";
    
    // 4.7 Order By - Sắp xếp theo giá giảm dần
    echo "4.7 Sản phẩm sắp xếp theo giá giảm dần:<br>";
    $stmt = $pdo->query("SELECT * FROM products ORDER BY unit_price DESC");
    while($row = $stmt->fetch()) {
        echo "- {$row['product_name']}: " . number_format($row['unit_price']) . " VNĐ<br>";
    }
    echo "<br>";
    
    // 4.8 Delete - Xóa sản phẩm theo ID
    echo "4.8 Xóa sản phẩm ID = 6:<br>";
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $result = $stmt->execute([6]);
    if($stmt->rowCount() > 0) {
        echo "- Đã xóa sản phẩm thành công<br>";
    } else {
        echo "- Không tìm thấy sản phẩm để xóa<br>";
    }
    echo "<br>";
    
    // 4.9 Update - Cập nhật giá và tồn kho
    echo "4.9 Cập nhật giá và tồn kho sản phẩm ID = 1:<br>";
    $stmt = $pdo->prepare("UPDATE products SET unit_price = ?, stock_quantity = ? WHERE id = ?");
    $stmt->execute([2800000, 45, 1]);
    echo "- Đã cập nhật sản phẩm ID = 1<br><br>";
    
    // 4.10 Limit - Lấy 5 sản phẩm mới nhất
    echo "4.10 5 sản phẩm mới nhất:<br>";
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
    while($row = $stmt->fetch()) {
        echo "- {$row['product_name']} (Tạo lúc: {$row['created_at']})<br>";
    }
    
} catch(PDOException $e) {
    echo "Lỗi thao tác dữ liệu: " . $e->getMessage() . "<br>";
}
?>