<?php
$old_db = 'RTU'; // 기존 데이터베이스 이름
$new_db = 'RTU2'; // 새 데이터베이스 이름

// MySQL에 연결
$mysqli = new mysqli("localhost", "myhanis", "Hanis123!", $old_db);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 새 데이터베이스 생성
$mysqli->query("CREATE DATABASE IF NOT EXISTS $new_db");

// 테이블 목록 가져오기
$result = $mysqli->query("SHOW TABLES FROM $old_db");

while ($row = $result->fetch_row()) {
    $table = $row[0];
    
    // 테이블 구조 복사
    $create_table_query = "CREATE TABLE $new_db.$table LIKE $old_db.$table";
    $mysqli->query($create_table_query);

    // 테이블 데이터 복사
    $insert_data_query = "INSERT INTO $new_db.$table SELECT * FROM $old_db.$table";
    $mysqli->query($insert_data_query);
}

echo "Database copy completed!";
$mysqli->close();
?>
