<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');
 
  
// 사용자 로그인
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {

    $partner_id = $_POST["partner_id"];
    $admin_id = $_POST["admin_id"];
    $admin_pw = $_POST["admin_pw"];


    if ($partner_id=="1111") {
		if ($adminManager->sysloginAdmin($partner_id, $admin_id, $admin_pw)) {
			if ($partner_id=="1111") {
				header("Location: ./p01/partner_list.php");
				exit();				
			}else{
				$loginError = "로그인에 실패하였습니다.";	
				exit();				
			}
		} else {
			 $loginError = "로그인에 실패하였습니다.";		
			//echo "<script>alert('로그인실패');</script>";
		}		
    }else{
		if ($adminManager->loginAdmin($partner_id, $admin_id, $admin_pw)) {
			if ($partner_id!="1111") {
				header("Location: ./home/dashboard.php");
				exit();				
			}else{
				$loginError = "로그인에 실패하였습니다.";	
				exit();				
			}
		} else {
			 $loginError = "로그인에 실패하였습니다.";		
			//echo "<script>alert('로그인실패');</script>";
		}		
    }
 	
}
?>
 
<!DOCTYPE html>
<html lang="ko-kr">
<head>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
<title>관리자 로그인</title>
<link rel="stylesheet" type="text/css" href="/gm/css/common.css">
<link rel="stylesheet" type="text/css" href="/gm/css/layout.css">
<script type="text/javascript" src="/gm/js/jquery-1.11.3.min.js"></script>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/default.php'); ?>
</head>

<SCRIPT type="text/javascript">
<!--
function checkIt() {


	if(document.myform.partner_id.value.length==0){ alert("파트너넘버를 입력하세요"); document.myform.partner_id.focus();  return false;}
	if(document.myform.admin_id.value.length==0){ alert("접속아이디를 입력하세요"); document.myform.admin_id.focus();  return false;}
	if(document.myform.admin_pw.value.length==0){ alert("비밀번호를 입력하세요"); document.myform.admin_pw.focus(); return false;}
	document.myform.submit();
}


function setfocus(){
	document.myform.partner_id.focus();
}
//-->
</SCRIPT>

<body class="login">
 
<div class="login-wrap">

	<div class="login-header">
		<h1><font size="5" color="">ADMIN LOGIN</font><!-- <img src="img/login/logo.png" alt=""> --></h1>	
	</div><!--//header -->

	<div class="login-container">
		<form method="post"  name="myform"><!-- action="login_ok.php"  -->
		<div class="login-form">
			<div class="input"><input type="text" name="partner_id" placeholder="Partner Number"></div>
			<div class="input"><input type="text" name="admin_id" placeholder="ID"></div>
			<div class="input"><input type="password" name="admin_pw" placeholder="PW"></div>
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