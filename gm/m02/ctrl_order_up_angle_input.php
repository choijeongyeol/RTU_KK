<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?
	if(isset($_GET['warehouse_id'])&&($_GET['warehouse_id']!="")&&isset($_GET['angle_id'])&&($_GET['angle_id']!="")&&isset($_GET['angle_order'])&&($_GET['angle_order']!="")){		
		order_up_angle($_GET['warehouse_id'],$_GET['angle_id'],$_GET['angle_order']);
		
		//exit();
	}		
?>
 </body>
</html>

 