<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?  
	if(isset($_POST['cate_name'])&&($_POST['cate_name']!="")){	

 		
		$role_cnt = ck_cate_cnt($_POST['cate_admin_role']);	// 중복확인		
		
		if ($role_cnt[0]['role_cnt'] == 0) {
			add_admin_Cate($_POST['cate_admin_role'],$_POST['cate_name'],$_POST['cate_comment']);
			echo "<script> window.opener.location.reload(); window.close();</script>";
		    exit();			
		}else{
		    echo "<script>alert('중복숫자.다시 입력바람');window.close();</script>";
			exit();	
		}		
 
	}
 ?>
 
				
<br>				
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">분류명 등록</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">분류 숫자  ( 2 ~ 99 사이 정수, 내림차순 출력. )
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="cate_admin_role" >
			</div> 	
		</div>

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">분류명
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="cate_name"  >
				<input type="hidden" name="cate_id" value="<? echo $_GET['arg2'];?>" >
			</div> 	
		</div>

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">분류설명
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="cate_comment" >
			</div> 	
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">등록완료</button>
			</div>
		</center>
 		
		</form>
		
		 <script>
			//document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
