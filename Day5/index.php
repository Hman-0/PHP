<?php
require_once 'includes/logger.php';
require_once 'includes/upload.php';

$message = '';
$messageType = '';

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $uploadResult = null;
    
    // Xử lý upload file nếu có
    if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = handleFileUpload($_FILES['evidence_file']);
    }
    
    // Tạo thông tin bổ sung cho log
    $additionalInfo = '';
    if ($uploadResult && $uploadResult['success']) {
        $additionalInfo = 'File đính kèm: ' . $uploadResult['filename'];
    }
    
    // Ghi log
    if (!empty($action)) {
        $logSuccess = writeLog($action, $additionalInfo);
        
        if ($logSuccess) {
            $message = 'Đã ghi nhật ký thành công!';
            if ($uploadResult && $uploadResult['success']) {
                $message .= ' ' . $uploadResult['message'];
            }
            $messageType = 'success';
        } else {
            $message = 'Có lỗi khi ghi nhật ký!';
            $messageType = 'error';
        }
    } else {
        $message = 'Vui lòng nhập mô tả hành động!';
        $messageType = 'error';
    }
    
    // Hiển thị lỗi upload nếu có
    if ($uploadResult && !$uploadResult['success'] && !empty($uploadResult['message'])) {
        $message .= ' Lỗi upload: ' . $uploadResult['message'];
        $messageType = 'error';
    }
}

include 'includes/header.php';
?>

<h2>📝 Ghi nhật ký hoạt động</h2>

<?php if (!empty($message)): ?>
    <div class="<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="action">Mô tả hành động:</label>
        <select name="action" id="action" required>
            <option value="">-- Chọn hành động --</option>
            <option value="Đăng nhập hệ thống">🔐 Đăng nhập hệ thống</option>
            <option value="Đăng xuất hệ thống">🚪 Đăng xuất hệ thống</option>
            <option value="Gửi biểu mẫu">📋 Gửi biểu mẫu</option>
            <option value="Tải file lên">📤 Tải file lên</option>
            <option value="Tải file xuống">📥 Tải file xuống</option>
            <option value="Xem báo cáo">📊 Xem báo cáo</option>
            <option value="Cập nhật thông tin">✏️ Cập nhật thông tin</option>
            <option value="Xóa dữ liệu">🗑️ Xóa dữ liệu</option>
            <option value="Đăng nhập thất bại">❌ Đăng nhập thất bại</option>
            <option value="Truy cập trái phép">⚠️ Truy cập trái phép</option>
            <option value="other">Khác...</option>
        </select>
    </div>
    
    <div class="form-group" id="custom-action" style="display: none;">
        <label for="custom_action">Mô tả hành động tùy chỉnh:</label>
        <textarea name="custom_action" id="custom_action" rows="3" placeholder="Nhập mô tả chi tiết hành động..."></textarea>
    </div>
    
    <div class="form-group">
        <label for="evidence_file">File minh chứng (tùy chọn):</label>
        <input type="file" name="evidence_file" id="evidence_file" accept=".jpg,.jpeg,.png,.pdf">
        <small>Chấp nhận: JPG, PNG, PDF. Tối đa 2MB.</small>
    </div>
    
    <button type="submit">💾 Ghi nhật ký</button>
</form>

<script>
document.getElementById('action').addEventListener('change', function() {
    const customActionDiv = document.getElementById('custom-action');
    const customActionInput = document.getElementById('custom_action');
    
    if (this.value === 'other') {
        customActionDiv.style.display = 'block';
        customActionInput.required = true;
    } else {
        customActionDiv.style.display = 'none';
        customActionInput.required = false;
        customActionInput.value = '';
    }
});

// Xử lý form submit để sử dụng custom action nếu được chọn
document.querySelector('form').addEventListener('submit', function(e) {
    const actionSelect = document.getElementById('action');
    const customAction = document.getElementById('custom_action');
    
    if (actionSelect.value === 'other' && customAction.value.trim()) {
        // Tạo hidden input để gửi custom action
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'action';
        hiddenInput.value = customAction.value.trim();
        this.appendChild(hiddenInput);
        
        // Disable select để không gửi "other"
        actionSelect.disabled = true;
    }
});
</script>

</div>
</body>
</html>