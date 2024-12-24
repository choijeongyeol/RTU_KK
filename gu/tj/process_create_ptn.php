<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php'); // DB 연결

function getNextCodeID($conn) {
    // code_id가 4자리인 것 중 가장 큰 값에 +1
    $sql = "SELECT MAX(code_id) AS max_code_id FROM RTU_partner WHERE LENGTH(code_id) = 4";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $max_code_id = $row['max_code_id'] ?? 1236; // 값이 없으면 기본값 1236
    return str_pad($max_code_id + 1, 4, '0', STR_PAD_LEFT); // 4자리 유지
}

function getNextCompanyCode($conn) {
    // company_code가 1자리인 것 중 가장 큰 문자 그 다음 문자로 생성
    $sql = "SELECT MAX(company_code) AS max_company_code FROM RTU_partner WHERE LENGTH(company_code) = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $max_company_code = $row['max_company_code'] ?? 'A'; // 값이 없으면 A로 시작
    $next_code = chr(ord($max_company_code) + 1); // 다음 알파벳으로 변경
    if ($next_code > 'Z') {
        throw new Exception("company_code가 Z를 초과했습니다.");
    }
    return $next_code;
}

// POST 데이터 처리
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code_name = $_POST['code_name'] ?? null;
    $code_tel = $_POST['code_tel'] ?? null;

    if (empty($code_name) || empty($code_tel)) {
        die("필수 입력값이 누락되었습니다.");
    }

    try {
        $conn->beginTransaction();

        // 자동으로 값 생성
        $code_type = 'M'; // 고정값
        $code_id = getNextCodeID($conn); // 다음 code_id
        $company_code = getNextCompanyCode($conn); // 다음 company_code

        // 1. RTU_partner에 데이터 삽입
        $sql_partner = "INSERT INTO RTU_partner (code_type, code_id, code_name, code_tel, company_code, created_at, updated_at)
                        VALUES (:code_type, :code_id, :code_name, :code_tel, :company_code, NOW(), NOW())";

        $stmt_partner = $conn->prepare($sql_partner);
        $stmt_partner->execute([
            ':code_type' => $code_type,
            ':code_id' => $code_id,
            ':code_name' => $code_name,
            ':code_tel' => $code_tel,
            ':company_code' => $company_code
        ]);

        // 2. RTU_admin에 데이터 삽입
        $sql_admin = "INSERT INTO RTU_admin (
                        partner_id, spartner_id, admin_id, admin_pw, admin_name, admin_role, admin_rdate, admin_use, delYN
                      ) VALUES (
                        :partner_id, :spartner_id, :admin_id, :admin_pw, :admin_name, :admin_role, NOW(), :admin_use, :delYN
                      )";

        $stmt_admin = $conn->prepare($sql_admin);
        $stmt_admin->execute([
            ':partner_id' => $code_id, // 방금 삽입한 code_id
            ':spartner_id' => 0,      // 고정값 0
            ':admin_id' => 'admin',   // 관리자 ID
            ':admin_pw' => '$2y$10$WcQesWZyz/kCb.FEpyiRXu/kcOU3FOvedU9kGGO/waU2iff/aoxKO', // 비밀번호 해시
            ':admin_name' => '관리자', // 관리자 이름
            ':admin_role' => 40,      // 관리자 역할
            ':admin_use' => 'Y',      // 사용 여부
            ':delYN' => 'N'           // 삭제 여부
        ]);


        // 3. RTU_Configuration 데이터 삽입
        $sql_Configuration = "INSERT INTO RTU_Configuration ( partner_id
                      ) VALUES (  :partner_id
                      )";

        $stmt_Configuration = $conn->prepare($sql_Configuration);
        $stmt_Configuration->execute([
            ':partner_id' => $code_id // 방금 삽입한 code_id
        ]);

        $conn->commit();

        // 성공 메시지 및 리다이렉트
        echo "<script>
                alert('성공적으로 등록되었습니다.');
                window.location.href = 'create_partner_list.php';
              </script>";

    } catch (Exception $e) {
        $conn->rollBack();
        echo "오류가 발생했습니다: " . $e->getMessage();
    }
} else {
    echo "잘못된 접근입니다.";
}
?>
