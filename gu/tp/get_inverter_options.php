<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

$lora_idx = $_GET['lora_idx']; // AJAX 요청에서 lora_idx를 받음

try {
    // lora_idx에 기반하여 관련 인버터 정보를 가져오는 쿼리
    $sql = "SELECT cid FROM RTU_facility WHERE lora_id = (SELECT lora_id FROM RTU_lora WHERE id = :lora_idx)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':lora_idx', $lora_idx, PDO::PARAM_STR); // lora_idx로 바인딩
    $stmt->execute();
    $inverters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON 형식으로 인버터 목록 반환
    echo json_encode(['inverters' => $inverters]);
} catch (PDOException $e) {
    echo json_encode(['error' => "데이터베이스 오류: " . $e->getMessage()]);
}
?>
