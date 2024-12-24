<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

$user_id = $_GET['user_id']; // AJAX 요청에서 user_id를 받음

try {
    // user_id 기반으로 로라 장비와 관련 발전소 정보를 가져오는 쿼리
    $sql = "
        SELECT DISTINCT l.id AS lora_idx, 
               l.lora_id, 
               CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
        FROM RTU_facility f
        JOIN RTU_lora l ON f.lora_id = l.lora_id
        WHERE f.user_id = :user_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR); // user_id 바인딩
    $stmt->execute();
    $loras = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON 형태로 로라 장비 목록 반환
    echo json_encode(['loras' => $loras]);
} catch (PDOException $e) {
    echo json_encode(['error' => "데이터베이스 오류: " . $e->getMessage()]);
}
?>
