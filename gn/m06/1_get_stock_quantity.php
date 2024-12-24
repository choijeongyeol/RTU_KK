<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php'); 

// 선택한 제품, 거래처, 창고 정보 받아오기
$item_id = $_POST['item_id'];
$company_id = $_POST['company_id'];
$warehouse_id = $_POST['warehouse_id'];
$angle_id = $_POST['angle_id'];

try {
    // MySQL 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 오류 출력을 위한 예외 처리
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 재고 수량 가져오기
    //$stmt = $conn->prepare("SELECT quantity FROM wms_stock_details WHERE item_id = :item_id AND company_id = :company_id AND warehouse_id = :warehouse_id AND angle_id = :angle_id");
    $stmt = $conn->prepare("SELECT quantity FROM wms_stock_details WHERE item_id = :item_id  AND warehouse_id = :warehouse_id AND angle_id = :angle_id");
    // 바인딩된 변수 설정
    $stmt->bindParam(':item_id', $item_id);
    //$stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
 
    $stmt->execute();
    $result_quantity = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 결과가 존재하는지 확인 후 출력
    if (!empty($result_quantity) && $result_quantity[0]['quantity'] !== null) {
        echo $result_quantity[0]['quantity'];
    } else {
        echo "미지정";
    }	

} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
