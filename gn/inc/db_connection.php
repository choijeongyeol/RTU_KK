<?php
// db_connection.php

$servername = "localhost";
$username = "myhanis";
$password = "Hanis123!";
$dbname = "myhanis";

// 데이터베이스 연결 생성
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("연결 실패: " . $e->getMessage());
}

// 데이터베이스 연결 함수
function getDbConnection(): ?PDO {
    $host = 'localhost'; // 데이터베이스 호스트
    $dbname = 'myhanis'; // 데이터베이스 이름
    $username = 'myhanis'; // 데이터베이스 사용자 이름
    $password = 'Hanis123!'; // 데이터베이스 비밀번호

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo '연결 실패: ' . $e->getMessage();
        return null;
    }
}
?>
