<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/fn.php');



// 사용자 등록
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

    $admin_id = $_POST["admin_id"];
    $admin_pw = $_POST["admin_pw"];
    $role = $_POST["role"];

    $adminManager->registerUser($admin_id, $admin_pw, $role);
}
 
// 사용자 로그인
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {

    $admin_id = $_POST["admin_id"];
    $admin_pw = $_POST["admin_pw"];

    if ($adminManager->loginAdmin($admin_id, $admin_pw)) {

        header("Location: ./home/dashboard.php");
        exit();	
    } else {
        $loginError = "아이디 또는 비밀번호가 바르지 않습니다.";		
    }
	
}
?>
 
<!DOCTYPE html>
<html lang="ko-kr">
<head>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
<title>관리자 로그인</title>
<link rel="stylesheet" type="text/css" href="/gn/css/common.css">
<link rel="stylesheet" type="text/css" href="/gn/css/layout.css">
<script type="text/javascript" src="/gn/js/jquery-1.11.3.min.js"></script>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/default.php'); ?>
</head>

<SCRIPT type="text/javascript">
<!--
function checkIt() {


	if(document.myform.admin_id.value.length==0){ alert("아이디를 입력하세요"); document.myform.admin_id.focus();  return false;}
	if(document.myform.admin_pw.value.length==0){ alert("비밀번호를 입력하세요"); document.myform.admin_pw.focus(); return false;}
	document.myform.submit();
}


function setfocus(){
	document.myform.admin_id.focus();
}
//-->
</SCRIPT>

<body class="login">
 
<div class="login-wrap">

	<div class="login-header">
		<h1><font size="5" color="">Admin 로그인</font><!-- <img src="img/login/logo.png" alt=""> --></h1>	
	</div><!--//header -->

	<div class="login-container">
		<form method="post"  name="myform"><!-- action="login_ok.php"  -->
		<div class="login-form">
			<div class="input"><input type="text" name="admin_id" placeholder="아이디"></div>
			<div class="input"><input type="password" name="admin_pw" placeholder="비밀번호"></div>
			<div class="command"><input type="submit"  name="login" value="로그인"></div><!--  onClick="checkIt();" -->
		</div> 
		</form>
	    <?php if (isset($loginError)) {
				echo "<p style='color: red;'>$loginError</p>"; 
				echo "<script>setfocus();</script>";
		} ?>		
	</div>

	<div class="login-footer">
		<small>Copyright ⓒ 2024 wms. All Rights Reserved.</small>
	</div>

</div>
</body>
</html>