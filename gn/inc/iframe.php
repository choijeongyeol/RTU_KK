<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?

	if(isset($_GET['warehouse_id'])&&($_GET['warehouse_id']!="")&&isset($_GET['angle_id'])&&($_GET['angle_id']!="")&&isset($_GET['angle_order'])&&($_GET['angle_order']!="")){		
		order_up_angle($_GET['warehouse_id'],$_GET['angle_id'],$_GET['angle_order']);
		
	}elseif(isset($_GET['m04_access_id'])&&($_GET['m04_access_id']!="")&&isset($_GET['m04_access_name'])&&($_GET['m04_access_name']!="")&&isset($_GET['m04_access_type'])&&($_GET['m04_access_type']!="")&&isset($_GET['m04_access_value'])&&($_GET['m04_access_value']!="")){	
		
		if ($_GET['m04_access_value']=="999") { // 전체 일괄등록
		   m04_role_add_all($_GET['m04_access_id'],$_GET['m04_access_name'],$_GET['m04_access_type'],$_GET['m04_access_value']);
		}else{
		   m04_role_add($_GET['m04_access_id'],$_GET['m04_access_name'],$_GET['m04_access_type'],$_GET['m04_access_value']);
		}   
	}elseif(isset($_GET['access_id'])&&($_GET['access_id']!="")&&isset($_GET['access_type'])&&($_GET['access_type']!="")&&isset($_GET['role'])&&($_GET['role']!="")){	
		   m04_role_del($_GET['access_id'],$_GET['access_type'],$_GET['role']);
		   
	}elseif(isset($_GET['cate_change'])&&($_GET['cate_change']!="")&&isset($_GET['item_cate'])&&($_GET['item_cate']!="")&&isset($_GET['item_id'])&&($_GET['item_id']!="")){	
		   m03_cate_change($_GET['cate_change'],$_GET['item_cate'],$_GET['item_id']);
		   
	}
	
	
?>
 
 
 
 
 <script>
 if (window.opener) {
    window.opener.location.reload(true);
} else {
   // console.error("부모 창이 없습니다.");
}
 
	   // window.opener.location.reload(true); // 부모 창 캐시 무시하고 새로고침
 </script>
 </body>
</html>

 