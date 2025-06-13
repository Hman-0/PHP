<?php
if (isset($_GET['category'])) {
    $category = $_GET['category'];
    
    // Đọc file XML
    $xml = simplexml_load_file('data/brands.xml');
    
    if ($xml) {
        $brands = [];
        
        // Tìm danh sách thương hiệu theo danh mục
        foreach ($xml->category as $cat) {
            if ((string)$cat['name'] === $category) {
                foreach ($cat->brand as $brand) {
                    $brands[] = (string)$brand;
                }
                break;
            }
        }
        
        if (!empty($brands)) {
            // Trả về dạng HTML
            echo '<select id="brand-select" name="brand">';
            echo '<option value="">Chọn thương hiệu</option>';
            foreach ($brands as $brand) {
                echo '<option value="' . htmlspecialchars($brand) . '">' . htmlspecialchars($brand) . '</option>';
            }
            echo '</select>';
        } else {
            echo '<p>Không tìm thấy thương hiệu cho danh mục này.</p>';
        }
    } else {
        echo '<p>Không thể đọc file XML.</p>';
    }
} else {
    echo '<p>Không có danh mục được chọn.</p>';
}
?>