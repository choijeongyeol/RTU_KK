<?
require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/set_info.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUT 요청 테스트</title>
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

    <h1>Device Reset</h1>
    <form id="resetForm">
        <label for="lTid">LTID:</label>
        <input type="text" id="lTid" name="lTid" required>
        <button type="submit">Reset Device</button>
    </form>

    <!-- 응답 결과를 표시할 영역 -->
    <div id="responseArea"></div>

<script>
    document.getElementById('resetForm').addEventListener('submit', function(event) {
        event.preventDefault(); // 폼 제출을 막음

        const lTid = document.getElementById('lTid').value;
		
        const apiUrl = "<?php echo $apiUrl; ?>";
		
        // PUT 요청 전송
        fetch(`${apiUrl}/tp_api/app_api.php?function=reset_device`, {		
            method: 'PUT',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `lTid=${encodeURIComponent(lTid)}`
        })
        .then(response => response.text())  // 응답을 먼저 텍스트로 받음
        .then(text => {
            // 응답을 화면에 출력
            const responseArea = document.getElementById('responseArea');
            responseArea.textContent = "Response:\n" + text;  // 응답을 화면에 표시
        })
        .catch(error => {
            console.error('Error:', error);  // 요청 전체 오류 처리
            const responseArea = document.getElementById('responseArea');
            responseArea.textContent = "Error occurred while resetting device.";  // 오류 메시지 화면 출력
        });
    });
</script>

</body>
</html>
