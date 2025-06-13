<?php
require_once 'data/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if ($product) {
        echo '<div class="product-detail">';
        echo '<h2>' . htmlspecialchars($product['name']) . '</h2>';
        echo '<div class="product-image"><img src="assets/images/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '"></div>';
        echo '<div class="product-info">';
        echo '<p class="description">' . htmlspecialchars($product['description']) . '</p>';
        echo '<p class="price">Giá: ' . number_format($product['price'], 0, ',', '.') . ' VNĐ</p>';
        echo '<p class="stock">Còn lại: ' . $product['stock'] . ' sản phẩm</p>';
        echo '<button class="add-to-cart" data-id="' . $product['id'] . '">Thêm vào giỏ hàng</button>';
        echo '</div>';
        echo '<div class="product-tabs">';
        echo '<ul class="tabs">';
        echo '<li class="active"><a href="#description">Mô tả</a></li>';
        echo '<li><a href="#reviews" id="reviews-tab">Đánh giá</a></li>';
        echo '</ul>';
        echo '<div class="tab-content">';
        echo '<div id="description" class="tab-pane active">';
        echo '<p>' . htmlspecialchars($product['description']) . '</p>';
        echo '</div>';
        echo '<div id="reviews" class="tab-pane">';
        echo '<div id="reviews-content">Đang tải đánh giá...</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p>Không tìm thấy sản phẩm.</p>';
    }
} else {
    echo '<p>Không có ID sản phẩm.</p>';
}
?>