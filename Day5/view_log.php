<?php
require_once 'includes/logger.php';

$selectedDate = $_GET['date'] ?? date('Y-m-d');
$searchKeyword = $_GET['search'] ?? '';
$logs = [];
$availableDates = getAvailableLogDates();

// Đọc log theo ngày được chọn
if (!empty($selectedDate)) {
    $logs = readLog($selectedDate);
    
    // Lọc theo từ khóa nếu có
    if (!empty($searchKeyword) && $logs) {
        $logs = array_filter($logs, function($log) use ($searchKeyword) {
            return stripos($log, $searchKeyword) !== false;
        });
    }
}

include 'includes/header.php';
?>

<h2>📋 Xem nhật ký hoạt động</h2>

<form method="GET" style="margin-bottom: 20px;">
    <div style="display: flex; gap: 10px; align-items: end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 200px;">
            <label for="date">Chọn ngày:</label>
            <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($selectedDate); ?>" max="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <div class="form-group" style="flex: 1; min-width: 200px;">
            <label for="search">Tìm kiếm từ khóa:</label>
            <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($searchKeyword); ?>" placeholder="Nhập từ khóa...">
        </div>
        
        <div class="form-group">
            <button type="submit">🔍 Xem log</button>
        </div>
    </div>
</form>

<?php if (!empty($availableDates)): ?>
    <div style="margin-bottom: 20px;">
        <strong>Ngày có sẵn log:</strong>
        <?php foreach (array_slice($availableDates, 0, 10) as $date): ?>
            <a href="?date=<?php echo $date; ?>" style="margin-right: 10px; <?php echo $date === $selectedDate ? 'font-weight: bold; color: #007bff;' : ''; ?>">
                <?php echo $date; ?>
            </a>
        <?php endforeach; ?>
        <?php if (count($availableDates) > 10): ?>
            <span>... và <?php echo count($availableDates) - 10; ?> ngày khác</span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($logs === false): ?>
    <div class="error">
        ❌ Không có nhật ký cho ngày <?php echo htmlspecialchars($selectedDate); ?>.
    </div>
<?php elseif (empty($logs)): ?>
    <?php if (!empty($searchKeyword)): ?>
        <div class="error">
            🔍 Không tìm thấy kết quả nào cho từ khóa "<?php echo htmlspecialchars($searchKeyword); ?>" trong ngày <?php echo htmlspecialchars($selectedDate); ?>.
        </div>
    <?php else: ?>
        <div class="error">
            📝 Chưa có hoạt động nào được ghi nhận trong ngày <?php echo htmlspecialchars($selectedDate); ?>.
        </div>
    <?php endif; ?>
<?php else: ?>
    <h3>📊 Nhật ký ngày <?php echo htmlspecialchars($selectedDate); ?></h3>
    <?php if (!empty($searchKeyword)): ?>
        <p><strong>Kết quả tìm kiếm cho:</strong> "<?php echo htmlspecialchars($searchKeyword); ?>" (<?php echo count($logs); ?> kết quả)</p>
    <?php endif; ?>
    
    <div style="margin-bottom: 10px;">
        <strong>Tổng số hoạt động:</strong> <?php echo count($logs); ?>
    </div>
    
    <?php foreach ($logs as $index => $log): ?>
        <?php $isImportant = isImportantAction($log); ?>
        <div class="log-entry <?php echo $isImportant ? 'log-important' : ''; ?>">
            <strong>#<?php echo $index + 1; ?></strong>
            <?php if ($isImportant): ?>
                <span style="color: #dc3545; font-weight: bold;">⚠️ QUAN TRỌNG</span>
            <?php endif; ?>
            <br>
            <?php 
            // Highlight từ khóa tìm kiếm
            $displayLog = htmlspecialchars($log);
            if (!empty($searchKeyword)) {
                $displayLog = preg_replace('/(' . preg_quote($searchKeyword, '/') . ')/i', '<mark>$1</mark>', $displayLog);
            }
            echo $displayLog;
            ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
    <h3>📈 Thống kê</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div style="background: #e3f2fd; padding: 15px; border-radius: 8px;">
            <strong>📅 Tổng số ngày có log:</strong><br>
            <?php echo count($availableDates); ?> ngày
        </div>
        <div style="background: #f3e5f5; padding: 15px; border-radius: 8px;">
            <strong>📝 Hoạt động hôm nay:</strong><br>
            <?php 
            $todayLogs = readLog(date('Y-m-d'));
            echo $todayLogs ? count($todayLogs) : 0;
            ?> hoạt động
        </div>
        <div style="background: #e8f5e8; padding: 15px; border-radius: 8px;">
            <strong>📂 File uploads:</strong><br>
            <?php 
            $uploadDir = __DIR__ . '/uploads';
            $uploadCount = 0;
            if (is_dir($uploadDir)) {
                $files = scandir($uploadDir);
                $uploadCount = count($files) - 2; // Trừ . và ..
            }
            echo max(0, $uploadCount);
            ?> file
        </div>
    </div>
</div>

</div>
</body>
</html>