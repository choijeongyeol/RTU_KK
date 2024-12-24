<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php //require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php'); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php'); // DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다) ?>
<? 
if (!isset($_SESSION['admin_idx'])) {
    header("Location: ".$top_dir.$kk_admin_dir."login.php");
    exit();
}
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
 
		 <!-- 본문 내용 시작 -->
		<main>
		<h1>기업 생성</h1>

    <form action="process_create_ptn.php" method="POST">
        <label for="code_name">기업 이름:</label>
        <input type="text" id="code_name" name="code_name" required><br><br>
        
        <label for="code_tel">기업 연락처:</label>
        <input type="text" id="code_tel" name="code_tel" required><br><br>

        <button type="submit">저장</button>
    </form>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	