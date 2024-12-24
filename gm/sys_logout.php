<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');
session_start();

	$adminManager->syslogoutAdmin();

header("Location:/gm/login.php");
exit();
?>
