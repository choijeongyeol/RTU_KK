<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
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
        <li><a>[ <? echo $_SESSION['admin_name'];?> ]</a></li>
        <li><a href="logout.php" target="_top">로그아웃</a></li>
        
		
		<? if ($_SESSION['spartner_id'] < 1000) { ?>
        <li><a href="partner_list.php">관리자관리</a></li>
        
		<? } ?>
        <li><a href="spartner_list.php">지자체관리</a></li>
        
        <li><a href="user_list.php">이용자 관리</a></li>
        <!-- <li><a href="user_form.php">이용자 등록</a></li> -->
        <li><a href="lora_form.php">로라 등록</a></li>
        <li><a href="facility_form.php">설비관리</a></li>	
        <!-- <li><a href="facility_subscription.php">설비연결관리</a></li>	 -->
        
		<li><a href="/gu/view/list_sunlight_0101.php">태양광 단상목록</a></li>
		<li><a href="/gu/view/list_sunlight_0103.php">태양광 삼상목록</a></li>
        <li><a href="list_user_lora_cid.php">이용자별 로라 / CID 목록</a></li>
        
        <li><a href="issue_history_admin_cate.php">장애 이력 카테고리관리</a></li>
        <li><a href="issue_history_admin_input.php">장애 이력 입력</a></li>
        <li><a href="issue_history_list.php">장애 이력 목록</a></li>
        <li><a href="as_request_list.php">장애 AS신청 목록</a></li>
        <li><a href="as_request.php">AS신청하기(고객테스트)</a></li>
        
        <li><a href="technician_list.php">장애처리담당자관리</a></li>
        
		
        <li><a href="notice_list.php">공지사항</a></li>
        <li><a href="notice_write.php">공지사항 등록</a></li>
        <li><a href="faq_category_list.php">FAQ 카테고리 관리</a></li>
        <li><a href="faq_list.php">FAQ 내용 관리</a></li>

        <li><a href="qna_list.php">QNA 목록 관리</a></li>
        <li><a href="qna_add.php">QNA 등록(고객테스트)</a></li>
         
        <li><a href="daily_statistics.php">일별 통계 분석</a></li>
        <li><a href="monthly_statistics.php">월별 통계 분석</a></li>
         
        <li><a href="weather_js.php">기상정보</a></li>
	
	</ul>
	
    <!-- <h2>유저메뉴</h2>
    <ul>
        <li><a href="user_form.php">조회</a></li>
    </ul>	 -->
</body>
</html>
 