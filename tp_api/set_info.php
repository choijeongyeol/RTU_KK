<?php

echo "/html/tp_api/set_info.php 차단"; exit();

$default_Url = "http://43.200.77.82";

$appEUI = "0060261000000799"; //이트론
$uKey = "bmp3WWFyUzhBNmFLcEdicS9FUnJMMkNTN1lDYlZLdTBhaExEdWdoanUrdlZ4Sm9ZczduV09qTi9rUTZuaHBOcg==";//이트론


$appEUI = "0060231000001132"; //금강
$uKey = "RklKUVlXbUlQQUZySGJxWGkrV1E4Q1lic0dOZXdlSFdGRjFER2s2WFQrMkt0TktqYlZFV1p0RkUySmVrRS9hNA=="; //금강
  

// URL 형식 유효성 검사
if (!filter_var($default_Url, FILTER_VALIDATE_URL)) {
    die("Error: Invalid default URL.");
}

// 구독할 장치 LTID와 알림을 받을 URL 설정
$LTID = "00000799d02544fffef3ca7e";
$LTID = "00001132d02544fffef3b7ca"; // 금강 태양광 단상
$LTID = "00001132d02544fffef3b7bf"; // 금강 태양광 삼상
$LTID = "00001132d02544fffef3bd96"; // 금강 태양광 삼상(이득재)
$LTID = "00001132d02544fffef3ba9d"; // 금강 태양광 삼상(보그워너충주(유))
$LTID = "00001132d02544fffef3bad6"; // 금강전기산업



//$LTID = "00000799d02544fffef3b13f"; // 이트론 동양연사 
//$LTID = "00000799d02544fffef3b162"; // 이트론 동양연사 

// LTID가 설정되었는지 검사
if (empty($LTID)) {
    die("Error: LTID is not set.");
}


$subscription_key = "etrons_3";
$subscription_key = "kk_3"; // 금강 단상, 삼상
 

// 키가 설정되었는지 검사
if (empty($subscription_key)) {
    die("Error: subscription key is not set.");
}

$notification_ip = "http://43.200.77.82:80";

$notification_Url = $default_Url . "/tp_api/receive_notification.php";

 
// URL 형식 유효성 검사
if (!filter_var($notification_Url, FILTER_VALIDATE_URL)) {
    die("Error: Invalid notification URL.");
}


 
?>