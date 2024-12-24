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
		
		
			<h1>메인 콘텐츠</h1>
			<p>여기에 본문 내용을 추가하세요.  <?php echo $_SESSION['admin_name']; ?></p>
			
			
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	