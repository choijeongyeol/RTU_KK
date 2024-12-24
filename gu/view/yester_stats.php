<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

// 로그 파일에 기록하는 함수
function log_to_file($message) {
    $log_file = $_SERVER['DOCUMENT_ROOT'].'/gu/logs/stats_log.txt';
    $result = file_put_contents($log_file, date("Y-m-d H:i:s") . " - " . $message . "\n", FILE_APPEND | LOCK_EX);
    if ($result === false) {
        error_log("Failed to write log: " . $message);
    }
}

// DB 연결
$conn = getDbConnection();
if (!$conn) {
    log_to_file("Database connection failed");
    exit("DB connection error");
}

// 어제의 통계를 계산하는 함수
function getYesterdayStats($conn) {
    try {
        $query = "
            SELECT 
                energy_source,
                COUNT(*) AS total_entries,
                MAX(cs) AS max_cs,
                MIN(cs) AS min_cs,
                AVG(cs) AS avg_cs,
                SUM(production_time) AS total_production_time,    -- 총 발전 시간
                SUM(production_amount) AS total_production_amount -- 총 발전량
            FROM RTU_real_time_stats
            WHERE timestamp >= CURDATE() - INTERVAL 1 DAY AND timestamp < CURDATE()  + INTERVAL 1 DAY
            GROUP BY energy_source;
        ";
		//            WHERE timestamp >= CURDATE() - INTERVAL 1 DAY AND timestamp < CURDATE()

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        log_to_file("Query failed: " . $e->getMessage());
        return false;
    }
}

// 통계 데이터를 JSON 형식으로 반환하는 API
header('Content-Type: application/json');
$stats = getYesterdayStats($conn);

if ($stats !== false) {
    echo json_encode([
        'status' => 'success',
        'data' => $stats
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve stats.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
