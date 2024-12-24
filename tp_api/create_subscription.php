<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/ThingPlugAPI.php');

$thingPlug = new ThingPlugAPI($appEUI, $uKey);

// 구독 생성
$response = $thingPlug->createSubscription($LTID, $notification_Url);

if ($response) {
    echo "Subscription created successfully.";
} else {
    echo "Failed to create subscription.";
}
?>
