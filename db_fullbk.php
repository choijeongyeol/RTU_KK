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

// 백업 파일 생성
$backupFile = "/var/www/html/backup/full_backup_" . date('Ymd_His') . ".sql";
$file = fopen($backupFile, "w");

if (!$file) {
    die("Failed to create backup file. Please check the file path and permissions.");
}

// 1. 데이터베이스 내 모든 테이블의 구조와 데이터를 백업
$tables = array();
$sql = "SHOW TABLES";
$result = $conn->query($sql);

while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    $createTableResult = $conn->query("SHOW CREATE TABLE `$table`");
    $createTableRow = $createTableResult->fetch_assoc();

    // 테이블 생성 SQL 추가
    fwrite($file, "-- Table structure for `$table`\n");
    fwrite($file, $createTableRow['Create Table'] . ";\n\n");

    // 테이블 데이터 백업
    $selectData = $conn->query("SELECT * FROM `$table`");
    if ($selectData->num_rows > 0) {
        fwrite($file, "-- Data for table `$table`\n");
        while ($row = $selectData->fetch_assoc()) {
            $values = array_map(array($conn, 'real_escape_string'), array_values($row));
            $values = "'" . implode("','", $values) . "'";
            $columns = implode(",", array_keys($row));

            $insertSQL = "INSERT INTO `$table` ($columns) VALUES ($values);";
            fwrite($file, $insertSQL . "\n");
        }
        fwrite($file, "\n\n");
    }
}

// 2. 트리거 백업
foreach ($tables as $table) {
    // 테이블에 대한 트리거 검색
    $triggerSql = "SHOW TRIGGERS LIKE '$table'";
    $triggerResult = $conn->query($triggerSql);

    if ($triggerResult->num_rows > 0) {
        fwrite($file, "-- Triggers for table `$table`\n");
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

// 3. 저장 프로시저 및 함수 백업
// 저장 프로시저와 함수 백업을 위해 `SHOW PROCEDURE STATUS` 및 `SHOW FUNCTION STATUS` 명령어를 사용
$proceduresResult = $conn->query("SHOW PROCEDURE STATUS WHERE Db = '$dbname'");
$functionsResult = $conn->query("SHOW FUNCTION STATUS WHERE Db = '$dbname'");

// 프로시저 백업
if ($proceduresResult->num_rows > 0) {
    fwrite($file, "-- Stored Procedures\n");
    while ($procRow = $proceduresResult->fetch_assoc()) {
        $procName = $procRow['Name'];
        $procCreate = $conn->query("SHOW CREATE PROCEDURE `$procName`");
        $procCreateRow = $procCreate->fetch_assoc();

        fwrite($file, "DELIMITER //\n");
        fwrite($file, $procCreateRow['Create Procedure'] . " //\n");
        fwrite($file, "DELIMITER ;\n\n");
    }
}

// 함수 백업
if ($functionsResult->num_rows > 0) {
    fwrite($file, "-- Stored Functions\n");
    while ($funcRow = $functionsResult->fetch_assoc()) {
        $funcName = $funcRow['Name'];
        $funcCreate = $conn->query("SHOW CREATE FUNCTION `$funcName`");
        $funcCreateRow = $funcCreate->fetch_assoc();

        fwrite($file, "DELIMITER //\n");
        fwrite($file, $funcCreateRow['Create Function'] . " //\n");
        fwrite($file, "DELIMITER ;\n\n");
    }
}

echo "Full database backup completed successfully. File: $backupFile<br>";

// 파일 닫기
fclose($file);

// MySQL 연결 종료
$conn->close();
?>
