<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/ThingPlugAPI.php');
 
// ThingPlug API 설정
//$appEUI = "0060261000000799";
//$uKey = "bmp3WWFyUzhBNmFLcEdicS9FUnJMMkNTN1lDYlZLdTBhaExEdWdoanUrdlZ4Sm9ZczduV09qTi9rUTZuaHBOcg==";

// 오류 로깅 추가
error_log("Initializing ThingPlugAPI...");

try {
    $thingPlug = new ThingPlugAPI($appEUI, $uKey);

    // 구독 요청을 보낼 장치의 LTID와 통지 URL 설정
    //$LTID = "00000799d02544fffef3ca7e";
    //$notification_Url = "http://43.200.77.82/tp_api/receive_notification.php";

    error_log("Sending subscription request for LTID: $LTID");

    // 구독 요청 보내기
    $subscriptionResponse = $thingPlug->createSubscription($LTID, $notification_Url);
 
    // 구독 응답을 확인하고 로그로 기록
    if ($subscriptionResponse) {
        error_log("Subscription created successfully: " . print_r($subscriptionResponse, true));
        echo "Subscription created successfully: " . print_r($subscriptionResponse, true);
    } else {
        error_log("Failed to create subscription. No response or invalid response from ThingPlug API.");
        echo "Failed to create subscription.";
    }
} catch (Exception $e) {
    // 예외 처리 및 로그 기록
    error_log("Exception occurred during subscription: " . $e->getMessage());
    echo "An error occurred: " . $e->getMessage();
}
?>
