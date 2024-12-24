<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php');

// 토큰 유효성 검사 
function ck_token_cnt($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(user_id), '0') as cnt FROM `wms_user` WHERE delYN = 'N' and user_token = :token");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $token_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $token_cnt;
}

function token_auth() { // Authorization 헤더에서 토큰 값을 가져옴
    $token = null;

    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            $authorizationHeader = $headers['Authorization'];
            $token = $authorizationHeader;
        } else {
            $token = 'Authorization Header not found.';
        }
    } else {
        $token = 'apache_request_headers function not available.';
    }

    if ($token == 'Authorization Header not found.' || $token == 'apache_request_headers function not available.') {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } else {
            $token = 'Authorization Header not found in both methods.';
        }
    }

    if (strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }

    return $token;
}

header('Content-Type: application/json');

$token = token_auth();
$tk_cnt = ck_token_cnt($token);

if ($tk_cnt[0]['cnt'] > 0) {
    echo json_encode(['message' => 'Token is valid']);
} else {
    echo json_encode(['message' => 'Token is invalid or not found']);
}
?>
