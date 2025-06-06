<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Nhật ký Hoạt động</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .success { color: green; background-color: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: red; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .log-entry { background-color: #f8f9fa; padding: 10px; margin: 5px 0; border-left: 4px solid #007bff; }
        .log-important { border-left-color: #dc3545; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; text-decoration: none; color: #007bff; }
        .nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Hệ thống Nhật ký Hoạt động</h1>
        <div class="nav">
            <a href="index.php">📝 Ghi nhật ký</a>
            <a href="view_log.php">📋 Xem nhật ký</a>
        </div>