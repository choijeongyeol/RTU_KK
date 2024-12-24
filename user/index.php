<?php

session_start();

//로그인 확인
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}else{
    header("Location: ../swagger-ui/index.html");
    exit();
}

?>