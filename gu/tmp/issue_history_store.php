<?php
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $issue_name = $_POST['issue_name'];
    $issue_date = $_POST['issue_date'];
    $facility_id = $_POST['facility_id'];
    $user_idx = $_POST['user_idx'];
    $lora_idx = $_POST['lora_idx'];
    $status = '1'; // 기본 상태를 '접수'으로 설정    0 = 미신청  / 1 = 접수 / 2 = 처리중 / 3 = 처리완료

    try {
        $sql = "
            INSERT INTO RTU_Issue_History_New (issue_name, issue_date, facility_id, user_idx, lora_idx, status)
            VALUES (:issue_name, :issue_date, :facility_id, :user_idx, :lora_idx, :status)
        ";
 
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':issue_name', $issue_name);
        $stmt->bindParam(':issue_date', $issue_date);
        $stmt->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_idx', $user_idx, PDO::PARAM_INT);
        $stmt->bindParam(':lora_idx', $lora_idx);
        $stmt->bindParam(':status', $status);

        $stmt->execute();
        echo "장애 이력이 성공적으로 추가되었습니다.";
        echo "<br><a href='issue_history_create.php'>돌아가기</a>";
    } catch (PDOException $e) {
        echo "데이터베이스 오류: " . $e->getMessage();
    }
} else {
    echo "잘못된 요청입니다.";
}
?>
