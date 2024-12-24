<?php

// ThingPlug API에 요청을 보내는 함수
function sendThingPlugRequest($url, $data, $method = 'POST') {
    $ch = curl_init();

    // HTTP 헤더 설정
    $headers = [
        'Content-Type: application/json',
        'uKey: bmp3WWFyUzhBNmFLcEdicS9FUnJMMkNTN1lDYlZLdTBhaExEdWdoanUrdlZ4Sm9ZczduV09qTi9rUTZuaHBOcg=='
    ];

    // cURL 옵션 설정
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // SSL 인증 활성화
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2); // TLS 1.2 강제 설정
    // curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cacert.pem'); // CA 인증서 경로 설정

    // HTTP 메서드 설정
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return "cURL Error: " . $error . " | HTTP Code: " . $httpCode;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200) {
        return "HTTP Error: " . $httpCode;
    }

    return json_decode($response, true);
}

// ThingPlug API에 보낼 URL 및 데이터 설정
$url = 'https://thingplugpf.sktiot.com:9000/0060261000000799/v1_0/mgmtCmd';
$data = [
    'mgc' => [
        'exe' => true,
        'cmt' => 'ResetCommand',
        'exra' => '0',
        'requestOrigin' => 'Originator',
        'url' => 'http://43.200.77.82/tp_api/receive_data.php'
    ]
];

// ThingPlug API에 요청 보내기
$response = sendThingPlugRequest($url, $data, 'POST');

if ($response) {
    echo "Response from ThingPlug: " . json_encode($response);
} else {
    echo "Failed to connect to ThingPlug API.";
}

?>
