<?php
require_once 'data/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['option'])) {
    $option = $_POST['option'];
    
    // Cập nhật số lượng bình chọn
    $stmt = $pdo->prepare("UPDATE polls SET votes = votes + 1 WHERE option_name = ?");
    $stmt->execute([$option]);
    
    // Lấy kết quả bình chọn
    $stmt = $pdo->prepare("SELECT option_name, votes FROM polls");
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    // Tính tổng số bình chọn
    $totalVotes = 0;
    foreach ($results as $result) {
        $totalVotes += $result['votes'];
    }
    
    // Tính phần trăm cho mỗi lựa chọn
    $pollResults = [];
    foreach ($results as $result) {
        $percentage = ($totalVotes > 0) ? round(($result['votes'] / $totalVotes) * 100) : 0;
        $pollResults[] = [
            'option' => $result['option_name'],
            'votes' => $result['votes'],
            'percentage' => $percentage
        ];
    }
    
    echo json_encode([
        'success' => true,
        'results' => $pollResults,
        'totalVotes' => $totalVotes
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Yêu cầu không hợp lệ.'
    ]);
}
?>