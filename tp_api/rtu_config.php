<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/fn_api_RTU.php');
 
// 현재 접속자의 partner_id 가져오기
if (!isset($_SESSION['partner_id'])) {
    die("Error: Partner ID not found in session.");
}


if (isset($_GET['lora_id'])) {
	$lora_id = $_GET['lora_id'];
}elseif (isset($_POST['lora_id'])) {
	$lora_id = $_POST['lora_id'];
}else{
	$lora_id = "";
}

$partner_id = $_SESSION['partner_id'];
$config = get_RTU_Config($partner_id);

if (!$config) {
    die("Error: No configuration found for partner ID: $partner_id");
}

// 변수 설정
$appEUI = $config['app_eui'];
$uKey = $config['u_key'];
$subscription_key = $config['subscription_key'];
$rtu_companyname = $config['partner_name'];
$default_Url = $config['default_url'];
$notification_ip = $config['notification_ip'];
 
// URL 형식 유효성 검사
if (!filter_var($default_Url, FILTER_VALIDATE_URL)) {
    die("Error: Invalid default URL.");
}
 
// LTID 만들기
//$lastEightChars = substr($appEUI, -8);
//$LTID = $lastEightChars . $lora_id; 

// LTID가 설정되었는지 검사
//if (empty($LTID)) {
//    die("Error: LTID is not set.");
//}
 
// 키가 설정되었는지 검사
if (empty($subscription_key)) {
    die("Error: subscription key is not set.");
}
 

$notification_Url = $default_Url . "/tp_api/receive_notification.php";

 
// URL 형식 유효성 검사
if (!filter_var($notification_Url, FILTER_VALIDATE_URL)) {
    die("Error: Invalid notification URL.");
}
 
?>