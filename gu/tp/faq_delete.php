<?php require_once('./inc/setting_info.php'); // ����start,  // $root_dir ����  // $db_conn ��θ� ������ ����. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$faq_id = $_GET['id'];

$sql = "DELETE FROM RTU_FAQ WHERE faq_id = :faq_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':faq_id', $faq_id);
$stmt->execute();
header("Location: faq_list.php");
exit;
?>
