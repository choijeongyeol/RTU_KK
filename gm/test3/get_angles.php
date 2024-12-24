<?php
// 데이터베이스 연결 설정
$servername = "localhost";
$username = "devbine";
$password = "Hanis123!";
$dbname = "devbine";
 
try {
    // MySQL 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 오류 출력을 위한 예외 처리
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 창고 목록 가져오기
    $stmt = $conn->prepare("SELECT angle_id, angle_name FROM wms_angle");
    $stmt->execute();
    $angles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON 형식으로 출력
    header('Content-Type: application/json');
    echo json_encode($angles);
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
