<?php
// 네이버 지도 API 키
define('CLIENT_ID', 'aiwhoshchv'); // 네이버 API Client ID
define('CLIENT_SECRET', 'yEd90qJoB6wtWjpNxmXxqbIMAS3Xwg4UhCIEB7PU'); // 네이버 API Client Secret

// 요청할 주소 (예: "서울특별시 종로구")
$address = "서울특별시 종로구";

// Geocoding API URL
$url = "https://naveropenapi.apigw.ntruss.com/map-geocode/v2/geocode";

// 쿼리 파라미터 추가
$queryString = http_build_query(['query' => $address]);
$requestUrl = $url . '?' . $queryString;

// cURL 초기화
$ch = curl_init();

// cURL 옵션 설정
curl_setopt($ch, CURLOPT_URL, $requestUrl); // 요청할 URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 결과를 문자열로 반환
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-NCP-APIGW-API-KEY-ID: ' . CLIENT_ID, // Client ID
    'X-NCP-APIGW-API-KEY: ' . CLIENT_SECRET // Client Secret
]);

// 요청 실행
$response = curl_exec($ch);

// HTTP 응답 코드 확인
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// cURL 종료
curl_close($ch);

// 결과 출력
if ($httpCode === 200) {
    // 응답 데이터를 JSON 디코딩
    $data = json_decode($response, true);
    if (!empty($data['addresses'])) {
        echo "주소: " . $address . "\n";
        echo "위도: " . $data['addresses'][0]['y'] . "\n"; // 위도
        echo "경도: " . $data['addresses'][0]['x'] . "\n"; // 경도
    } else {
        echo "주소를 찾을 수 없습니다.\n";
    }
} else {
    echo "API 요청 실패 (HTTP 코드: $httpCode)\n";
    echo "응답 메시지: $response\n";
}