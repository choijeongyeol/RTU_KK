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
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // POST 데이터 수신 및 처리
        $productNames = $_POST['product_name'];
        $companyNames = $_POST['company_name'];
        $warehouseNames = $_POST['warehouse_name'];
        $angleNames = $_POST['angle_name'];
        $plannedQuantities = $_POST['planned_quantity'];
        $incomingQuantities = $_POST['incoming_quantity'];

        // 데이터베이스에 삽입
        $stmt = $conn->prepare("INSERT INTO test_tb2 (product_name, company_name, warehouse_name, angle_name, planned_quantity, incoming_quantity) VALUES (:product_name, :company_name, :warehouse_name, :angle_name, :planned_quantity, :incoming_quantity)");
        
        foreach ($productNames as $key => $productName) {
            $stmt->bindParam(':product_name', $productName);
            $stmt->bindParam(':company_name', $companyNames[$key]);
            $stmt->bindParam(':warehouse_name', $warehouseNames[$key]);
            $stmt->bindParam(':angle_name', $angleNames[$key]);
            $stmt->bindParam(':planned_quantity', $plannedQuantities[$key]);
            $stmt->bindParam(':incoming_quantity', $incomingQuantities[$key]);
            $stmt->execute();
        }
        
        echo "데이터가 성공적으로 저장되었습니다!";
    } else {
        // 올바르지 않은 요청인 경우 에러 메시지 반환
        http_response_code(400);
        echo "올바르지 않은 요청입니다!";
    }
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
