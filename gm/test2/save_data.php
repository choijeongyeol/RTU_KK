<?php
// 데이터베이스 연결 설정
$dsn = 'mysql:host=localhost;dbname=devbine;charset=utf8';
$username = "devbine";
$password = "Hanis123!";
 

try {
    // PDO 객체 생성 및 데이터베이스 연결
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 동적 필드에서 받은 데이터 처리
    if(isset($_POST['field'])) {
        $fields = $_POST['field'];
        foreach($fields as $field) {
            // 필드 유효성 검사 또는 필요한 처리 수행

            // 데이터베이스에 데이터 삽입
            $stmt = $pdo->prepare("INSERT INTO test_tb (name) VALUES (:field)");
            $stmt->bindParam(':field', $field);
            $stmt->execute();
        }
        echo "데이터가 성공적으로 저장되었습니다.";
    } else {
        echo "저장할 데이터가 없습니다.";
    }
} catch(PDOException $e) {
    echo "오류: " . $e->getMessage();
}
?>
