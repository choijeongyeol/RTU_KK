<?php  session_start();
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');



function generateDateTimeWithRandomSuffix() {
    // 현재 날짜 및 시간 가져오기
    $currentDateTime = date('YmdHis'); // 14자 형식: 년월일시분초

    // 랜덤 문자 생성 (18자)
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomSuffix = '';
    for ($i = 0; $i < 18; $i++) {
        $randomSuffix .= $characters[rand(0, strlen($characters) - 1)];
    }

    // 14자 날짜 + 랜덤 18자
    return $currentDateTime . $randomSuffix;
}

$user_token = generateDateTimeWithRandomSuffix();


// 공통 JSON 응답 함수
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
    // POST 데이터 가져오기
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
   // $email_receive = $_POST['email_receive'] ?? null;

    // 필수값 확인
    if (empty($partner_id) || empty($user_id) || empty($user_name) || empty($user_pw) || empty($user_phone) || empty($user_addr) || empty($user_addr2) || empty($user_email)) {
        jsonResponse('error', '필수 입력값이 누락되었습니다.');
    }

    // 이메일 형식 유효성 검사
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse('error', '유효하지 않은 이메일 형식입니다.');
    }
 

    try {
        // 비밀번호 암호화
        $hashed_pw = password_hash($user_pw, PASSWORD_DEFAULT);

        // SQL 쿼리 준비
        $sql = "INSERT INTO RTU_user (
                    partner_id, user_id, user_name, user_pw, user_tel, legalcode, 
                    user_addr, user_addr2, user_email, email_receive, spartner_id, user_token
                ) VALUES (
                    :partner_id, :user_id, :user_name, :user_pw, :user_phone, :legalcode, 
                    :user_addr, :user_addr2, :user_email, :email_receive, :spartner_id, :user_token
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

        // 쿼리 실행
        $stmt->execute();

        // 성공 응답
        jsonResponse('success', '사용자가 성공적으로 등록되었습니다.');
		// 3초 후 페이지 이동
		echo '<script type="text/javascript">';
		echo 'setTimeout(function() { window.location.href = "user_list.php"; }, 3000);';
		echo '</script>';
		
    } catch (PDOException $e) {
        // PDO 예외 처리
        jsonResponse('error', '데이터 저장 중 오류가 발생했습니다.', $e->getMessage());
    }
} else {
    // 잘못된 요청 응답
    jsonResponse('error', '잘못된 요청입니다.');
}
