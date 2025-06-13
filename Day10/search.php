<?php
require_once 'data/database.php';

if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = '%' . $_GET['keyword'] . '%';
    
    $stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE name LIKE ? OR description LIKE ? LIMIT 10");
    $stmt->execute([$keyword, $keyword]);
    $products = $stmt->fetchAll();
    
    if (count($products) > 0) {
        foreach ($products as $product) {
            echo '<div class="search-item" data-id="' . $product['id'] . '">';
            echo '<div class="search-item-image"><img src="assets/images/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '"></div>';
            echo '<div class="search-item-info">';
            echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
            echo '<p class="price">' . number_format($product['price'], 0, ',', '.') . ' VNĐ</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>Không tìm thấy sản phẩm nào phù hợp.</p>';
    }
} else {
    echo '<p>Vui lòng nhập từ khóa tìm kiếm.</p>';
}
?>