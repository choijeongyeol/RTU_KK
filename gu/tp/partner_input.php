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
		<h1>관리자 등록</h1>
		<form action="process_partner.php" method="post">
			<!-- <label for="partner_id">파트너 ID:</label>
			<input type="text" id="partner_id" name="partner_id" required><br><br> -->

			<label for="admin_id">관리자 ID:</label>
			<input type="text" id="admin_id" name="admin_id" required><br><br>

			<label for="admin_name">관리자 이름:</label>
			<input type="text" id="admin_name" name="admin_name" required><br><br>

			<label for="admin_pw">비밀번호:</label>
			<input type="password" id="admin_pw" name="admin_pw" required><br><br>
            
			<input type="hidden" name="admin_role" value="40">

			<!-- <label for="admin_role">관리자 역할:</label>
			<select id="admin_role" name="admin_role">
				<option value="100">관리자</option>
				<option value="9">일반 관리자</option>
			</select><br><br> -->

			<label for="admin_use">사용 여부:</label>
			<select id="admin_use" name="admin_use">
				<option value="Y">사용</option>
				<option value="N">사용 안 함</option>
			</select><br><br>

			<button type="submit">등록</button>
		</form>
		</main>
		 <!-- 본문 내용 끝. -->

<?php require_once($root_dir.'inc/footer.php'); ?>	