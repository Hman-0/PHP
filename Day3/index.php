<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Chấm Công và Tính Lương</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php
    // Dữ liệu đầu vào cho danh sách nhân viên
    $employees = [
        ['id' => 101, 'name' => 'Nguyễn Văn A', 'base_salary' => 5000000],
        ['id' => 102, 'name' => 'Trần Thị B', 'base_salary' => 6000000],
        ['id' => 103, 'name' => 'Lê Văn C', 'base_salary' => 5500000],
    ];

    // Dữ liệu chấm công của nhân viên
    $timesheet = [
        101 => ['2025-03-01', '2025-03-02', '2025-03-04', '2025-03-05'],
        102 => ['2025-03-01', '2025-03-03', '2025-03-04'],
        103 => ['2025-03-02', '2025-03-03', '2025-03-04', '2025-03-05', '2025-03-06'],
    ];

    // Dữ liệu phụ cấp và khấu trừ
    $adjustments = [
        101 => ['allowance' => 500000, 'deduction' => 200000],
        102 => ['allowance' => 300000, 'deduction' => 100000],
        103 => ['allowance' => 400000, 'deduction' => 150000],
    ];

    // Hằng số: Số ngày công chuẩn trong tháng
    const STANDARD_WORKING_DAYS = 22;

    // Hàm tính số ngày công thực tế của từng nhân viên
    function calculateWorkingDays($timesheet) {
        return array_map(function($days) {
            return count($days); 
        }, $timesheet);
    }

    // Hàm tính lương thực lĩnh cho từng nhân viên
    function calculateNetSalary($employees, $timesheet, $adjustments) {
        $employeeIds = array_column($employees, 'id'); // Lấy danh sách ID nhân viên
        $baseSalaries = array_column($employees, 'base_salary', 'id'); // Tạo mapping id => lương cơ bản
        
        return array_map(function($empId) use ($baseSalaries, $timesheet, $adjustments) {
            $workingDays = count($timesheet[$empId]); // Số ngày công thực tế
            $dailySalary = $baseSalaries[$empId] / STANDARD_WORKING_DAYS; // Lương ngày
            $basicPay = $dailySalary * $workingDays; // Lương cơ bản theo ngày công
            $allowance = $adjustments[$empId]['allowance']; // Phụ cấp
            $deduction = $adjustments[$empId]['deduction']; // Khấu trừ
            
            return round($basicPay + $allowance - $deduction); // Lương thực lĩnh sau khi cộng phụ cấp và trừ khấu trừ
        }, $employeeIds);
    }

    // Hàm tạo bảng lương tổng hợp
    function generatePayrollTable($employees, $timesheet, $adjustments) {
        $payrollData = [];
        
        foreach ($employees as $employee) {
            $empId = $employee['id'];
            $name = $employee['name'];
            $baseSalary = $employee['base_salary'];
            
            // Kiểm tra xem nhân viên có dữ liệu chấm công hay không
            if (array_key_exists($empId, $timesheet)) {
                $workingDays = count($timesheet[$empId]); // Số ngày công thực tế
                $allowance = $adjustments[$empId]['allowance']; // Phụ cấp
                $deduction = $adjustments[$empId]['deduction']; // Khấu trừ
                
                $dailySalary = $baseSalary / STANDARD_WORKING_DAYS; // Lương ngày
                $netSalary = round($dailySalary * $workingDays + $allowance - $deduction); // Lương thực lĩnh
                
                // Tạo mảng dữ liệu cho bảng lương
                $payrollData[] = compact('empId', 'name', 'workingDays', 'baseSalary', 'allowance', 'deduction', 'netSalary');
            }
        }
        
        return $payrollData;
    }

    // Hàm tìm nhân viên có số ngày công cao nhất và thấp nhất
    function findMinMaxWorkingDays($employees, $timesheet) {
        $workingDaysData = [];
        
        foreach ($employees as $employee) {
            $empId = $employee['id'];
            $workingDays = count($timesheet[$empId]); // Số ngày công của nhân viên
            $workingDaysData[] = [
                'id' => $empId,
                'name' => $employee['name'],
                'days' => $workingDays
            ];
        }
        
        // Sắp xếp theo số ngày công từ thấp đến cao
        usort($workingDaysData, function($a, $b) {
            return $a['days'] <=> $b['days'];
        });
        
        $minWorker = $workingDaysData[0]; // Nhân viên có ít ngày công nhất
        $maxWorker = end($workingDaysData); // Nhân viên có nhiều ngày công nhất
        
        return ['min' => $minWorker, 'max' => $maxWorker];
    }

    // Hàm cập nhật danh sách nhân viên
    function updateEmployeeData($employees, $newEmployees) {
        return array_merge($employees, $newEmployees); // Gộp danh sách nhân viên mới vào danh sách hiện tại
    }

    // Hàm cập nhật dữ liệu chấm công
    function updateTimesheet($timesheet, $empId, $action, $date = null) {
        if (!array_key_exists($empId, $timesheet)) {
            $timesheet[$empId] = []; // Khởi tạo mảng chấm công nếu chưa tồn tại
        }
        
        switch ($action) {
            case 'add_end':
                if ($date) array_push($timesheet[$empId], $date); // Thêm ngày công vào cuối
                break;
            case 'add_start':
                if ($date) array_unshift($timesheet[$empId], $date); // Thêm ngày công vào đầu
                break;
            case 'remove_end':
                array_pop($timesheet[$empId]); // Xóa ngày công ở cuối
                break;
            case 'remove_start':
                array_shift($timesheet[$empId]); // Xóa ngày công ở đầu
                break;
        }
        
        return $timesheet;
    }

    // Hàm lọc nhân viên theo số ngày công tối thiểu
    function filterEmployeesByWorkingDays($employees, $timesheet, $minDays = 4) {
        return array_filter($employees, function($employee) use ($timesheet, $minDays) {
            $empId = $employee['id'];
            return array_key_exists($empId, $timesheet) && count($timesheet[$empId]) >= $minDays; // Lọc nhân viên có ít nhất $minDays ngày công
        });
    }

    // Hàm kiểm tra nhân viên có đi làm vào một ngày cụ thể hay không
    function checkEmployeeWorkedOnDate($empId, $date, $timesheet) {
        if (!array_key_exists($empId, $timesheet)) {
            return false; // Nhân viên không có dữ liệu chấm công
        }
        return in_array($date, $timesheet[$empId]); // Kiểm tra ngày có trong danh sách chấm công
    }

    // Hàm kiểm tra xem nhân viên có dữ liệu phụ cấp/khấu trừ hay không
    function checkAdjustmentExists($empId, $adjustments) {
        return array_key_exists($empId, $adjustments); // Kiểm tra sự tồn tại của dữ liệu phụ cấp
    }

    // Hàm làm sạch dữ liệu chấm công, loại bỏ ngày trùng lặp
    function cleanDuplicateTimesheet($timesheet) {
        return array_map(function($dates) {
            return array_unique($dates); // Loại bỏ các ngày trùng lặp
        }, $timesheet);
    }

    // Hàm tính tổng quỹ lương
    function getTotalSalary($payrollData) {
        return array_sum(array_column($payrollData, 'netSalary')); // Tính tổng lương thực lĩnh
    }

    // Hàm định dạng tiền tệ
    function formatCurrency($amount) {
        return number_format($amount, 0, ',', '.') . ' VND'; // Định dạng số tiền với đơn vị VND
    }

    // Xử lý dữ liệu
    $workingDays = calculateWorkingDays($timesheet); // Tính ngày công
    $cleanTimesheet = cleanDuplicateTimesheet($timesheet); // Làm sạch dữ liệu chấm công
    $payrollData = generatePayrollTable($employees, $cleanTimesheet, $adjustments); // Tạo bảng lương
    $totalSalary = getTotalSalary($payrollData); // Tính tổng quỹ lương
    $minMaxWorkers = findMinMaxWorkingDays($employees, $timesheet); // Tìm nhân viên có ngày công min/max
    $qualifiedEmployees = filterEmployeesByWorkingDays($employees, $timesheet, 4); // Lọc nhân viên đủ điều kiện
    $check1 = checkEmployeeWorkedOnDate(102, '2025-03-03', $timesheet); // Kiểm tra ngày làm việc của nhân viên 102
    $check2 = checkAdjustmentExists(101, $adjustments); // Kiểm tra phụ cấp của nhân viên 101
    $newEmployees = [['id' => 104, 'name' => 'Phạm Thị D', 'base_salary' => 4500000]]; // Nhân viên mới
    $updatedEmployees = updateEmployeeData($employees, $newEmployees); // Cập nhật danh sách nhân viên
    $updatedTimesheet = updateTimesheet($timesheet, 101, 'add_end', '2025-03-07'); // Cập nhật chấm công

    function generatePayrollReport($employees, $timesheet, $adjustments) {
        $report = [];
        
        foreach ($employees as $employee) {
            // Get employee basic info
            $empKeys = array_keys($employee);
            $empValues = array_values($employee);
            $empData = array_combine($empKeys, $empValues);
            
            $empId = $empData['id'];
            $name = $empData['name'];
            $baseSalary = $empData['base_salary'];
            
            // Calculate working days and salary components
            if (array_key_exists($empId, $timesheet)) {
                $workingDays = count($timesheet[$empId]);
                $allowance = $adjustments[$empId]['allowance'];
                $deduction = $adjustments[$empId]['deduction'];
                
                // Calculate net salary
                $dailySalary = $baseSalary / STANDARD_WORKING_DAYS;
                $netSalary = round($dailySalary * $workingDays + $allowance - $deduction);
                
                // Create report entry using compact()
                $report[] = compact(
                    'name',
                    'workingDays',
                    'baseSalary', 
                    'allowance',
                    'deduction',
                    'netSalary'
                );
            }
        }
        
        return $report;
    }

    // Example usage:
    $payrollReport = generatePayrollReport($employees, $timesheet, $adjustments);


    ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Tiêu đề chính -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Hệ Thống Chấm Công và Tính Lương Nhân Viên</h1>

        <!-- Phần 1: Ngày công thực tế -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">1. Ngày Công Thực Tế</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($workingDays as $empId => $days): ?>
                    <?php $empName = array_column($employees, 'name', 'id')[$empId]; ?>
                    <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                        <p class="text-gray-600"><span class="font-medium"><?php echo htmlspecialchars($empName); ?>:</span> <?php echo $days; ?> ngày</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Phần 2: Bảng lương -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">2. Bảng Lương Tháng 03/2025</h2>
            <div class="table-container">
                <table class="min-w-full bg-white border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-2 px-4 border">Mã NV</th>
                            <th class="py-2 px-4 border">Họ tên</th>
                            <th class="py-2 px-4 border">Ngày công</th>
                            <th class="py-2 px-4 border">Lương cơ bản</th>
                            <th class="py-2 px-4 border">Phụ cấp</th>
                            <th class="py-2 px-4 border">Khấu trừ</th>
                            <th class="py-2 px-4 border">Lương thực lĩnh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payrollData as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['empId']); ?></td>
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['workingDays']); ?></td>
                                <td class="py-2 px-4 border"><?php echo formatCurrency($row['baseSalary']); ?></td>
                                <td class="py-2 px-4 border"><?php echo formatCurrency($row['allowance']); ?></td>
                                <td class="py-2 px-4 border"><?php echo formatCurrency($row['deduction']); ?></td>
                                <td class="py-2 px-4 border"><?php echo formatCurrency($row['netSalary']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-4 text-lg font-semibold text-gray-700">Tổng quỹ lương: <?php echo formatCurrency($totalSalary); ?></p>
        </div>

        <!-- Phần 3: Thống kê ngày công -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">3. Thống Kê Ngày Công</h2>
            <div class="space-y-2">
                <p class="text-gray-600">Nhân viên làm nhiều nhất: <span class="font-medium"><?php echo htmlspecialchars($minMaxWorkers['max']['name']); ?></span> (<?php echo $minMaxWorkers['max']['days']; ?> ngày công)</p>
                <p class="text-gray-600">Nhân viên làm ít nhất: <span class="font-medium"><?php echo htmlspecialchars($minMaxWorkers['min']['name']); ?></span> (<?php echo $minMaxWorkers['min']['days']; ?> ngày công)</p>
            </div>
        </div>

        <!-- Phần 4: Nhân viên đủ điều kiện xét thưởng -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">4. Danh Sách Nhân Viên Đủ Điều Kiện Xét Thưởng (>= 4 ngày công)</h2>
            <ul class="list-disc list-inside space-y-2">
                <?php foreach ($qualifiedEmployees as $employee): ?>
                    <?php $workingDays = count($timesheet[$employee['id']]); ?>
                    <li class="text-gray-600"><?php echo htmlspecialchars($employee['name']); ?> (<?php echo $workingDays; ?> ngày công)</li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Phần 5: Kiểm tra điều kiện -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">5. Kiểm Tra Điều Kiện</h2>
            <div class="space-y-2">
                <p class="text-gray-600">Trần Thị B có đi làm vào ngày 2025-03-03: <span class="font-medium"><?php echo $check1 ? 'Có' : 'Không'; ?></span></p>
                <p class="text-gray-600">Thông tin phụ cấp của nhân viên 101 tồn tại: <span class="font-medium"><?php echo $check2 ? 'Có' : 'Không'; ?></span></p>
            </div>
        </div>

    
    </div>
</body>
</html>