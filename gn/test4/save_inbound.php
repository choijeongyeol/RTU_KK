<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php'); 

// POST로 전달된 데이터 수신
$product_names = $_POST['product_name'];
$company_names = $_POST['company_name'];
$warehouse_id = $_POST['warehouse_id'];
$angle_id = $_POST['angle_id'];
$planned_quantities = $_POST['planned_quantity'];
$inbound_quantities = $_POST['inbound_quantity'];
$plan_date = $_POST['plan_date'];

try {
    // MySQL 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 오류 출력을 위한 예외 처리
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 입력된 데이터를 wms_inbound 테이블에 저장
    $stmt = $conn->prepare("INSERT INTO wms_inbound (product_name, company_name, warehouse_id, angle_id, planned_quantity, inbound_quantity,plan_date,rdate) VALUES (:product_name, :company_name, :warehouse_id,:angle_id, :planned_quantity, :inbound_quantity, :plan_date, :rdate)");

    // 각 필드에 대해 반복하여 값을 바인딩하고 쿼리를 실행
    for ($i = 0; $i < count($product_names); $i++) {
        $stmt->bindParam(':product_name', $product_names[$i]);
        $stmt->bindParam(':company_name', $company_names[$i]);
        $stmt->bindParam(':warehouse_id', $warehouse_id[$i]);
        $stmt->bindParam(':angle_id', $angle_id[$i]);
        $stmt->bindParam(':planned_quantity', $planned_quantities[$i]);
        $stmt->bindParam(':inbound_quantity', $inbound_quantities[$i]);
        $stmt->bindParam(':plan_date', $plan_date);
        $stmt->bindParam(':rdate', date("Y-m-d H:i:s"));
        $stmt->execute();
    }

    echo "입고지시가 성공적으로 저장되었습니다.";
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
