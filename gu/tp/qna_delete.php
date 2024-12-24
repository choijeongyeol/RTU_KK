<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$inquiry_id = $_GET['id'];

$sql = "DELETE FROM RTU_Inquiry WHERE inquiry_id = :inquiry_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':inquiry_id', $inquiry_id, PDO::PARAM_INT);
$stmt->execute();
header("Location: qna_list.php");
exit;
?>
