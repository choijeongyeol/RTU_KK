<?php
// ThingPlug에서 전송한 데이터를 수신
$request_body = file_get_contents('php://input');

// 로그 기록 (테스트 시 유용)
error_log("Received Notification: " . $request_body);

// 데이터를 처리하고 필요한 로직 수행
// 예: 데이터베이스에 저장하거나 특정 조건에 따라 처리
echo "Notification received successfully.";
?>
