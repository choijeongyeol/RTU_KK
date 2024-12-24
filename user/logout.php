<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/fn_api.php');

$userManager->logoutUser();
header("Location:/user/login.php");
exit();
?>
