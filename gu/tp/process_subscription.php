<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/ThingPlugAPI_only.php');

 
        // LTID 만들기
        $lastEightChars = substr($appEUI, -8);
        $LTID = $lastEightChars . $_GET['lora_id'];

?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>Subscription Delete</title>
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
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>

    <h1>Subscription Delete</h1>
    <form id="FM">
        <label for="lTid">LTID:</label>
        <input type="text" id="lTid" name="lTid" required  value="<?php echo $LTID;?>">
        <label for="subscription_1">Subscription 1:</label>
        <input type="text" id="subscription_1" name="subscription_1" value="<?php echo $subscription_key;?>"><br><br>
        
        <button type="submit">Delete subscription</button>
    </form>

    <!-- 응답 결과를 표시할 영역 -->
    <div id="responseArea"></div>

<script>
    document.getElementById('FM').addEventListener('submit', function(event) {
        event.preventDefault(); // 폼 제출을 막음

        const lTid = document.getElementById('lTid').value;
        const subscription_1 = document.getElementById('subscription_1').value;
		
        // PHP에서 전달된 변수 사용
        const apiUrl = "<?php echo $notification_ip; ?>";
		
        // DELETE 요청 전송
        fetch(`${apiUrl}/tp_api/app_api.php?function=delete_subscription`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `lTid=${encodeURIComponent(lTid)}&subscription_1=${encodeURIComponent(subscription_1)}`
        })
        .then(response => response.text())  // 응답을 먼저 텍스트로 받음
        .then(text => {
            // 응답을 화면에 출력
            const responseArea = document.getElementById('responseArea');
            responseArea.textContent = text;  // 응답을 화면에 표시
        })
        .catch(error => {
            console.error('Error:', error);  // 요청 전체 오류 처리
            const responseArea = document.getElementById('responseArea');
            responseArea.textContent = "Error occurred while deleting subscription.";  // 오류 메시지 화면 출력
        });
    });
</script>

		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	