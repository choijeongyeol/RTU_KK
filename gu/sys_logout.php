<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn.php');
session_start();

	$adminManager->syslogoutAdmin();

header("Location:/gu/login.php");
exit();
?>
