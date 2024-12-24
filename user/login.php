<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');



// 사용자 등록
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $user_id = $_POST["user_id"];
    $user_pw = $_POST["user_pw"];
    $role = $_POST["role"];
 
    $userManager->registerUser($user_id, $user_pw, $role);
}
 

// 사용자 로그인
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
 
    $partner_id = $_POST["partner_id"];
    $user_id = $_POST["user_id"];
    $user_pw = $_POST["user_pw"];
	
 					
    if ($userManager->loginUser($partner_id, $user_id, $user_pw)) {
echo "2";exit();		

       // header("Location: /swagger-ui/index.html");
        //header("Location: /swagger-ui-multi/index.html");

        header("Location: /swagger-ui-rtu/index.html");
        exit();
    } else {
echo "3";exit();			
        $loginError = "아이디 또는 비밀번호가 바르지 않습니다.";
    }
}
?>
 
<!DOCTYPE html>
<html lang="ko-kr">
<head>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
<title>RTU 로그인</title>
<link rel="stylesheet" type="text/css" href="/gn/css/common.css">
<link rel="stylesheet" type="text/css" href="/gn/css/layout.css">
<script type="text/javascript" src="/gn/js/jquery-1.11.3.min.js"></script>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/user/inc/default.php'); ?>
</head>

<SCRIPT type="text/javascript">
<!--
function checkIt() {


	if(document.myform.user_id.value.length==0){ alert("아이디를 입력하세요"); document.myform.user_id.focus();  return false;}
	if(document.myform.user_pw.value.length==0){ alert("비밀번호를 입력하세요"); document.myform.user_pw.focus(); return false;}
	document.myform.submit();
}


function setfocus(){
	document.myform.user_id.focus();
}
//-->
</SCRIPT>

<body class="login">
 
<div class="login-wrap">

	<div class="login-header">
		<h1><font size="5" color="">RTU 로그인</font><!-- <img src="img/login/logo.png" alt=""> --></h1>	
	</div><!--//header -->

	<div class="login-container">
		<form method="post"  name="myform"><!-- action="login_ok.php"  -->
		<div class="login-form">
			<div class="input"><input type="text" name="partner_id" placeholder="파트너 NO" value="1234"></div>
			<div class="input"><input type="text" name="user_id" placeholder="아이디" value="BN241017-0001"></div>
			<div class="input"><input type="password" name="user_pw" placeholder="비밀번호"></div>
			<div class="command"><input type="submit"  name="login" value="로그인"></div><!--  onClick="checkIt();" -->
		</div> 
		</form>
	    <?php if (isset($loginError)) {
				echo "<p style='color: red;'>$loginError</p>"; 
				echo "<script>setfocus();</script>";
		} ?>		
	</div>

	<div class="login-footer">
		<!-- <small>Copyright ⓒ 2024 RTU. All Rights Reserved.</small> -->
	</div>

</div>
 
 
</body>
</html>