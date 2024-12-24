<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');
 
?>


<!DOCTYPE html>
<html lang="ko-kr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>WMS</title>
	
	<!-- /////// gm 소스 시작 ////// -->	
	<!-- <link rel="stylesheet" type="text/css" href="/gm/css/common.css">
	<link rel="stylesheet" type="text/css" href="/gm/css/layout.css"> -->
	<script type="text/javascript" src="/gm/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="/gm/js/ui.js"></script>
	<script type="text/javascript" src="/gm/js/fn.js"></script>
	<script type="text/javascript" src="/gm/js/jquery.js"></script>
	<script type="text/javascript" src="/gm/js/ui.core.js"></script>
	<script type="text/javascript" src="/gm/js/ui.datepicker.js"></script>
	<!-- <link type="text/css" href="/gm/css/base/ui.all.css" rel="stylesheet"> -->
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/default.php'); ?>

	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
		$(document).ready(
			function(){
			   $("input[name=ip_date]").attr({readonly : true});
			 //  $("input[name=ip_date]").datepicker({showOn: 'button', buttonImage: "/gm/img/content/btn_date.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
			}
		);
	//-->
	</SCRIPT>
	<!-- /////// gm 소스 끝 ////// -->
	
	

    <!-- Bootstrap -->
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    
    <link href="/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="/build/css/custom.min.css" rel="stylesheet">	