<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');
 
// POST 요청이 있을 때만 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 입력된 데이터 가져오기
    $rtu_code = $_POST['rtu_code'] ?? null;
    $lora_id = $_POST['lora_id'] ?? null;
    $app_eui = $_POST['app_eui'] ?? null;
    $ukey = $_POST['ukey'] ?? null;
    $rtu_companyname = $_POST['rtu_companyname'] ?? null;

    // 데이터가 비어 있는지 확인 (기본적인 유효성 검사)
    if (!$rtu_code || !$lora_id || !$app_eui || !$ukey) {
        echo json_encode(['status' => 'error', 'message' => '모든 필드를 입력해 주세요.']);
        exit();
    }

    try {
        // 로라 아이디 중복 확인
        $check_sql = "SELECT COUNT(*) FROM RTU_lora WHERE lora_id = :lora_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(':lora_id', $lora_id);
        $check_stmt->execute();
        $count = $check_stmt->fetchColumn();

        if ($count > 0) {
            // 중복된 경우 에러 메시지 반환
            echo json_encode(['status' => 'error', 'message' => '로라 아이디가 이미 등록되어 있습니다.']);
            exit();
        }

        // 데이터베이스에 삽입
        $sql = "INSERT INTO RTU_lora (rtu_code, lora_id, app_eui, ukey, rtu_companyname) 
                VALUES (:rtu_code, :lora_id, :app_eui, :ukey, :rtu_companyname)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':rtu_code', $rtu_code);
        $stmt->bindParam(':lora_id', $lora_id);
        $stmt->bindParam(':app_eui', $app_eui);
        $stmt->bindParam(':ukey', $ukey);
        $stmt->bindParam(':rtu_companyname', $rtu_companyname);
        $stmt->execute();
 
        echo json_encode(['status' => 'success', 'message' => '데이터가 성공적으로 저장되었습니다.'], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        // 오류가 발생한 경우 구체적인 오류 메시지를 출력
        echo json_encode(['status' => 'error', 'message' => '데이터 저장 중 오류가 발생했습니다.', 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
}
?>
