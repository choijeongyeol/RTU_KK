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
		<h1>지자체 생성</h1>
		<form action="insert_spartner.php" method="post">
			<label for="spartner_name">지자체 이름:</label>
			<input type="text" id="spartner_name" name="spartner_name" required><br><br>

			<label for="spartner_tel">전화번호:</label>
			<input type="text" id="spartner_tel" name="spartner_tel" required><br><br>

			<label for="spartner_addr">주소:</label>
			<input type="text" id="spartner_addr" name="spartner_addr" required><br><br>

			<label for="spartner_addr2">상세 주소:</label>
			<input type="text" id="spartner_addr2" name="spartner_addr2"><br><br>

			<label for="spartner_email">이메일:</label>
			<input type="email" id="spartner_email" name="spartner_email" required><br><br>

			<label for="spartner_role">지자체 역할:</label>
			<select id="spartner_role" name="spartner_role">
				<option value="1" selected>기본 역할</option>
				<option value="2">특별 역할</option>
			</select><br><br>

			<label for="spartner_use">사용 여부:</label>
			<select id="spartner_use" name="spartner_use">
				<option value="Y" selected>사용</option>
				<option value="N">사용 안 함</option>
			</select><br><br>

			<button type="submit">등록</button>
		</form>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	