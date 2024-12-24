<?php
// 데이터베이스 연결 정보
$servername = "localhost";
$username = "myhanis";
$password = "Hanis123!";
$dbname = "RTU";

$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 체크
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 트리거 백업 파일 생성
$backupFile = "trigger_backup_" . date('Ymd_His') . ".sql";
$file = fopen($backupFile, "w");

if (!$file) {
    die("Failed to create backup file.");
}

// 데이터베이스의 모든 테이블 검색
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // 각 테이블에 대한 처리
    while ($row = $result->fetch_array()) {
        $table = $row[0];

        // 테이블에 대한 트리거 검색
        $triggerSql = "SHOW TRIGGERS LIKE '$table'";
        $triggerResult = $conn->query($triggerSql);

        if ($triggerResult->num_rows > 0) {
            while ($triggerRow = $triggerResult->fetch_assoc()) {
                $trigger_name = $triggerRow['Trigger'];
                $trigger_event = $triggerRow['Event'];
                $trigger_timing = $triggerRow['Timing'];
                $trigger_statement = $triggerRow['Statement'];

                // 트리거 백업 SQL 형식으로 생성
                $backupTriggerSQL = "DELIMITER //\n";
                $backupTriggerSQL .= "CREATE TRIGGER `$trigger_name` $trigger_timing $trigger_event ON `$table` FOR EACH ROW $trigger_statement //\n";
                $backupTriggerSQL .= "DELIMITER ;\n\n";

                // 백업 파일에 작성
                fwrite($file, $backupTriggerSQL);
            }
        }
    }
    echo "Trigger backup completed successfully. File: $backupFile<br>";
} else {
    echo "No tables found in the database.<br>";
}

// 파일 닫기
fclose($file);

// MySQL 연결 종료
$conn->close();
?>
