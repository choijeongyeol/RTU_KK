<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>메뉴</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 10px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #000;
			font-size:14px;
        }
        a:hover {
            color: #007BFF;
        }
    </style>
</head>
<body>
    <h2>관리메뉴</h2>
    <ul>
        <li><a href="technician_list.php" target="mainFrame">담당자 목록</a></li>
        <li><a href="technician_register.php" target="mainFrame">담당자 등록/수정</a></li>
        <li>---------------</li>
        <li><a href="user_list.php" target="mainFrame">이용자 목록</a></li>
        <li><a href="user_form.php" target="mainFrame">이용자 등록</a></li>
        <li><a href="lora_form.php" target="mainFrame">로라 등록</a></li>
        <li><a href="facility_form.php" target="mainFrame">설비 등록</a></li>	
        <li>---------------</li>
		<li><a href="/gu/view/list_sunlight_0101.php" target="mainFrame">태양광 단상목록</a></li>
        <li><a href="list_user_lora_cid.php" target="mainFrame">이용자별 로라 / CID 목록</a></li>
        <li>---------------</li>
        <li><a href="issue_history_admin_cate.php" target="mainFrame">장애 이력 카테고리관리</a></li>
        <li><a href="issue_history_admin_input.php" target="mainFrame">장애 이력 입력</a></li>
        <li><a href="issue_history_list.php" target="mainFrame">장애 이력 목록</a></li>
        <li><a href="as_request_list.php" target="mainFrame">장애 AS신청 목록</a></li>
        <li><a href="as_request.php" target="mainFrame">AS신청하기(고객테스트)</a></li>
        <li>---------------</li>
        <li><a href="notice_list.php" target="mainFrame">공지사항</a></li>
        <li><a href="notice_write.php" target="mainFrame">공지사항 등록</a></li>
        <li><a href="faq_category_list.php" target="mainFrame">FAQ 카테고리 관리</a></li>
        <li><a href="faq_list.php" target="mainFrame">FAQ 내용 관리</a></li>

        <li><a href="qna_list.php" target="mainFrame">QNA 목록 관리</a></li>
        <li><a href="qna_add.php" target="mainFrame">QNA 등록(고객테스트)</a></li>
        <li>---------------</li> 
        <li><a href="daily_statistics.php" target="mainFrame">일별 통계 분석</a></li>
        <li><a href="monthly_statistics.php" target="mainFrame">월별 통계 분석</a></li>
 
	
	
	</ul>
	
    <!-- <h2>유저메뉴</h2>
    <ul>
        <li><a href="user_form.php" target="mainFrame">조회</a></li>
    </ul>	 -->
</body>
</html>
 