<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['issue_type_id']) && !empty($_POST['new_issue_name'])) {
    $issue_type_id = $_POST['issue_type_id'];
    $new_issue_name = $_POST['new_issue_name'];
    try {
        $sql = "UPDATE RTU_issue_type SET issue_name = :issue_name WHERE issue_type_id = :issue_type_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':issue_name', $new_issue_name, PDO::PARAM_STR);
        $stmt->bindParam(':issue_type_id', $issue_type_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: issue_history_admin_cate.php");
    } catch (PDOException $e) {
        echo "데이터베이스 오류: " . $e->getMessage();
    }
}
?>
