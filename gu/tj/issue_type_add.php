<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['new_issue_name'])) {
    $new_issue_name = $_POST['new_issue_name'];
    try {
        $sql = "INSERT INTO RTU_issue_type (issue_name) VALUES (:issue_name)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':issue_name', $new_issue_name, PDO::PARAM_STR);
        $stmt->execute();
        header("Location: issue_history_admin_cate.php");
    } catch (PDOException $e) {
        echo "데이터베이스 오류: " . $e->getMessage();
    }
}
?>
