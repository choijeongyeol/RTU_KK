<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$notice_id = $_GET['id'];

$sql = "UPDATE RTU_Notice SET delYN = 'Y' WHERE notice_id = :notice_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':notice_id', $notice_id, PDO::PARAM_INT);
$stmt->execute();

header("Location: notice_list.php");
exit;
?>
