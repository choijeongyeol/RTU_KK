<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/db_connection.php');

// 토큰 유효성 검사 
function ck_token_cnt($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(user_id), '0') as cnt FROM `wms_user` WHERE delYN = 'N' and user_token = :token");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $token_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $token_cnt;
}

// 토큰 유효성 검사 
function ck_token_user($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT user_id, user_name FROM `wms_user` WHERE delYN = 'N' AND user_token = :token");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $token_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $token_user;
}

function token_auth() { // Authorization 헤더에서 토큰 값을 가져옴
    $token = null;

    // HTTP_AUTHORIZATION 헤더를 확인하여 토큰을 가져옴
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();

        // 'Authorization' 헤더가 있는지 확인하고 출력합니다
        if (isset($headers['Authorization'])) {
            $authorizationHeader = $headers['Authorization'];
            $token = $authorizationHeader;
        } else {
            $token = 'Authorization Header not found.';
        }
    } else {
        $token = 'apache_request_headers function not available.';
    }

    // 만약 apache_request_headers 함수가 작동하지 않는다면, $_SERVER 변수를 사용해 헤더를 확인
    if ($token == 'Authorization Header not found.' || $token == 'apache_request_headers function not available.') {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } else {
            $token = 'Authorization Header not found in both methods.';
        }
    }

    // 헤더 값에서 "Bearer "를 제거하고 순수 토큰 값을 반환
    if (strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }

    return $token;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<?php
// 입고지시 목록 날짜 가져오기
function api_tk() {
    $token = token_auth();
    echo "Token received: " . htmlspecialchars($token) . "<br>"; // 토큰을 출력하여 확인
    $tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

    if ($tk_cnt[0]['cnt'] > 0) {
        echo "Token is valid. Token count: " . $tk_cnt[0]['cnt'];
        $token_user = ck_token_user($token);
        echo "<br>User ID: " . $token_user[0]['user_id'];
        echo "<br>User Name: " . $token_user[0]['user_name'];
    } else {
        echo "Token is invalid or not found.";
    }
}

api_tk();
?>
</body>
</html>
