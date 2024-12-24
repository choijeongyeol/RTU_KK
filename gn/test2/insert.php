<?php
// 데이터베이스 연결 설정
$servername = "localhost";
$username = "devbine";
$password = "Hanis123!";
$dbname = "devbine";

 


// MySQL 연결 생성 및 확인
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST 데이터 가져오기
$name = $_POST["name"];
$email = $_POST["email"];

// SQL 쿼리 작성하여 데이터 삽입
$sql = "INSERT INTO test_tb (name, email) VALUES ('$name', '$email')";
if ($conn->query($sql) === TRUE) {
    echo "데이터가 성공적으로 추가되었습니다.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// MySQL 연결 종료
$conn->close();
?>
