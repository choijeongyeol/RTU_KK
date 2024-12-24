<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');
// 사용자 로그인
if ($_GET["sys_code"]=="sdfw*^34g53y4uy_(H$345dfg@*df") {
 
    $partner_id = $_GET["partner_id"];
    $admin_id = "sysid".$_GET["partner_id"];
 
	if ($adminManager->loginAdmin_from_sys($partner_id, $admin_id)) {
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
?>
 