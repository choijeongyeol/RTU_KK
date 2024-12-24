<?php
// JWT 라이브러리를 사용하기 위한 Composer autoload 파일을 포함합니다.
include_once($_SERVER['DOCUMENT_ROOT'].'/gm/composer/vendor/autoload.php');

use Firebase\JWT\JWT;

// JWT 시크릿 키
define('JWT_SECRET', 'your_secret_key');

// 가상의 사용자 데이터
$users = [
    'admin' => ['password' => 'admin123', 'role' => 'admin'],
    'user' => ['password' => 'user123', 'role' => 'user']
];

// 세션 시작
session_start();

// 로그인 API
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // 사용자 인증
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        // JWT 생성
        $token = JWT::encode(['user' => $username, 'role' => $users[$username]['role']], JWT_SECRET);
        
        http_response_code(200);
        echo json_encode(array('token' => $token));
        exit;
    } else {
        http_response_code(401);
        echo json_encode(array('message' => 'Login failed'));
        exit;
    }
}

// JWT 검증 함수
function verifyJWT($token) {
    try {
        $decoded = JWT::decode($token, JWT_SECRET, array('HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}

// API 호출 전에 사용자의 인증 및 권한을 확인합니다.
function checkPermission($token, $requiredRole) {
    $decoded = verifyJWT($token);
    return $decoded && isset($decoded['role']) && $decoded['role'] === $requiredRole;
}

// 데이터 유효성 검사
function validateData($data) {
    return isset($data['id']) && isset($data['quantity']) && is_numeric($data['quantity']);
}

// API 호출 전 권한 확인
function authorize($token, $requiredRole) {
    if (!checkPermission($token, $requiredRole)) {
        http_response_code(403);
        echo json_encode(array('message' => 'Forbidden'));
        exit;
    }
}

// 재고 업데이트 API
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_GET['endpoint'] === 'inventory') {
    // 권한 확인
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(array('message' => 'Unauthorized'));
        exit;
    }
    $token = explode(' ', $headers['Authorization'])[1];
    authorize($token, 'admin');

    // 데이터 유효성 검사
    $data = json_decode(file_get_contents('php://input'), true);
    if (!validateData($data)) {
        http_response_code(400);
        echo json_encode(array('message' => 'Invalid data'));
        exit;
    }
    
    // 유효한 데이터일 경우, 재고 업데이트를 수행합니다.
    // ...
}
?>
