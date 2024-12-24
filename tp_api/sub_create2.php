<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Create 테스트</title>
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // URL에서 ltid 파라미터 값 가져오기
            const urlParams = new URLSearchParams(window.location.search);
            const ltid = urlParams.get('ltid');

            // lTid 입력 필드에 ltid 값 설정
            if (ltid) {
                document.getElementById('lTid').value = ltid;

                // 폼 자동 제출
                document.getElementById('resetForm').submit();
            }
        });
    </script>
</head>
<body>
    <h1>Subscription Create</h1>
    <form id="resetForm" method="post" action="/tp_api/app_api.php">
        <label for="lTid">LTID:</label>
        <input type="text" id="lTid" name="lTid" required>
        <input type="hidden" name="function" value="create_subscription">
        <button type="submit">Create</button>
    </form>
</body>
</html>
