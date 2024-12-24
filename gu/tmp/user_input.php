<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');


// POST 요청이 있을 때만 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 입력된 데이터 가져오기 (필수값 검사)
    $user_id = $_POST['user_id'] ?? null;
    $user_name = $_POST['user_name'] ?? null;
    $user_pw = $_POST['user_pw'] ?? null;
    $user_phone = $_POST['user_phone'] ?? null;
    $user_addr = $_POST['user_addr'] ?? null;
    $user_email = $_POST['user_email'] ?? null;
    $sms_receive = $_POST['sms_receive'] ?? null;
    $email_receive = $_POST['email_receive'] ?? null;

    // 필수값 체크 (NULL 또는 빈 값이 있는지 확인)
    if (empty($user_id) || empty($user_name) || empty($user_pw) || empty($user_phone) || empty($user_addr) || empty($user_email)) {
        echo json_encode(['status' => 'error', 'message' => '필수 입력값이 누락되었습니다.']);
        exit();
    }

    try {
        // 비밀번호 암호화
        $hashed_pw = password_hash($user_pw, PASSWORD_DEFAULT);

        // 데이터베이스에 삽입
        $sql = "INSERT INTO RTU_user (user_id, user_name, user_pw, user_tel, user_addr, user_email, sms_receive, email_receive) 
                VALUES (:user_id, :user_name, :user_pw, :user_phone, :user_addr, :user_email, :sms_receive, :email_receive)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':user_pw', $hashed_pw);
        $stmt->bindParam(':user_phone', $user_phone);
        $stmt->bindParam(':user_addr', $user_addr);
        $stmt->bindParam(':user_email', $user_email);
        $stmt->bindParam(':sms_receive', $sms_receive);
        $stmt->bindParam(':email_receive', $email_receive);

        // 쿼리 실행
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => '사용자가 성공적으로 등록되었습니다.'], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        // 오류 로그 기록
        error_log('Database Error: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => '데이터 저장 중 오류가 발생했습니다.'], JSON_UNESCAPED_UNICODE);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
}
