<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469">
 
  <?
	if((isset($_POST['admin_id'])&&($_POST['admin_id']!=""))&&(isset($_POST['admin_pw'])&&($_POST['admin_pw']!=""))){	
		
	    change_pw($_POST['admin_id'],$_POST['admin_pw']); 
		echo "<script> window.opener.location.reload(); window.close();</script>";
		exit();
	}		
?>

     <? // 리스트 받는 admin_id 유지
	 if($_GET['arg2']!="") $item_id=$_GET['arg2'];
 	 ?>
	   
	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">비밀번호 변경</span></h2></center>
	<div class="ln_solid"></div>		
	
	
	
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	
	<input type="hidden" name="admin_id"  value="<? echo $_GET['arg2']?>">
 

		<center>

		<div class="item form-group" >
			<br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;text-align:left">신규비밀번호 입력
			    </label>
                <input type="text" required="required" class="form-control " name="admin_pw"  >
			</div> 	
		</div>
 		
		
		
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">비밀번호 변경완료</button>
		</div>
		</center>		
 
		</form>
		
		 <script>
			//document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
