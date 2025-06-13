<?php
session_start();
require_once 'data/database.php';

// Lấy danh sách sản phẩm
$stmt = $pdo->query("SELECT id, name, price, image FROM products");
$products = $stmt->fetchAll();

// Lấy danh sách danh mục từ XML
$xml = simplexml_load_file('data/brands.xml');
$categories = [];
if ($xml) {
    foreach ($xml->category as $category) {
        $categories[] = (string)$category['name'];
    }
}

include 'includes/header.php';
?>

<div class="page-content">
    <div class="sidebar">
        <div class="category-filter">
            <h3>Danh mục sản phẩm</h3>
            <select id="category-select">
                <option value="">Chọn danh mục</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                <?php endforeach; ?>
            </select>
            <div id="brand-container">
                <!-- Danh sách thương hiệu sẽ được load bằng AJAX -->
            </div>
        </div>
    </div>
    
    <div class="product-list">
        <h2>Danh sách sản phẩm</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
            <div class="product-item" data-id="<?php echo $product['id']; ?>">
                <div class="product-image">
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
                    <button class="view-detail" data-id="<?php echo $product['id']; ?>">Xem chi tiết</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div id="product-detail-container" class="product-detail-container">
    <!-- Chi tiết sản phẩm sẽ được load bằng AJAX -->
</div>

<?php include 'includes/footer.php'; ?>