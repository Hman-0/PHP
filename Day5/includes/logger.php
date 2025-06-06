<?php

function writeLog($action, $additional_info = '') {
    // Tạo thư mục logs nếu chưa tồn tại
    $logDir = __DIR__ . '/../logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    // Tạo tên file log theo ngày hiện tại
    $currentDate = date('Y-m-d');
    $logFile = $logDir . '/log_' . $currentDate . '.txt';
    
    // Lấy thông tin thời gian và IP
    $timestamp = date('Y-m-d H:i:s');
    $userIP = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    // Tạo nội dung log
    $logContent = "[{$timestamp}] IP: {$userIP} | Hành động: {$action}";
    if (!empty($additional_info)) {
        $logContent .= " | Thông tin: {$additional_info}";
    }
    $logContent .= "\n";
    
    // Ghi vào file (thêm vào cuối file)
    $result = file_put_contents($logFile, $logContent, FILE_APPEND | LOCK_EX);
    
    return $result !== false;
}


function readLog($date) {
    $logDir = __DIR__ . '/../logs';
    $logFile = $logDir . '/log_' . $date . '.txt';
    
    if (!file_exists($logFile)) {
        return false;
    }
    
    $logs = [];
    $handle = fopen($logFile, 'r');
    
    if ($handle) {
        while (!feof($handle)) {
            $line = fgets($handle);
            if (trim($line) !== '') {
                $logs[] = trim($line);
            }
        }
        fclose($handle);
    }
    
    return $logs;
}


function getAvailableLogDates() {
    $logDir = __DIR__ . '/../logs';
    $dates = [];
    
    if (is_dir($logDir)) {
        $files = scandir($logDir);
        foreach ($files as $file) {
            if (preg_match('/^log_(\d{4}-\d{2}-\d{2})\.txt$/', $file, $matches)) {
                $dates[] = $matches[1];
            }
        }
        rsort($dates); // Sắp xếp ngày mới nhất trước
    }
    
    return $dates;
}


function isImportantAction($action) {
    $importantKeywords = ['thất bại', 'lỗi', 'error', 'failed', 'unauthorized', 'hack', 'attack'];
    $actionLower = strtolower($action);
    
    foreach ($importantKeywords as $keyword) {
        if (strpos($actionLower, $keyword) !== false) {
            return true;
        }
    }
    
    return false;
}
?>