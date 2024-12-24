<?php
// thingplug_test.php

// 설정 파일 포함
include 'config.php';

echo "Starting cURL request...<br>";

// cURL 세션 초기화
$ch = curl_init();

if (!$ch) {
    die("cURL initialization failed");
}

// cURL 옵션 설정
curl_setopt($ch, CURLOPT_URL, $apiUrl); // 설정된 API URL 사용
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 응답을 문자열로 반환
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
));

echo "Executing cURL request...<br>";

// GET 요청 실행 및 응답 수신
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "HTTP Status Code: " . $httpCode;
    echo "\nResponse: " . $response;
}

// cURL 세션 종료
curl_close($ch);

echo "Finished cURL request.<br>";
?>
