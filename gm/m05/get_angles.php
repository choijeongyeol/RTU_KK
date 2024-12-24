<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/db_connection.php'); 


// 선택된 창고의 ID를 받아옴
$warehouse_id = $_GET['warehouse_id'];

try {
    // MySQL 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 오류 출력을 위한 예외 처리
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 선택된 창고에 해당하는 앵글 목록을 가져오는 쿼리
    $stmt = $conn->prepare("SELECT angle_id, angle_name FROM wms_angle WHERE warehouse_id = :warehouse_id and delYN = 'N'");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();

    // 결과 가져오기
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON 형태로 반환
    echo json_encode($result);
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
