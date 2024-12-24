<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2단 레이어 메뉴 + 고정 로고</title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        /* 좌측 상단 고정 로고 */
        .logo {
            position: fixed; /* 화면 고정 */
            top: 10px; /* 상단 여백 */
            left: 10px; /* 좌측 여백 */
            font-size: 24px;
            font-weight: bold;
            color: #0033cc; /* 파란색 */
            z-index: 100; /* 메뉴 위에 표시 */
        }

        .logo span {
            color: #000; /* 검은색 */
        }

        .logo::before {
            content: "↩"; /* 화살표 추가 */
            margin-right: 5px;
            font-size: 20px;
            color: #0033cc;
        }

        header {
            background-color: #f8f8f8;
            padding: 10px 0; /* 상단 간격 */
            border-bottom: 1px solid #ddd;
        }

        nav {
            display: flex;
            justify-content: center; /* 메뉴를 화면 가운데 정렬 */
            align-items: center; /* 수직 정렬 */
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex; /* 수평 정렬 */
        }

        nav ul li {
            margin: 0 20px; /* 좌우 간격 */
            position: relative; /* 드롭다운 기준 위치 */
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        nav ul li ul {
            display: none; /* 숨김 */
            position: absolute;
            top: 100%; /* 부모 아래 위치 */
            left: 0;
            background-color: white;
            border: 1px solid #ddd;
            padding: 10px 0;
            width: 150px;
            z-index: 10; /* 드롭다운 메뉴가 다른 요소 위로 오도록 설정 */
        }

        nav ul li:hover ul {
            display: block; /* 부모 hover 시 표시 */
        }

        nav ul li ul li a {
            display: block;
            padding: 5px 20px;
            text-decoration: none;
            color: #333;
        }

        nav ul li ul li a:hover {
            background-color: #f0f0f0; /* 하위 메뉴 hover 배경 */
        }
    </style>
</head>
<body>
    <!-- 좌측 상단 고정 로고 -->
    <div class="logo">
        Re<span>new</span>
    </div>

    <header>
        <nav>
            <ul>
                <li>
                    <a href="#">대시보드</a>
                    <ul>
                        <li><a href="#">기업소개</a></li>
                        <li><a href="#">연혁</a></li>
                        <li><a href="#">주요 실적</a></li>
                        <li><a href="#">인증서</a></li>
                        <li><a href="#">오시는길</a></li>
                    </ul>
                </li>
                <li><a href="#">모니터링</a></li>
                <li><a href="#">통계분석</a></li>
                <li><a href="#">게시판</a></li>
                <li><a href="#">설정</a></li>
            </ul>
        </nav>
    </header>
</body>
</html>
