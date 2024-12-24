<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$category_id = $_GET['id'];

$sql = "DELETE FROM RTU_FAQ_Category WHERE category_id = :category_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':category_id', $category_id);
$stmt->execute();
header("Location: faq_category_list.php");
exit;
?>
