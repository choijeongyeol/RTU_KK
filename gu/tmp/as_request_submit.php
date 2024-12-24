<?php
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// 요청 데이터 수신
$issue_id = $_POST['issue_id'] ?? null;
$notes = $_POST['notes'] ?? null;
$technician_id = $_POST['technician_id'] ?? null;
$status = "2"; //$_POST['status'] ?? 2; // 기본값으로 신청 상태 설정

// api7002_set 함수 호출하여 AS 요청 생성
if ($issue_id) {
    $result = api7002_set_noTOKEN($issue_id, $notes, $technician_id, $status);
    if ($result['header']['resultCode'] == 200) {
        echo "AS 요청이 성공적으로 접수되었습니다.";
    } else {
        echo "AS 요청 중 오류가 발생했습니다: " . $result['header']['message'];
    }
} else {
    echo "유효한 Issue ID가 필요합니다.";
}
