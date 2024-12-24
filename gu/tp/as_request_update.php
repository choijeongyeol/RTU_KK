<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        
        foreach ($_POST['issue_status'] as $as_id => $issue_status) {
            // 현재 이슈 상태와 담당자 정보를 가져옵니다
            $sql = "SELECT ih.status, ar.technician_id FROM RTU_Issue_History_New ih
                    JOIN RTU_AS_Request ar ON ih.id = ar.issue_id WHERE ar.as_id = :as_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':as_id', $as_id, PDO::PARAM_INT);
            $stmt->execute();
            $current = $stmt->fetch(PDO::FETCH_ASSOC);

            // RTU_Issue_History_New의 이슈 상태 업데이트
            $update_issue_sql = "UPDATE RTU_Issue_History_New SET status = :issue_status WHERE id = (SELECT issue_id FROM RTU_AS_Request WHERE as_id = :as_id)";
            $update_issue_stmt = $conn->prepare($update_issue_sql);
            $update_issue_stmt->bindParam(':issue_status', $issue_status, PDO::PARAM_STR);
            $update_issue_stmt->bindParam(':as_id', $as_id, PDO::PARAM_INT);
            $update_issue_stmt->execute();

            // 처리예정 날짜 값 가져오기, 빈 문자열일 경우 NULL로 설정
            $reservationDate = !empty($_POST['reservation_date'][$as_id]) ? $_POST['reservation_date'][$as_id] : null;

            // RTU_AS_Request의 as_status, 담당자 정보, 수정 날짜, 예약 날짜 업데이트
            $completionDate = ($issue_status == '4') ? date('Y-m-d H:i:s') : null;

            $update_as_sql = "
                UPDATE RTU_AS_Request
                SET technician_id = :technician_id, as_status = :as_status, updated_at = NOW(),
                    completion_date = :completion_date, reservation_date = :reservation_date
                WHERE as_id = :as_id
            ";
            $update_as_stmt = $conn->prepare($update_as_sql);
            $update_as_stmt->bindParam(':technician_id', $_POST['technician'][$as_id], PDO::PARAM_STR);
            $update_as_stmt->bindParam(':as_status', $issue_status, PDO::PARAM_STR);
            $update_as_stmt->bindParam(':as_id', $as_id, PDO::PARAM_INT);

            if ($completionDate !== null) {
                $update_as_stmt->bindParam(':completion_date', $completionDate, PDO::PARAM_STR);
            } else {
                $update_as_stmt->bindValue(':completion_date', null, PDO::PARAM_NULL);
            }

            if ($reservationDate !== null) {
                $update_as_stmt->bindParam(':reservation_date', $reservationDate, PDO::PARAM_STR);
            } else {
                $update_as_stmt->bindValue(':reservation_date', null, PDO::PARAM_NULL);
            }

            $update_as_stmt->execute();
        }

        $conn->commit();
        echo "<script>alert('업데이트가 완료되었습니다.'); window.location.href = 'as_request_list.php';</script>";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "업데이트 중 오류가 발생했습니다: " . $e->getMessage();
    }
} else {
    echo "잘못된 요청입니다.";
}
