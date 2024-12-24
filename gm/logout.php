<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');

$adminManager->logoutAdmin();

if ($_GET['partner_id']=="1234") {
	header("Location:/gm/slogin.php");
}else{
	header("Location:/gm/login.php");
}
exit();
?>
