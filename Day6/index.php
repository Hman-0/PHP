<?php
session_start();

// Custom Exception class
class CartException extends Exception {}

// Hàm lọc dữ liệu đầu vào
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Định nghĩa danh sách sách mẫu
$books = [
    ['title' => 'Clean Code', 'price' => 150000],
    ['title' => 'Design Patterns', 'price' => 200000],
    ['title' => 'Refactoring', 'price' => 180000]
];

// Mảng lưu trữ lỗi
$errors = [];

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    try {
        // Lọc và xác thực dữ liệu đầu vào
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors['email'] = "Email không hợp lệ!";
            file_put_contents('log.txt', date('Y-m-d H:i:s') . " - Email không hợp lệ: " . htmlspecialchars($_POST['email']) . "\n", FILE_APPEND);
        }

        $phone = filter_input(INPUT_POST, 'phone', FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[0-9]{10,11}$/']
        ]);
        if (!$phone) {
            $errors['phone'] = "Số điện thoại không hợp lệ!";
            file_put_contents('log.txt', date('Y-m-d H:i:s') . " - Số điện thoại không hợp lệ: " . htmlspecialchars($_POST['phone']) . "\n", FILE_APPEND);
        }

        $address = filter_input(INPUT_POST, 'address', FILTER_CALLBACK, [
            'options' => 'sanitizeInput'
        ]);
        if (empty($address)) {
            $errors['address'] = "Địa chỉ không được để trống!";
            file_put_contents('log.txt', date('Y-m-d H:i:s') . " - Địa chỉ không được để trống\n", FILE_APPEND);
        }

        $book_title = filter_input(INPUT_POST, 'book', FILTER_CALLBACK, [
            'options' => 'sanitizeInput'
        ]);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        // Kiểm tra xem sách có tồn tại trong danh sách không
        $valid_book = false;
        $book_price = 0;
        foreach ($books as $book) {
            if ($book['title'] === $book_title) {
                $valid_book = true;
                $book_price = $book['price'];
                break;
            }
        }

        if (!$valid_book || !$quantity) {
            $errors['book'] = "Sách hoặc số lượng không hợp lệ!";
            file_put_contents('log.txt', date('Y-m-d H:i:s') . " - Sách hoặc số lượng không hợp lệ: Book=" . htmlspecialchars($_POST['book']) . ", Quantity=" . htmlspecialchars($_POST['quantity']) . "\n", FILE_APPEND);
        }

        // Nếu không có lỗi, tiếp tục xử lý
        if (empty($errors)) {
            // Lưu email vào cookie
            setcookie('customer_email', $email, time() + (7 * 24 * 60 * 60), "/");

            // Khởi tạo giỏ hàng nếu chưa có
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Cộng dồn số lượng nếu sách đã tồn tại
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['title'] === $book_title) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $_SESSION['cart'][] = [
                    'title' => $book_title,
                    'quantity' => $quantity,
                    'price' => $book_price
                ];
            }

            // Lưu thông tin khách hàng
            $_SESSION['customer'] = [
                'email' => $email,
                'phone' => $phone,
                'address' => $address
            ];

            // Lưu giỏ hàng vào file JSON
            $cart_data = [
                'customer_email' => $email,
                'products' => $_SESSION['cart'],
                'total_amount' => array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $_SESSION['cart'])),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $json_data = json_encode($cart_data, JSON_PRETTY_PRINT);
            if (!file_put_contents('cart_data.json', $json_data)) {
                $errors['general'] = "Lỗi khi ghi file JSON!";
                file_put_contents('log.txt', date('Y-m-d H:i:s') . " - Lỗi khi ghi file JSON\n", FILE_APPEND);
            }
        }

    } catch (CartException $e) {
        $errors['general'] = $e->getMessage();
        file_put_contents('log.txt', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

// Xử lý xóa giỏ hàng
if (isset($_POST['clear_cart'])) {
    try {
        unset($_SESSION['cart']);
        unset($_SESSION['customer']);
        if (file_exists('cart_data.json') && !unlink('cart_data.json')) {
            $errors['general'] = "Lỗi khi xóa file JSON!";
            file_put_contents('log.txt', date('Y-m-d H:i:s') . " - Lỗi khi xóa file JSON\n", FILE_APPEND);
        }
    } catch (CartException $e) {
        $errors['general'] = $e->getMessage();
        file_put_contents('log.txt', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng Sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Giỏ Hàng Sách</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
        <?php endif; ?>

        <!-- Form thêm sách -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" 
                       value="<?php echo isset($_SESSION['customer']['email']) ? htmlspecialchars($_SESSION['customer']['email']) : (isset($_COOKIE['customer_email']) ? htmlspecialchars($_COOKIE['customer_email']) : ''); ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" 
                       value="<?php echo isset($_SESSION['customer']['phone']) ? htmlspecialchars($_SESSION['customer']['phone']) : ''; ?>">
                <?php if (isset($errors['phone'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['phone']); ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ giao hàng</label>
                <input type="text" class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" id="address" name="address" 
                       value="<?php echo isset($_SESSION['customer']['address']) ? htmlspecialchars($_SESSION['customer']['address']) : ''; ?>">
                <?php if (isset($errors['address'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['address']); ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="book" class="form-label">Chọn sách</label>
                <select class="form-select <?php echo isset($errors['book']) ? 'is-invalid' : ''; ?>" id="book" name="book">
                    <?php foreach ($books as $book): ?>
                        <option value="<?php echo htmlspecialchars($book['title']); ?>">
                            <?php echo htmlspecialchars($book['title']) . " - " . number_format($book['price']) . " VNĐ"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['book'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['book']); ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Số lượng</label>
                <input type="number" class="form-control <?php echo isset($errors['book']) ? 'is-invalid' : ''; ?>" id="quantity" name="quantity" min="1" value="1">
                <?php if (isset($errors['book'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['book']); ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" name="add_to_cart" class="btn btn-primary">Thêm vào giỏ hàng</button>
            <button type="submit" name="clear_cart" class="btn btn-danger">Xóa giỏ hàng</button>
        </form>

        <!-- Hiển thị giỏ hàng -->
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <h2>Thông tin đơn hàng</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên sách</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo number_format($item['price']); ?> VNĐ</td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($subtotal); ?> VNĐ</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Tổng tiền:</strong></td>
                        <td><strong><?php echo number_format($total); ?> VNĐ</strong></td>
                    </tr>
                </tbody>
            </table>

            <!-- Thông tin khách hàng -->
            <?php if (isset($_SESSION['customer'])): ?>
                <h3>Thông tin khách hàng</h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['customer']['email']); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($_SESSION['customer']['phone']); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($_SESSION['customer']['address']); ?></p>
                <p><strong>Thời gian đặt hàng:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>