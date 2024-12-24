<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

// 로그 파일에 기록하는 함수
function log_to_file($message) {
    $log_file = $_SERVER['DOCUMENT_ROOT'].'/tp_api/logs/notification_log.txt';
    $result = file_put_contents($log_file, date("Y-m-d H:i:s") . " - " . $message . "\n", FILE_APPEND | LOCK_EX);
    if ($result === false) {
        error_log("Failed to write log: " . $message);
    }
}

// SQL 오류 로그를 기록하는 함수
function log_sql_error($stmt, $query_description) {
    $error_info = $stmt->errorInfo();
    log_to_file("$query_description failed. SQLSTATE: " . $error_info[0] . ", Error Code: " . $error_info[1] . ", Message: " . $error_info[2]);
}

// 서버 환경 정보 확인
log_to_file("Server received request at: " . date("Y-m-d H:i:s"));
log_to_file("Request Method: " . $_SERVER['REQUEST_METHOD']);
log_to_file("Request Headers: " . print_r(getallheaders(), true));

// DB 연결
$conn = getDbConnection();
if (!$conn) {
    log_to_file("Database connection failed");
    exit();
}

// 트랜잭션 시작
$conn->beginTransaction();

try {
    // php://input에서 데이터를 확인 (ThingPlug 전송 데이터)
    $request_body = file_get_contents('php://input');

    if (!$request_body) {
        log_to_file("No data received in request body");
        $conn->rollBack();  // 실패 시 롤백
        exit();
    }

    log_to_file("Received Notification: " . $request_body);

    // 수신 데이터가 XML인지 확인
    if (strpos($request_body, '<?xml') !== false) {
        // XML 파싱 처리
        libxml_use_internal_errors(true);
        $xml_data = simplexml_load_string($request_body);

        if ($xml_data === false) {
            $xml_errors = libxml_get_errors();
            $error_message = "Failed to parse XML: ";
            foreach ($xml_errors as $error) {
                $error_message .= $error->message . " ";
            }
            log_to_file($error_message);
            libxml_clear_errors();
            $conn->rollBack();  // 실패 시 롤백
            exit();
        }

        // XML 데이터를 JSON으로 변환하여 저장
        $xml_content = json_encode($xml_data);

        // RTU_lora_raw 테이블에 데이터 삽입
        $stmt = $conn->prepare("INSERT INTO RTU_lora_raw (type, con, rdate, ltid) VALUES ('xml', :con_row, :rdate, :ltid)");

        // ltid 필드를 추가하여 3번째 값에서 'remoteCSE-'를 제거하여 저장
        $sr_value = $xml_data->sr ?? null;
        $ltid = null;

        if ($sr_value) {
            $sr_parts = explode('/', $sr_value);
            $ltid = isset($sr_parts[3]) ? str_replace('remoteCSE-', '', $sr_parts[3]) : "test";
        }
        
        $stmt->bindParam(':con_row', $xml_content);
        $stmt->bindParam(':rdate', date("Y-m-d H:i:s"));
        $stmt->bindParam(':ltid', $ltid);

        if (!$stmt->execute()) {
            log_sql_error($stmt, "Insert XML data into RTU_lora_raw");
            $conn->rollBack();  // 실패 시 롤백
            exit();
        } else {
            log_to_file("XML data inserted successfully into RTU_lora_raw.");
        }
    }

    $conn->commit();

} catch (PDOException $e) {
    $conn->rollBack();
    log_to_file("Transaction failed: " . $e->getMessage());
}
?>
