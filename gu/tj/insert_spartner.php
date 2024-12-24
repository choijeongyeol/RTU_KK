<?php 
require_once('./inc/setting_info.php'); // 세션 start, $root_dir 지정
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php'); // 데이터베이스 연결

// 공통 JSON 응답 함수
function jsonResponse($status, $message, $error = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($error) {
        $response['error'] = $error;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

// 알파벳 증가 함수: AA -> AB -> ... -> AZ -> BA
function getNextCompanyCode($currentCode) {
    if ($currentCode == null) {
        return 'AA';
    }

    $letters = str_split($currentCode);
    $letters[1]++; // 마지막 자리부터 증가

    if ($letters[1] > 'Z') {
        $letters[1] = 'A';
        $letters[0]++;
    }

    if ($letters[0] > 'Z') {
        throw new Exception("Company code overflow");
    }

    return implode('', $letters);
}

// POST 요청 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();

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

    if (empty($spartner_name) || empty($spartner_tel) || empty($spartner_addr) || empty($spartner_email)) {
        jsonResponse('error', '필수 입력값이 누락되었습니다.');
    }

    if (!filter_var($spartner_email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse('error', '유효하지 않은 이메일 형식입니다.');
    }

    try {
        $conn->beginTransaction();

        // 1. RTU_spartner에 INSERT
        $sql = "SELECT spartner_id FROM RTU_spartner WHERE partner_id = :partner_id ORDER BY spartner_id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $stmt->execute();
        $last_spartner_id = $stmt->fetchColumn();

        $new_number = $last_spartner_id ? str_pad((int)substr($last_spartner_id, -3) + 1, 3, '0', STR_PAD_LEFT) : '001';
        $new_spartner_id = $partner_id . $new_number;

        $sql = "INSERT INTO RTU_spartner (partner_id, spartner_id, spartner_name, spartner_tel, spartner_addr, 
                spartner_addr2, spartner_email, spartner_role, spartner_use) 
                VALUES (:partner_id, :spartner_id, :spartner_name, :spartner_tel, :spartner_addr, 
                :spartner_addr2, :spartner_email, :spartner_role, :spartner_use)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':partner_id' => $partner_id,
            ':spartner_id' => $new_spartner_id,
            ':spartner_name' => $spartner_name,
            ':spartner_tel' => $spartner_tel,
            ':spartner_addr' => $spartner_addr,
            ':spartner_addr2' => $spartner_addr2,
            ':spartner_email' => $spartner_email,
            ':spartner_role' => $spartner_role,
            ':spartner_use' => $spartner_use
        ]);

        // 2. RTU_partner에 INSERT하기 위한 company_code 생성
        $sql = "SELECT company_code FROM RTU_partner ORDER BY company_code DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $last_company_code = $stmt->fetchColumn();
        $new_company_code = getNextCompanyCode($last_company_code);

        // RTU_partner에 INSERT
        $sql = "INSERT INTO RTU_partner (code_type, code_id, code_name, code_tel, company_code) 
                VALUES ('S', :code_id, :code_name, :code_tel, :company_code)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':code_id' => $new_spartner_id,
            ':code_name' => $spartner_name,
            ':code_tel' => $spartner_tel,
            ':company_code' => $new_company_code
        ]);

        $conn->commit();
        jsonResponse('success', '지자체와 파트너가 성공적으로 등록되었습니다.', null, 'spartner_list.php');

    } catch (Exception $e) {
        $conn->rollBack();
        jsonResponse('error', '데이터 저장 중 오류가 발생했습니다.', $e->getMessage());
    }
} else {
    jsonResponse('error', '잘못된 요청입니다.');
}
