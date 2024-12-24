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

    // 제품명 데이터 가져오기
    $stmt = $conn->prepare("SELECT product_name FROM test_products");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 가져온 데이터로 select box 옵션 생성
    $options = '';
    foreach ($products as $product) {
        $options .= '<option value="' . $product['product_name'] . '">' . $product['product_name'] . '</option>';
    }

    echo $options;
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
