<?
require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/set_info.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>subscription Retrieve 테스트</title>
    <style>
        /* 응답 결과를 표시할 영역 스타일 */
        #responseArea {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            white-space: pre-wrap; /* 텍스트 줄바꿈을 유지 */
        }
    </style>
</head>
<body>
    <h1>subscription Retrieve</h1>
    <form id="resetForm" method="get" action="/tp_api/app_api.php">
        <label for="lTid">LTID:</label>
        <input type="text" id="lTid" name="lTid" required>
        <label for="subscription_1">subscription_1:</label>
        <input type="text" id="subscription_1" name="subscription_1" value="<?php echo $subscription_key;?>">
         
		<input type="hidden" name="function" value="getRetrieve_Subscription">
        <button type="submit">Retrieve</button>
    </form>
 </body>
</html>
<!--  value="http://43.200.77.82/rtu_api/thingplug_notification.php" -->
