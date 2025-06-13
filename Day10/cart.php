<?php
session_start();
require_once 'data/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    
    // Kiểm tra sản phẩm có tồn tại không
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Khởi tạo giỏ hàng nếu chưa có
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Thêm sản phẩm vào giỏ hàng hoặc tăng số lượng nếu đã có
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1
            ];
        }
        
        // Tính tổng số sản phẩm trong giỏ hàng
        $cartCount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += $item['quantity'];
        }
        
        echo json_encode([
            'success' => true,
            'cartCount' => $cartCount,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Sản phẩm không tồn tại.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Yêu cầu không hợp lệ.'
    ]);
}
?>