<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->beginTransaction(); // 트랜잭션 시작

        foreach ($_POST['status'] as $issue_id => $new_status) {
            // 해당 issue_id로 facility_id, status, four_hex 값을 조회
            $sql = "SELECT facility_id, status, four_hex FROM RTU_Issue_History_New WHERE id = :issue_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':issue_id', $issue_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $facility_id = $row['facility_id'];
                $current_status = $row['status'];
                $current_four_hex = $row['four_hex'];

                // 새로운 상태와 현재 상태가 다를 경우에만 업데이트
                if ($current_status !== $new_status) {
                    // 조회된 facility_id, status, four_hex 값이 일치하는 모든 레코드 업데이트
                    $update_sql = "
                        UPDATE RTU_Issue_History_New 
                        SET status = :new_status, updated_at = CURRENT_TIMESTAMP
                        WHERE facility_id = :facility_id 
                          AND status = :current_status 
                          AND four_hex = :four_hex
                    ";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bindParam(':new_status', $new_status);
                    $update_stmt->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);
                    $update_stmt->bindParam(':current_status', $current_status);
                    $update_stmt->bindParam(':four_hex', $current_four_hex);
                    $update_stmt->execute();
                }
            }
        }

        $conn->commit(); // 트랜잭션 커밋
        echo "장애 이력 해결여부가 성공적으로 업데이트되었습니다.";
    } catch (PDOException $e) {
        $conn->rollBack(); // 오류 시 롤백
        echo "<script>alert('업데이트 중 오류가 발생했습니다: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('잘못된 요청입니다.'); window.history.back();</script>";
}
?>
