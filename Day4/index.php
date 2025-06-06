<?php
// Khởi tạo session để lưu trữ dữ liệu giao dịch
session_start();

// Sử dụng $_GLOBALS để lưu trữ cấu hình và biến toàn cục
$GLOBALS['exchange_rate'] = 1; // Tỷ giá (có thể mở rộng cho đa tiền tệ)
$GLOBALS['sensitive_keywords'] = ['nợ xấu', 'vay nóng', 'lãi suất cao', 'đòi nợ'];
$GLOBALS['total_income'] = 0;
$GLOBALS['total_expense'] = 0;

// Khởi tạo mảng giao dịch trong session nếu chưa có
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

// Biến để lưu thông báo lỗi riêng cho từng field
$field_errors = [];
$warnings = [];
$success_message = '';

// Xử lý dữ liệu khi form được submit (sử dụng $_POST và $_SERVER)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_transaction'])) {
    
    // Lấy dữ liệu từ $_POST
    $transaction_name = trim($_POST['transaction_name'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $transaction_type = $_POST['transaction_type'] ?? '';
    $note = trim($_POST['note'] ?? '');
    $transaction_date = trim($_POST['transaction_date'] ?? '');
    
    // Validation sử dụng Regular Expressions - lưu lỗi riêng cho từng field
    
    // 1. Kiểm tra tên giao dịch không chứa ký tự đặc biệt
    if (empty($transaction_name)) {
        $field_errors['transaction_name'] = 'Tên giao dịch không được để trống.';
    } elseif (!preg_match('/^[a-zA-ZÀ-ỹ0-9\s]+$/u', $transaction_name)) {
        $field_errors['transaction_name'] = 'Tên giao dịch không được chứa ký tự đặc biệt.';
    }
    
    // 2. Kiểm tra số tiền là số dương
    if (empty($amount)) {
        $field_errors['amount'] = 'Số tiền không được để trống.';
    } elseif (!preg_match('/^[0-9]+(\.[0-9]+)?$/', $amount)) {
        $field_errors['amount'] = 'Số tiền phải là số dương, không chứa ký tự chữ.';
    } elseif (floatval($amount) <= 0) {
        $field_errors['amount'] = 'Số tiền phải lớn hơn 0.';
    }
    
    // 3. Kiểm tra định dạng ngày dd/mm/yyyy
    if (empty($transaction_date)) {
        $field_errors['transaction_date'] = 'Ngày thực hiện không được để trống.';
    } elseif (!preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/([0-9]{4})$/', $transaction_date)) {
        $field_errors['transaction_date'] = 'Ngày thực hiện phải có định dạng dd/mm/yyyy.';
    } else {
        // Kiểm tra ngày có hợp lệ không
        $date_parts = explode('/', $transaction_date);
        if (!checkdate($date_parts[1], $date_parts[0], $date_parts[2])) {
            $field_errors['transaction_date'] = 'Ngày thực hiện không hợp lệ.';
        }
    }
    
    // Kiểm tra loại giao dịch
    if (empty($transaction_type) || !in_array($transaction_type, ['thu', 'chi'])) {
        $field_errors['transaction_type'] = 'Vui lòng chọn loại giao dịch (Thu hoặc Chi).';
    }
    
    // 4. Kiểm tra từ khóa nhạy cảm trong ghi chú (sử dụng $_GLOBALS)
    if (!empty($note)) {
        foreach ($GLOBALS['sensitive_keywords'] as $keyword) {
            if (stripos($note, $keyword) !== false) {
                $warnings[] = "Cảnh báo: Phát hiện từ khóa nhạy cảm '" . $keyword . "' trong ghi chú.";
            }
        }
    }
    
    // Nếu không có lỗi, lưu giao dịch vào session
    if (empty($field_errors)) {
        $transaction = [
            'id' => uniqid(), // Tạo ID duy nhất
            'name' => $transaction_name,
            'amount' => floatval($amount),
            'type' => $transaction_type,
            'note' => $note,
            'date' => $transaction_date,
            'created_at' => date('Y-m-d H:i:s') // Sử dụng thời gian hiện tại
        ];
        
        // Lưu vào $_SESSION
        $_SESSION['transactions'][] = $transaction;
        
        $success_message = 'Giao dịch đã được thêm thành công!';
        
        // Reset form data
        $_POST = [];
    }
}

// Tính toán tổng thu, chi từ session (cập nhật $_GLOBALS)
foreach ($_SESSION['transactions'] as $transaction) {
    if ($transaction['type'] === 'thu') {
        $GLOBALS['total_income'] += $transaction['amount'];
    } else {
        $GLOBALS['total_expense'] += $transaction['amount'];
    }
}

// Tính số dư
$balance = $GLOBALS['total_income'] - $GLOBALS['total_expense'];

// Xử lý xóa giao dịch (sử dụng $_GET)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    foreach ($_SESSION['transactions'] as $key => $transaction) {
        if ($transaction['id'] === $delete_id) {
            unset($_SESSION['transactions'][$key]);
            $_SESSION['transactions'] = array_values($_SESSION['transactions']); // Reindex array
            header('Location: ' . $_SERVER['PHP_SELF']); // Redirect để tránh resubmit
            exit;
        }
    }
}

// Lấy thông tin về trình duyệt và IP (sử dụng $_SERVER và $_ENV)
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$user_ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';

// Sử dụng $_COOKIE để lưu preferences (tùy chọn)
if (isset($_POST['save_preferences'])) {
    setcookie('preferred_currency', 'VND', time() + (86400 * 30)); // 30 days
}
$preferred_currency = $_COOKIE['preferred_currency'] ?? 'VND';

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css.css">
    <title>Quản Lý Giao Dịch Tài Chính</title>
   
</head>
<body>
    <div class="container">
        <h1>🏦 Hệ Thống Quản Lý Giao Dịch Tài Chính</h1>
        
      

        <!-- Hiển thị cảnh báo và thành công -->
        <?php if (!empty($warnings)): ?>
            <div class="warning">
                <strong>⚠️ Cảnh báo:</strong><br>
                <?php foreach ($warnings as $warning): ?>
                    • <?php echo htmlspecialchars($warning); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success">
                <strong>✅ Thành công:</strong> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <!-- Form nhập giao dịch (sử dụng $_SERVER['PHP_SELF']) -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="transactionForm">
            <h2>📝 Thêm Giao Dịch Mới</h2>
            
            <div class="form-group">
                <label for="transaction_name">Tên giao dịch *:</label>
                <input type="text" id="transaction_name" name="transaction_name" 
                       class="<?php echo isset($field_errors['transaction_name']) ? 'error' : ''; ?>"
                       value="<?php echo htmlspecialchars($_POST['transaction_name'] ?? ''); ?>"
                       placeholder="Ví dụ: Mua sắm, Lương tháng 12...">
                <?php if (isset($field_errors['transaction_name'])): ?>
                    <span class="field-error"> <?php echo htmlspecialchars($field_errors['transaction_name']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="amount">Số tiền (<?php echo $preferred_currency; ?>) *:</label>
                <input type="text" id="amount" name="amount" 
                       class="<?php echo isset($field_errors['amount']) ? 'error' : ''; ?>"
                       value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>"
                       placeholder="Ví dụ: 500000">
                <?php if (isset($field_errors['amount'])): ?>
                    <span class="field-error"> <?php echo htmlspecialchars($field_errors['amount']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Loại giao dịch *:</label>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" id="thu" name="transaction_type" value="thu" 
                               <?php echo (isset($_POST['transaction_type']) && $_POST['transaction_type'] === 'thu') ? 'checked' : ''; ?>>
                        <label for="thu">💰 Thu nhập</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="chi" name="transaction_type" value="chi"
                               <?php echo (isset($_POST['transaction_type']) && $_POST['transaction_type'] === 'chi') ? 'checked' : ''; ?>>
                        <label for="chi">💸 Chi tiêu</label>
                    </div>
                </div>
                <?php if (isset($field_errors['transaction_type'])): ?>
                    <span class="field-error"> <?php echo htmlspecialchars($field_errors['transaction_type']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="transaction_date">Ngày thực hiện (dd/mm/yyyy) *:</label>
                <input type="text" id="transaction_date" name="transaction_date" 
                       class="<?php echo isset($field_errors['transaction_date']) ? 'error' : ''; ?>"
                       value="<?php echo htmlspecialchars($_POST['transaction_date'] ?? date('d/m/Y')); ?>"
                       placeholder="<?php echo date('d/m/Y'); ?>">
                <?php if (isset($field_errors['transaction_date'])): ?>
                    <span class="field-error"> <?php echo htmlspecialchars($field_errors['transaction_date']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="note">Ghi chú (tùy chọn):</label>
                <textarea id="note" name="note" rows="3" 
                          placeholder="Thêm ghi chú về giao dịch..."><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea>
            </div>

            <button type="submit" name="submit_transaction">➕ Thêm Giao Dịch</button>
        </form>
    </div>

    <!-- Hiển thị thống kê và danh sách giao dịch -->
    <?php if (!empty($_SESSION['transactions'])): ?>
    <div class="container">
        <h2>📊 Thống Kê Tài Chính</h2>
        
        <div class="summary">
            <div class="summary-item">
                <h3>💰 Tổng Thu</h3>
                <div class="amount income"><?php echo number_format($GLOBALS['total_income'], 0, ',', '.'); ?> <?php echo $preferred_currency; ?></div>
            </div>
            <div class="summary-item">
                <h3>💸 Tổng Chi</h3>
                <div class="amount expense"><?php echo number_format($GLOBALS['total_expense'], 0, ',', '.'); ?> <?php echo $preferred_currency; ?></div>
            </div>
            <div class="summary-item">
                <h3>💳 Số Dư</h3>
                <div class="amount <?php echo $balance >= 0 ? 'income' : 'expense'; ?>">
                    <?php echo number_format($balance, 0, ',', '.'); ?> <?php echo $preferred_currency; ?>
                </div>
            </div>
        </div>

        <h2>📋 Danh Sách Giao Dịch</h2>
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên Giao Dịch</th>
                    <th>Số Tiền</th>
                    <th>Loại</th>
                    <th>Ngày</th>
                    <th>Ghi Chú</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['transactions'] as $index => $transaction): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($transaction['name']); ?></td>
                    <td class="<?php echo $transaction['type'] === 'thu' ? 'income' : 'expense'; ?>">
                        <?php echo ($transaction['type'] === 'thu' ? '+' : '-') . number_format($transaction['amount'], 0, ',', '.'); ?> <?php echo $preferred_currency; ?>
                    </td>
                    <td>
                        <span class="<?php echo $transaction['type'] === 'thu' ? 'income' : 'expense'; ?>">
                            <?php echo $transaction['type'] === 'thu' ? '💰 Thu' : '💸 Chi'; ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['note']); ?></td>
                    <td>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=<?php echo $transaction['id']; ?>" 
                           class="delete-btn" 
                           onclick="return confirm('Bạn có chắc muốn xóa giao dịch này?');">🗑️ Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="container">
        <div class="info-panel">
            <strong>📝 Chưa có giao dịch nào được thêm.</strong><br>
            Hãy sử dụng form bên trên để thêm giao dịch đầu tiên của bạn!
        </div>
    </div>
    <?php endif; ?>

    <!-- JavaScript chỉ để format input, không validation -->
    <script>
        // Auto-format ngày khi người dùng nhập
        document.getElementById('transaction_date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0,2) + '/' + value.substring(2);
            }
            if (value.length >= 5) {
                value = value.substring(0,5) + '/' + value.substring(5,9);
            }
            e.target.value = value;
        });
        
        // Auto-format số tiền
        document.getElementById('amount').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9.]/g, '');
            e.target.value = value;
        });
        
        // Xóa class error khi người dùng bắt đầu nhập lại
        document.querySelectorAll('input, textarea').forEach(function(element) {
            element.addEventListener('input', function() {
                this.classList.remove('error');
                const errorSpan = this.parentNode.querySelector('.field-error');
                if (errorSpan) {
                    errorSpan.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>