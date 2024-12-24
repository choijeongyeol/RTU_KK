<?php
// PHP 오류 표시 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// JSON 입력 데이터 처리
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['latitude']) || !isset($data['longitude'])) {
    echo "위치 정보가 제공되지 않았습니다.";
    exit;
}

// 격자 좌표 계산 함수 (위도, 경도를 격자 좌표로 변환)
function convertToGrid($lat, $lng) {
    $RE = 6371.00877; // 지구 반경(km)
    $GRID = 5.0; // 격자 간격(km)
    $SLAT1 = 30.0; // 투영 위도1(degree)
    $SLAT2 = 60.0; // 투영 위도2(degree)
    $OLON = 126.0; // 기준점 경도(degree)
    $OLAT = 38.0; // 기준점 위도(degree)
    $XO = 43; // 기준점 X좌표 (GRID)
    $YO = 136; // 기준점 Y좌표 (GRID)

    $DEGRAD = M_PI / 180.0;
    $RADDEG = 180.0 / M_PI;

    $re = $RE / $GRID;
    $slat1 = $SLAT1 * $DEGRAD;
    $slat2 = $SLAT2 * $DEGRAD;
    $olon = $OLON * $DEGRAD;
    $olat = $OLAT * $DEGRAD;

    $sn = tan(M_PI * 0.25 + $slat2 * 0.5) / tan(M_PI * 0.25 + $slat1 * 0.5);
    $sn = log(cos($slat1) / cos($slat2)) / log($sn);
    $sf = tan(M_PI * 0.25 + $slat1 * 0.5);
    $sf = pow($sf, $sn) * cos($slat1) / $sn;
    $ro = tan(M_PI * 0.25 + $olat * 0.5);
    $ro = $re * $sf / pow($ro, $sn);

    $lat = $lat * $DEGRAD;
    $lng = $lng * $DEGRAD;

    $ra = tan(M_PI * 0.25 + $lat * 0.5);
    $ra = $re * $sf / pow($ra, $sn);
    $theta = $lng - $olon;
    if ($theta > M_PI) $theta -= 2.0 * M_PI;
    if ($theta < -M_PI) $theta += 2.0 * M_PI;
    $theta *= $sn;

    $x = floor($ra * sin($theta) + $XO + 0.5);
    $y = floor($ro - $ra * cos($theta) + $YO + 0.5);

    return ['x' => $x, 'y' => $y];
}

// 위도, 경도 가져오기
$latitude = $data['latitude'];
$longitude = $data['longitude'];

// 격자 좌표로 변환
$grid = convertToGrid($latitude, $longitude);

// API 요청 파라미터
$serviceKey = 'NyoDSPoGQxqODN9q+4LEKSX3sUCDNKBGFvCeztIkw79w80/d2RjL57GL2kx62vUStiqkowQdQ8JZdckddaQpSg=='; // 유효한 Service Key 입력
$url = 'https://apis.data.go.kr/1360000/VilageFcstInfoService_2.0/getUltraSrtNcst';

date_default_timezone_set('Asia/Seoul');
$currentHour = date('H');
$currentMinute = date('i');

// base_time 계산 (30분 단위)
if ($currentMinute < 45) {
    $baseTime = str_pad($currentHour - 1, 2, '0', STR_PAD_LEFT) . '30';
} else {
    $baseTime = $currentHour . '30';
}
$today = date('Ymd');

// API 호출
$queryParams = '?' . http_build_query([
    'serviceKey' => $serviceKey,
    'numOfRows' => '10',
    'pageNo' => '1',
    'base_date' => $today,
    'base_time' => $baseTime,
    'nx' => $grid['x'],
    'ny' => $grid['y'],
    'dataType' => 'XML',
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    echo "API 호출 실패";
    exit;
}

// XML 파싱
$xml = simplexml_load_string($response);
if ($xml === false) {
    echo "XML 파싱 실패";
    exit;
}

// 데이터 출력
echo "<h2>현재 날씨 정보</h2>";
foreach ($xml->body->items->item as $item) {
    $category = (string)$item->category;
    $value = (string)$item->obsrValue;

    switch ($category) {
        case 'T1H':
            echo "<p>기온: {$value}°C</p>";
            break;
        case 'RN1':
            echo "<p>1시간 강수량: {$value}mm</p>";
            break;
        case 'REH':
            echo "<p>습도: {$value}%</p>";
            break;
        case 'PTY':
            $precipitation = [
                '0' => '없음',
                '1' => '비',
                '2' => '비/눈',
                '3' => '눈',
                '4' => '소나기',
            ];
            echo "<p>강수형태: {$precipitation[$value]}</p>";
            break;
        case 'WSD':
            echo "<p>풍속: {$value}m/s</p>";
            break;
    }
}
?>
