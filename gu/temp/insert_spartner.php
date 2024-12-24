<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');

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
    session_start();

    // 세션에서 partner_id 가져오기
    if (!isset($_SESSION['partner_id'])) {
        jsonResponse('error', '세션에 파트너 ID가 없습니다.');
    }
    $partner_id = $_SESSION['partner_id'];

    // POST 데이터 가져오기
    $spartner_name = $_POST['spartner_name'] ?? null;
    $spartner_tel = $_POST['spartner_tel'] ?? null;
    $spartner_addr = $_POST['spartner_addr'] ?? null;
    $spartner_addr2 = $_POST['spartner_addr2'] ?? null;
    $spartner_email = $_POST['spartner_email'] ?? null;
    $spartner_role = $_POST['spartner_role'] ?? 1; // 기본값 1
    $spartner_use = $_POST['spartner_use'] ?? 'Y'; // 기본값 'Y'

    // 필수값 확인
    if (empty($spartner_name) || empty($spartner_tel) || empty($spartner_addr) || empty($spartner_email)) {
        jsonResponse('error', '필수 입력값이 누락되었습니다.');
    }

    // 이메일 형식 유효성 검사
    if (!filter_var($spartner_email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse('error', '유효하지 않은 이메일 형식입니다.');
    }

    try {
        // RTU_spartner 테이블에서 현재 partner_id에 해당하는 가장 큰 spartner_id 조회
        $sql = "SELECT spartner_id 
                FROM RTU_spartner 
                WHERE partner_id = :partner_id 
                ORDER BY spartner_id DESC 
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $stmt->execute();
        $last_spartner_id = $stmt->fetchColumn();

        // 새 spartner_id 생성
        if ($last_spartner_id) {
            $last_number = (int)substr($last_spartner_id, -3); // 마지막 3자리 숫자 추출
            $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT); // 001부터 +1
        } else {
            $new_number = '001'; // 처음 등록인 경우
        }
        $new_spartner_id = $partner_id . $new_number;

        // SQL 쿼리 준비
        $sql = "INSERT INTO RTU_spartner (
                    partner_id, spartner_id, spartner_name, spartner_tel, spartner_addr, 
                    spartner_addr2, spartner_email, spartner_role, spartner_use
                ) VALUES (
                    :partner_id, :spartner_id, :spartner_name, :spartner_tel, :spartner_addr, 
                    :spartner_addr2, :spartner_email, :spartner_role, :spartner_use
                )";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $stmt->bindParam(':spartner_id', $new_spartner_id);
        $stmt->bindParam(':spartner_name', $spartner_name);
        $stmt->bindParam(':spartner_tel', $spartner_tel);
        $stmt->bindParam(':spartner_addr', $spartner_addr);
        $stmt->bindParam(':spartner_addr2', $spartner_addr2);
        $stmt->bindParam(':spartner_email', $spartner_email);
        $stmt->bindParam(':spartner_role', $spartner_role, PDO::PARAM_INT);
        $stmt->bindParam(':spartner_use', $spartner_use);

        // 쿼리 실행
        $stmt->execute();

        // 성공 응답
        jsonResponse('success', '지자체가 성공적으로 등록되었습니다.');

    } catch (PDOException $e) {
        // PDO 예외 처리
        jsonResponse('error', '데이터 저장 중 오류가 발생했습니다.', $e->getMessage());
    }
} else {
    // 잘못된 요청 응답
    jsonResponse('error', '잘못된 요청입니다.');
}
