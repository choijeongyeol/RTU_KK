<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $as_id = isset($_POST['as_id']) ? $_POST['as_id'] : 0;
    $as_memo = isset($_POST['as_memo']) ? $_POST['as_memo'] : '';

    if ($as_id && $as_memo) {
        try {
            // 메모 업데이트 쿼리
            $sql = "UPDATE RTU_AS_Request SET as_memo = :as_memo WHERE as_id = :as_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':as_memo', $as_memo, PDO::PARAM_STR);
            $stmt->bindParam(':as_id', $as_id, PDO::PARAM_INT);
            $stmt->execute();

            echo "success";
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }
    } else {
        echo "error: Invalid input";
    }
} else {
    echo "error: Invalid request method";
}
?>
