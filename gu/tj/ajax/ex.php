<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>AJAX 팝업 예제</title>
    <style>
        /* 팝업 스타일 */
        #popup {
            display: none; /* 초기에는 숨김 */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            padding: 20px;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        #overlay {
            display: none; /* 초기에는 숨김 */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <!-- 팝업 오버레이 -->
    <div id="overlay"></div>

    <!-- 팝업 창 -->
    <div id="popup">
        <h3>팝업</h3>
        <div id="popupContent">로딩 중...</div>
        <button onclick="closePopup()">닫기</button>
    </div>

    <!-- 버튼 -->
    <button id="loadPopup">팝업 띄우기</button>

    <!-- Script -->
    <script>
        document.getElementById('loadPopup').addEventListener('click', function () {
            // AJAX 요청
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'data.php?a=11', true); // 서버에서 데이터를 가져올 URL (data.php)
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // 팝업에 데이터 삽입
                    document.getElementById('popupContent').innerHTML = xhr.responseText;

                    // 팝업 및 오버레이 표시
                    document.getElementById('popup').style.display = 'block';
                    document.getElementById('overlay').style.display = 'block';
                }
            };
            xhr.send();
        });

        // 팝업 닫기 함수
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
</body>
</html>
