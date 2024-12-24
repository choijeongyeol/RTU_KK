<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469">
 
  <?
	if((isset($_POST['user_id'])&&($_POST['user_id']!=""))&&(isset($_POST['user_use'])&&($_POST['user_use']!=""))){	
		
	    user_change_state($_POST['user_id'],$_POST['user_use']); 
		echo "<script> window.opener.location.reload(); window.close();</script>";
		exit();
	}		
?>

     <? // 리스트 받는 user_id 유지
	 if($_GET['arg2']!="") $item_id=$_GET['arg2'];
 	 ?>
	   
	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">계정 상태변경</span></h2></center>
	<div class="ln_solid"></div>		
	
	
	
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	
	<input type="hidden" name="user_id"  value="<? echo $_GET['arg2']?>">
 

		<center>
 <br><br><br>
		
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<? if ($_GET['arg3']=="Y") { ?>
					<button type="submit" class="btn btn-success">비활성화로 변경완료</button>				
					<input type="hidden" name="user_use"  value="N">
				<?}else{?>
					<button type="submit" class="btn btn-success">활성화로 변경완료</button>				
					<input type="hidden" name="user_use"  value="Y">
				<?}?>
		</div>
		</center>		
 
		</form>
		
		 <script>
			//document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
