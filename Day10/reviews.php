<?php
require_once 'data/database.php';

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
    $stmt->execute([$product_id]);
    $reviews = $stmt->fetchAll();
    
    if (count($reviews) > 0) {
        echo '<div class="reviews-list">';
        foreach ($reviews as $review) {
            echo '<div class="review-item">';
            echo '<div class="review-header">';
            echo '<span class="review-author">' . htmlspecialchars($review['user_name']) . '</span>';
            echo '<span class="review-date">' . date('d/m/Y', strtotime($review['created_at'])) . '</span>';
            echo '</div>';
            echo '<div class="review-rating">';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $review['rating']) {
                    echo '<span class="star filled">★</span>';
                } else {
                    echo '<span class="star">☆</span>';
                }
            }
            echo '</div>';
            echo '<div class="review-comment">' . htmlspecialchars($review['comment']) . '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>Chưa có đánh giá nào cho sản phẩm này.</p>';
    }
} else {
    echo '<p>Không có ID sản phẩm.</p>';
}
?>