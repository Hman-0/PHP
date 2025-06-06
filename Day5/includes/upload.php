<?php

function handleFileUpload($file) {
    $result = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];
    
    // Kiểm tra có file được upload không
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $result['message'] = 'Không có file nào được chọn.';
        return $result;
    }
    
    // Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'Có lỗi xảy ra khi upload file.';
        return $result;
    }
    
    // Tạo thư mục uploads nếu chưa tồn tại
    $uploadDir = __DIR__ . '/../uploads';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Kiểm tra kích thước file (tối đa 2MB)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        $result['message'] = 'File quá lớn. Kích thước tối đa là 2MB.';
        return $result;
    }
    
    // Kiểm tra định dạng file
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        $result['message'] = 'Định dạng file không hợp lệ. Chỉ chấp nhận: ' . implode(', ', $allowedExtensions);
        return $result;
    }
    
    // Tạo tên file mới với timestamp để tránh trùng
    $timestamp = time();
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $newFileName = 'upload_' . $timestamp . '_' . $originalName . '.' . $fileExtension;
    $uploadPath = $uploadDir . '/' . $newFileName;
    
    // Di chuyển file đến thư mục uploads
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $result['success'] = true;
        $result['message'] = 'Upload file thành công!';
        $result['filename'] = $newFileName;
    } else {
        $result['message'] = 'Không thể lưu file. Vui lòng thử lại.';
    }
    
    return $result;
}


function getUploadedFiles() {
    $uploadDir = __DIR__ . '/../uploads';
    $files = [];
    
    if (is_dir($uploadDir)) {
        $fileList = scandir($uploadDir);
        foreach ($fileList as $file) {
            if ($file !== '.' && $file !== '..' && is_file($uploadDir . '/' . $file)) {
                $files[] = [
                    'name' => $file,
                    'size' => filesize($uploadDir . '/' . $file),
                    'date' => date('Y-m-d H:i:s', filemtime($uploadDir . '/' . $file))
                ];
            }
        }
    }
    
    return $files;
}


function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $unitIndex = 0;
    
    while ($size >= 1024 && $unitIndex < count($units) - 1) {
        $size /= 1024;
        $unitIndex++;
    }
    
    return round($size, 2) . ' ' . $units[$unitIndex];
}
?>