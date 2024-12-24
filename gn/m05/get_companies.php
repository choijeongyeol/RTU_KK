<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php'); 

try {
    // MySQL 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 오류 출력을 위한 예외 처리
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 업체 목록 가져오기
    $stmt = $conn->prepare("SELECT cate_id, cate_name FROM wms_company where delYN ='N'");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 가져온 업체 목록을 selectbox 형태로 출력
    foreach ($result as $row) {
       // echo "<option value='" . $row['cate_id'] . "'>" . $row['cate_name'] . "</option>";
    }
        echo "<option value='0'>선택안함</option>";
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
