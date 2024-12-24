<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>subscription Create 테스트</title>
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
    <h1>subscription Create</h1>
    <form id="resetForm" method="post" action="/tp_api/app_api.php">
        <label for="lTid">LTID:</label>
        <input type="text" id="lTid" name="lTid" required>
        <label for="notification_Url">noti_url:</label>
        <!-- <input type="text" id="notification_Url" name="notification_Url" required value="http://43.200.77.82/"> -->
		<input type="hidden" name="function" value="create_subscription">
        <button type="submit">Create</button>
    </form>
 </body>
</html>
<!--  value="http://43.200.77.82/rtu_api/thingplug_notification.php" -->
<!-- {"status":201,"message":"Subscription created successfully.","data":{"ty":"23","ri":"SS00000000000009493443","rn":"kk_3","pi":"CT00000000000001288251","ct":"2024-10-07T10:17:47+09:00","lt":"2024-10-07T10:17:47+09:00","enc":{"rss":"1"},"nu":"HTTP|","nct":"2"}} -->
