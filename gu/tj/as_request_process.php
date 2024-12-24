<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// POST 데이터 받기
$issue_id = $_POST['issue_id'];
$status = $_POST['status'];
$technician_id = $_POST['technician_id'];

// AS 상태 및 기술자 업데이트 SQL
$sql = "
    INSERT INTO RTU_AS_Request (issue_id, request_date, status, technician_id) 
    VALUES (:issue_id, NOW(), :status, :technician_id)
    ON DUPLICATE KEY UPDATE 
        status = :status,
        technician_id = :technician_id,
        updated_at = NOW();
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':issue_id', $issue_id, PDO::PARAM_INT);
$stmt->bindParam(':status', $status, PDO::PARAM_STR);
$stmt->bindParam(':technician_id', $technician_id, PDO::PARAM_STR);

if ($stmt->execute()) {
    echo "AS 상태와 기술자 정보가 성공적으로 업데이트되었습니다.";
} else {
    echo "업데이트 실패: 오류가 발생했습니다.";
}
?>
