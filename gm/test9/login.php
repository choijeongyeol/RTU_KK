<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/db_connection.php');

function authenticate($username, $password) {
    global $conn;

    // 여기서는 고정된 토큰을 사용하는 것으로 가정합니다.
    // 실제로는 비밀번호 검증 후 토큰을 생성해야 합니다.
    if ($username === 'testid' && $password === '1234') {
        $stmt = $conn->prepare("SELECT user_token FROM wms_user WHERE user_id = :username AND delYN = 'N'");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return $user['user_token'];
        }
    }

    return null;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['username']) && isset($data['password'])) {
    $token = authenticate($data['username'], $data['password']);

    if ($token) {
        echo json_encode(['token' => $token]);
    } else {
        echo json_encode(['message' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['message' => 'Username and password required']);
}
?>
