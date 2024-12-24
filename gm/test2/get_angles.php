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
	
     $warehouse_id = $_GET['warehouse_id'];

    // 창고명 데이터 가져오기
     // $stmt = $conn->prepare("SELECT angle_id, angle_name FROM wms_angle WHERE warehouse_id = :warehouse_id");	
  $stmt = $conn->prepare("SELECT angle_id, angle_name FROM wms_angle where delYN = 'N'");
    $stmt->execute();
    $angles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 가져온 데이터로 select box 옵션 생성
    $options = '';
    foreach ($angles as $angle) {
        $options .= '<option value="' . $angle['angle_id'] . '">' . $angle['angle_name'] . '</option>';
    }

    echo $options;
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
