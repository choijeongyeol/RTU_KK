<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469">
 
  <?
	if(isset($_POST['user_id'])&&($_POST['user_id']!="")){	
		user_reset_pw($_POST['user_id']); 
		echo "<script>alert('비밀번호 초기화 완료');window.opener.location.reload(); window.close();</script>";
	}		
?>

     <? // 리스트로부터 입고등록 버튼누르면 받는 item_id 유지
	 if($_GET['arg2']!="") $item_id=$_GET['arg2'];
	 if($_POST['item_id']!="") $item_id=$_POST['item_id'];
	 ?>
	   
	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">비밀번호 초기화</span></h2></center>
	<div class="ln_solid"></div>		
	
	
	
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	
	<input type="hidden" name="user_id"  value="<? echo $_GET['arg2']?>">
 

		<center>
		<div  style="text-align:center;">
			<label for="middle-name"  style="color:#fff">
				아이디 <span style="font-size:30px"><? echo $_GET['arg2']?></span><BR><BR> 비밀번호를 <span style="font-size:20px;color:#ffff00" >1234</span> 로 초기화 합니다.<BR><BR>
				
			</label>

		</div>
 		
		
		
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">비밀번호 초기화 완료</button>
		</div>
		</center>		
 
		</form>
		
		 <script>
			//document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
