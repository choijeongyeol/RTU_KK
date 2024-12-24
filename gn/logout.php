<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/fn.php');

$adminManager->logoutAdmin();
header("Location:/gn/login.php");
exit();
?>
