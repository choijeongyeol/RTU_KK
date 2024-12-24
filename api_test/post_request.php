<?php
// cURL 세션 초기화
$ch = curl_init();

// 요청할 첫 번째 URL 설정 (IP 주소)
$url_ip = "http://43.200.77.82/api-endpoint";
curl_setopt($ch, CURLOPT_URL, $url_ip);

// POST 데이터 설정
$data_ip = array(
    'key1' => 'value1',
    'key2' => 'value2',
);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_ip));

// cURL 옵션 설정
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 응답을 문자열로 반환

// cURL 요청 실행 및 응답 받기
$response_ip = curl_exec($ch);

// cURL 오류 확인
if (curl_errno($ch)) {
    echo 'cURL Error (IP): ' . curl_error($ch);
} else {
    // 응답 데이터 처리
    echo "Response from 43.200.77.82:\n";
    echo $response_ip;
}

// 두 번째 요청을 위해 URL 재설정 (도메인 주소)
$url_domain = "http://devhanis.shop/api-endpoint";
curl_setopt($ch, CURLOPT_URL, $url_domain);

// POST 데이터 설정
$data_domain = array(
    'key1' => 'value1',
    'key2' => 'value2',
);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_domain));

// cURL 요청 실행 및 응답 받기
$response_domain = curl_exec($ch);

// cURL 오류 확인
if (curl_errno($ch)) {
    echo 'cURL Error (Domain): ' . curl_error($ch);
} else {
    // 응답 데이터 처리
    echo "\n\nResponse from devhanis.shop:\n";
    echo $response_domain;
}

// cURL 세션 종료
curl_close($ch);
?>
