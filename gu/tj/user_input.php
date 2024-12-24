<?php
require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦.
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');

// 네이버 지도 API 키
define('CLIENT_ID', 'aiwhoshchv'); // 네이버 API Client ID
define('CLIENT_SECRET', 'yEd90qJoB6wtWjpNxmXxqbIMAS3Xwg4UhCIEB7PU'); // 네이버 API Client Secret

// 위도와 경도를 가져오는 함수
function getCoordinates($address) {
    $url = "https://naveropenapi.apigw.ntruss.com/map-geocode/v2/geocode";
    $queryString = http_build_query(['query' => $address]);
    $requestUrl = $url . '?' . $queryString;

    // cURL 초기화
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requestUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-NCP-APIGW-API-KEY-ID: ' . CLIENT_ID,
        'X-NCP-APIGW-API-KEY: ' . CLIENT_SECRET
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['error' => "API 요청 실패 (HTTP 코드: $httpCode)"];
    }

    $data = json_decode($response, true);
    if (empty($data['addresses'])) {
        return ['error' => "주소를 찾을 수 없습니다."];
    }

    $location = $data['addresses'][0];
    return [
        'latitude' => $location['y'], // 위도
        'longitude' => $location['x'] // 경도
    ];
}

// 랜덤 토큰 생성 함수
function generateDateTimeWithRandomSuffix() {
    $currentDateTime = date('YmdHis'); // 14자 형식: 년월일시분초
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomSuffix = '';
    for ($i = 0; $i < 18; $i++) {
        $randomSuffix .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $currentDateTime . $randomSuffix; // 14자 날짜 + 랜덤 18자
}

// JSON 응답 처리 함수
function jsonResponse($status, $message, $error = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($error) {
        $response['error'] = $error;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

// POST 요청 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $partner_id = $_SESSION['partner_id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $user_name = $_POST['user_name'] ?? null;
    $user_pw = $_POST['user_pw'] ?? null;
    $user_phone = $_POST['user_phone'] ?? null;
    $legalcode = $_POST['legalcode'] ?? null;
    $user_addr = $_POST['user_addr'] ?? null;
    $user_addr2 = $_POST['user_addr2'] ?? null;
    $user_email = $_POST['user_email'] ?? null;
    $spartner_id = $_POST['spartner_id'] ?? null;
    $email_receive = 1;

    // 필수값 확인
    if (empty($partner_id) || empty($user_id) || empty($user_name) || empty($user_pw) || empty($user_phone) || empty($user_addr) || empty($user_email)) {
        jsonResponse('error', '필수 입력값이 누락되었습니다.');
    }

    // 이메일 형식 유효성 검사
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse('error', '유효하지 않은 이메일 형식입니다.');
    }

    // 주소를 기반으로 위도와 경도를 가져옴
    $coordinates = getCoordinates($user_addr);
    if (isset($coordinates['error'])) {
        jsonResponse('error', $coordinates['error']);
    }

    $latitude = $coordinates['latitude'];
    $longitude = $coordinates['longitude'];

    // 토큰 생성
    $user_token = generateDateTimeWithRandomSuffix();

    try {
        // 비밀번호 암호화
        $hashed_pw = password_hash($user_pw, PASSWORD_DEFAULT);

        // SQL 쿼리 준비
        $sql = "INSERT INTO RTU_user (
                    partner_id, user_id, user_name, user_pw, user_tel, legalcode, 
                    user_addr, user_addr2, user_email, email_receive, spartner_id, 
                    user_token, latitude, longitude
                ) VALUES (
                    :partner_id, :user_id, :user_name, :user_pw, :user_phone, :legalcode, 
                    :user_addr, :user_addr2, :user_email, :email_receive, :spartner_id, 
                    :user_token, :latitude, :longitude
                )";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':user_pw', $hashed_pw);
        $stmt->bindParam(':user_phone', $user_phone);
        $stmt->bindParam(':legalcode', $legalcode);
        $stmt->bindParam(':user_addr', $user_addr);
        $stmt->bindParam(':user_addr2', $user_addr2);
        $stmt->bindParam(':user_email', $user_email);
        $stmt->bindParam(':email_receive', $email_receive);
        $stmt->bindParam(':spartner_id', $spartner_id);
        $stmt->bindParam(':user_token', $user_token);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);

        $stmt->execute();
        jsonResponse('success', '사용자가 성공적으로 등록되었습니다.');
    } catch (PDOException $e) {
        jsonResponse('error', '데이터 저장 중 오류가 발생했습니다.', $e->getMessage());
    }
} else {
    jsonResponse('error', '잘못된 요청입니다.');
}
?>
