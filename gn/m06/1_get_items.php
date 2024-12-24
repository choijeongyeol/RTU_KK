<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php'); 
try {
    // MySQL 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 오류 출력을 위한 예외 처리
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 창고 목록 가져오기 (Prepared Statement 사용)
    //$stmt = $conn->prepare("SELECT DISTINCT(item_id), item_name FROM wms_stock_details WHERE quantity > 0 AND company_name <> '' ORDER BY item_name ASC");
    $stmt = $conn->prepare("SELECT DISTINCT(item_id), item_name FROM wms_stock_details WHERE quantity > 0 ORDER BY item_name ASC");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	    echo "<option value=''>제품 선택</option>";
    // 가져온 업체 목록을 selectbox 형태로 출력
    foreach ($result as $row) {
        $item_name = htmlspecialchars($row['item_name']); // XSS 공격 방지를 위해 htmlspecialchars 함수 사용
        echo "<option value='" . $row['item_id'] . "'>" . $item_name . "</option>";
    }
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
