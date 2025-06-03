<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phân tích chiến dịch Affiliate Marketing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            overflow-x: auto;
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            background: #3b82f6;
            border-radius: 50%;
            position: absolute;
            left: -6px;
            top: 4px;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <header class="gradient-bg text-white rounded-t-2xl p-6 text-center">
            <h1 class="text-3xl md:text-4xl font-bold">Phân tích chiến dịch Affiliate Marketing</h1>
            <p class="mt-2 text-lg opacity-80">Báo cáo hiệu quả và khuyến nghị chiến lược</p>
        </header>

        <div class="card p-6 mt-6">
            <?php

            echo "<section class='mb-8'>";
            echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3'>Thông tin Debug</h2>";
            echo "<div class='bg-gray-50 p-4 rounded-lg'>";
            echo "<p class='text-gray-700'>File đang chạy: " . __FILE__ . "</p>";
            echo "<p class='text-gray-700'>Dòng hiện tại: " . __LINE__ . "</p>";
            echo "</div>";
            echo "</section>";

            const COMMISSION_RATE = 0.2;  // Tỷ lệ hoa hồng 20%
            const VAT_RATE = 0.1;         // Thuế VAT 10%

            echo "<section class='mb-8'>";
            echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3'>Chiến dịch Affiliate Marketing</h2>";
            echo "<div class='bg-gray-50 p-4 rounded-lg'>";
            echo "<p class='text-gray-700'>Tỷ lệ hoa hồng: " . (COMMISSION_RATE * 100) . "%</p>";
            echo "<p class='text-gray-700'>Thuế VAT: " . (VAT_RATE * 100) . "%</p>";
            echo "</div>";
            echo "</section>";

            // 1. KHAI BÁO DỮ LIỆU ĐẦU VÀO
            $campaign_name = "Spring Sale 2025";
            $order_count = "150";
            $product_price = 99.99;
            $product_name = "Áo thời trang cao cấp";
            $campaign_status = true;
            $product_category = "Thời trang";

            // Chuyển đổi kiểu dữ liệu
            $order_count = (int)$order_count;

            // Danh sách đơn hàng
            $order_list = array(
                "ID001" => 99.99,
                "ID002" => 49.99,
                "ID003" => 129.99,
                "ID004" => 79.99,
                "ID005" => 159.99,
                "ID006" => 89.99,
                "ID007" => 109.99
            );

            echo "<section class='mb-8'>";
            echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3'>Thông tin chiến dịch</h2>";
            echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg'>";
            echo "<p><strong>Tên chiến dịch:</strong> " . htmlspecialchars($campaign_name) . "</p>";
            echo "<p><strong>Sản phẩm:</strong> " . htmlspecialchars($product_name) . "</p>";
            echo "<p><strong>Loại sản phẩm:</strong> " . htmlspecialchars($product_category) . "</p>";
            echo "<p><strong>Số lượng đơn hàng:</strong> " . $order_count . "</p>";
            echo "<p><strong>Giá sản phẩm chuẩn:</strong> $" . number_format($product_price, 2) . "</p>";
            $status_text = ($campaign_status === true) ? "Đã kết thúc" : "Đang chạy";
            echo "<p><strong>Trạng thái:</strong> " . $status_text . "</p>";
            echo "</div>";
            echo "</section>";

            // 2. TÍNH TOÁN DOANH THU TỪ DANH SÁCH ĐƠN HÀNG
            echo "<section class='mb-8'>";
            echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3'>Tính toán doanh thu</h2>";
            echo "<div class='bg-gray-50 p-4 rounded-lg'>";
            echo "<p class='text-gray-600 mb-2'>Dòng hiện tại: " . __LINE__ . "</p>";

            $total_revenue_from_orders = 0;
            $order_counter = 0;

            echo "<div class='table-container'>";
            echo "<table class='w-full border-collapse'>";
            echo "<thead><tr class='bg-blue-600 text-white'><th class='p-3'>Mã đơn hàng</th><th class='p-3'>Giá trị</th></tr></thead>";
            echo "<tbody>";
            foreach ($order_list as $order_id => $order_value) {
                $order_counter++;
                $total_revenue_from_orders += $order_value;
                echo "<tr class='hover:bg-gray-100'><td class='p-3'>" . htmlspecialchars($order_id) . "</td><td class='p-3'>$" . number_format($order_value, 2) . "</td></tr>";
            }
            echo "</tbody></table>";
            echo "</div>";

            echo "<h3 class='text-lg font-semibold mt-4 mb-2'>Thống kê tổng quan:</h3>";
            echo "<ul class='list-disc pl-5'>";
            echo "<li>Tổng số đơn hàng trong danh sách: " . count($order_list) . "</li>";
            echo "<li>Doanh thu từ danh sách đơn: $" . number_format($total_revenue_from_orders, 2) . "</li>";
            echo "<li>Giá trị đơn hàng trung bình: $" . number_format($total_revenue_from_orders / count($order_list), 2) . "</li>";
            echo "</ul>";

            $revenue = $product_price * $order_count;
            echo "<p class='mt-4'><strong>Doanh thu tổng thể:</strong> $" . number_format($revenue, 2) . "</p>";
            echo "<p class='text-gray-600'>Công thức: $" . $product_price . " × " . $order_count . " = $" . number_format($revenue, 2) . "</p>";
            echo "</div>";
            echo "</section>";

            // 3. TÍNH TOÁN CHI PHÍ VÀ LỢI NHUẬN
            echo "<section class='mb-8'>";
            echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3'>Tính toán chi phí và lợi nhuận</h2>";
            echo "<div class='bg-gray-50 p-4 rounded-lg'>";
            echo "<p class='text-gray-600 mb-2'>Dòng hiện tại: " . __LINE__ . "</p>";

            $commission_cost = $revenue * COMMISSION_RATE;
            echo "<p><strong>Chi phí hoa hồng:</strong> $" . number_format($commission_cost, 2) . "</p>";
            echo "<p class='text-gray-600'>Công thức: $" . number_format($revenue, 2) . " × " . (COMMISSION_RATE * 100) . "% = $" . number_format($commission_cost, 2) . "</p>";

            $vat_cost = $revenue * VAT_RATE;
            echo "<p><strong>Thuế VAT:</strong> $" . number_format($vat_cost, 2) . "</p>";
            echo "<p class='text-gray-600'>Công thức: $" . number_format($revenue, 2) . " × " . (VAT_RATE * 100) . "% = $" . number_format($vat_cost, 2) . "</p>";

            $profit = $revenue - $commission_cost - $vat_cost;
            echo "<p class='mt-4'><strong>Lợi nhuận:</strong> $" . number_format($profit, 2) . "</p>";
            echo "<p class='text-gray-600'>Công thức: $" . number_format($revenue, 2) . " - $" . number_format($commission_cost, 2) . " - $" . number_format($vat_cost, 2) . " = $" . number_format($profit, 2) . "</p>";
            echo "</div>";
            echo "</section>";

            // 4. ĐÁNH GIÁ HIỆU QUẢ CHIẾN DỊCH
            echo "<section class='mb-8'>";
            echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3'>Đánh giá hiệu quả</h2>";
            echo "<div class='bg-gray-50 p-4 rounded-lg'>";
            echo "<p class='text-gray-600 mb-2'>Dòng hiện tại: " . __LINE__ . "</p>";

            if ($profit > 0) {
                $campaign_result = "Chiến dịch thành công";
                $performance_level = "Tốt";
                $result_class = "text-green-600";
            } elseif ($profit == 0) {
                $campaign_result = "Chiến dịch hòa vốn";
                $performance_level = "Trung bình";
                $result_class = "text-yellow-600";
            } else {
                $campaign_result = "Chiến dịch thất bại";
                $performance_level = "Kém";
                $result_class = "text-red-600";
            }

            echo "<p><strong>Kết quả:</strong> <span class='$result_class'>" . $campaign_result . "</span></p>";
            echo "<p><strong>Mức độ hiệu quả:</strong> <span class='$result_class'>" . $performance_level . "</span></p>";

            switch ($product_category) {
                case "Điện tử":
                    $category_message = "Sản phẩm Điện tử có tiềm năng lợi nhuận cao";
                    break;
                case "Thời trang":
                    $category_message = "Sản phẩm Thời trang có doanh thu ổn định";
                    break;
                case "Gia dụng":
                    $category_message = "Sản phẩm Gia dụng có tỷ lệ chuyển đổi tốt";
                    break;
                default:
                    $category_message = "Loại sản phẩm có tiềm năng phát triển";
            }

            echo "<p><strong>Nhận xét theo danh mục:</strong> " . $category_message . "</p>";
            echo "</div>";
            echo "</section>";
            // 5. PHÂN TÍCH CHI TIẾT ĐƠN HÀNG
            echo "<section class='mb-8'>";
            echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3'>Chi tiết đơn hàng</h2>";
            echo "<div class='bg-gray-50 p-4 rounded-lg'>";
            echo "<div class='table-container'>";
            echo "<table class='w-full border-collapse'>";
            echo "<thead><tr class='bg-blue-600 text-white'><th class='p-3'>Mã đơn hàng</th><th class='p-3'>Giá trị</th></tr></thead>";
            echo "<tbody>";
            foreach ($order_list as $order_id => $order_value) {
                echo "<tr class='hover:bg-gray-100'><td class='p-3'>" . htmlspecialchars($order_id) . "</td><td class='p-3'>$" . number_format($order_value, 2) . "</td></tr>";
            }
            echo "</tbody></table>";
            echo "</div>";

            $max_order_value = max($order_list);
            $min_order_value = min($order_list);
            $max_order_id = array_search($max_order_value, $order_list);
            $min_order_id = array_search($min_order_value, $order_list);

            echo "<h3 class='text-lg font-semibold mt-4 mb-2'>Phân tích đơn hàng:</h3>";
            echo "<p><strong>Đơn hàng giá trị cao nhất:</strong> " . htmlspecialchars($max_order_id) . " = $" . number_format($max_order_value, 2) . "</p>";
            echo "<p><strong>Đơn hàng giá trị thấp nhất:</strong> " . htmlspecialchars($min_order_id) . " = $" . number_format($min_order_value, 2) . "</p>";
            echo "</div>";
            echo "</section>";

            // 6. BÁO CÁO TỔNG KẾT
            echo "<section class='mb-8'>";
            echo "<h2 class='text-xl font-semibold text-gray-800 mb-4 border-l-4 border-blue-500 pl-3'>Báo cáo tổng kết</h2>";
            echo "<div class='bg-gray-50 p-4 rounded-lg'>";
            echo "<p>Chiến dịch <strong>" . htmlspecialchars($campaign_name) . "</strong> đã kết thúc với lợi nhuận: <strong>$" . number_format($profit, 2) . "</strong></p>";

            echo "<h3 class='text-lg font-semibold mt-4 mb-2'>Các chỉ số quan trọng:</h3>";
            $metrics = array(
                "Tổng doanh thu" => "$" . number_format($revenue, 2),
                "Chi phí hoa hồng" => "$" . number_format($commission_cost, 2),
                "Thuế VAT" => "$" . number_format($vat_cost, 2),
                "Lợi nhuận cuối cùng" => "$" . number_format($profit, 2),
                "Tỷ suất lợi nhuận" => number_format(($profit / $revenue) * 100, 2) . "%"
            );

            echo "<div class='grid grid-cols-1 sm:grid-cols-2 gap-4'>";
            foreach ($metrics as $key => $value) {
                echo "<div class='bg-white p-3 rounded-lg shadow-sm'><strong>" . htmlspecialchars($key) . ":</strong> " . $value . "</div>";
            }
            echo "</div>";
            echo "</div>";
            echo "</section>";



            ?>
        </div>
    </div>
</body>

</html>