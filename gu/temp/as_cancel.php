<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// POST로 전달된 AS ID 가져오기
$as_id = isset($_POST['as_id']) ? $_POST['as_id'] : 0;

if ($as_id > 0) {
    try {
        $sql = "
            UPDATE RTU_AS_Request ar
            JOIN RTU_Issue_History_New ih ON ar.issue_id = ih.id
            SET ar.as_status = 5, ih.status = 5
            WHERE ar.as_id = :as_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':as_id', $as_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "failed";
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
} else {
    echo "invalid_id";
}
