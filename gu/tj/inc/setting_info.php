<?php
session_start();
    
	$kk_admin_dir = "tj/"; // 금강(파트너) 관리자 디렉토리
	$top_dir = '/gu/'; // 파트너 앞의 root 디렉토리
	$root_dir = $_SERVER['DOCUMENT_ROOT'].$top_dir.$kk_admin_dir;   //$root_dir = $_SERVER['DOCUMENT_ROOT'].'/gu/tp/';  파트너이든, S파트너이든 root_dir로 시작함. 위에서 분기처리
    $db_conn = '/gu/inc/db_connection.php';  // DB 연결경로

?>


