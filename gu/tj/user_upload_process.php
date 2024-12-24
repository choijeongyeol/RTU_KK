<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/setting_info.php'); // 세션 start
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php'); // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// JSON 응답 함수
function jsonResponse($status, $message, $error = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($error) {
        $response['error'] = $error;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

// 파일 업로드 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    if (!$file) {
        jsonResponse('error', '파일 업로드에 실패했습니다.');
    }

    try {
        // 엑셀 파일 읽기
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // 첫 번째 행(헤더)은 건너뜀
        unset($rows[0]);

        $conn->beginTransaction(); // 트랜잭션 시작

        foreach ($rows as $row) {
            // 엑셀 데이터 매핑
            $partner_id = $_SESSION['partner_id'] ?? null;
            $user_name = trim($row[0]);
            $user_pw = trim($row[1]);
            $user_phone = trim($row[2]);
            $user_addr = trim($row[3]);
            $user_addr2 = trim($row[4]);
            $user_email = trim($row[5]);
            $legalcode = trim($row[6]);

            // 필수값 확인
            if (empty($partner_id) || empty($user_name) || empty($user_pw) || empty($user_phone) || empty($user_addr) || empty($user_email)) {
                throw new Exception('필수 데이터가 누락되었습니다.');
            }

            // 위도와 경도 가져오기
            $coordinates = getCoordinates($user_addr);
            if (isset($coordinates['error'])) {
                throw new Exception($coordinates['error']);
            }

            $latitude = $coordinates['latitude'];
            $longitude = $coordinates['longitude'];

            // 비밀번호 암호화
            $hashed_pw = password_hash($user_pw, PASSWORD_DEFAULT);

            // UID 생성
            $user_id = generateUID($conn, $legalcode);

            // 데이터 삽입
            $sql = "INSERT INTO RTU_user (
                        partner_id, user_id, user_name, user_pw, user_tel, legalcode, 
                        user_addr, user_addr2, user_email, email_receive, spartner_id, 
                        user_token, latitude, longitude
                    ) VALUES (
                        :partner_id, :user_id, :user_name, :user_pw, :user_phone, :legalcode, 
                        :user_addr, :user_addr2, :user_email, 1, :partner_id, 
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
            $stmt->bindParam(':user_token', $user_token);
            $stmt->bindParam(':latitude', $latitude);
            $stmt->bindParam(':longitude', $longitude);

            $stmt->execute();
        }

        $conn->commit(); // 트랜잭션 커밋
        jsonResponse('success', '엑셀 데이터가 성공적으로 업로드되었습니다.');

    } catch (Exception $e) {
        $conn->rollBack(); // 트랜잭션 롤백
        jsonResponse('error', '업로드 중 오류가 발생했습니다.', $e->getMessage());
    }
} else {
    jsonResponse('error', '잘못된 요청입니다.');
}
?>
