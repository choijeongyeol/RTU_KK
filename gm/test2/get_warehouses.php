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

    // 창고명 데이터 가져오기
    $stmt = $conn->prepare("SELECT warehouse_id, warehouse_name FROM wms_warehouses where delYN ='N'");
    $stmt->execute();
    $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 가져온 데이터로 select box 옵션 생성
    $options = '';
    foreach ($warehouses as $warehouse) {
        $options .= '<option value="' . $warehouse['warehouse_id'] . '">' . $warehouse['warehouse_name'] . '</option>';
    }

    echo $options;
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
