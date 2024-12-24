<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?  
	if(isset($_POST['cate_name'])&&($_POST['cate_name']!="")&&isset($_POST['cate_id'])&&($_POST['cate_id']!="")){	
 		
		$role_cnt = ck_cate_cnt($_POST['cate_admin_role']);	// 중복확인		
		
		if ($role_cnt[0]['role_cnt'] == 0) {
			update_admin_Cate($_POST['cate_admin_role'],$_POST['cate_name'],$_POST['cate_id'],$_POST['cate_comment'],$_POST['before_cate_admin_role']);	
			echo "<script> window.opener.location.reload(); window.close();</script>";
		    exit();			
		}else{
		    echo "<script>alert('숫자로 중복없이 입력바람');window.close();</script>";
			exit();	
		}				
		
	}
 ?>
 				<?php
				//echo $_GET['arg2'];
				// 제품 가져오기
				$cates_1 = get_admin_cate_search1($_GET['arg2']);				
						if ($cates_1) {
							$i=1;
							foreach ($cates_1 as $cate) {
								$cate_admin_role = "{$cate['cate_admin_role']}";								
								$cate_name = "{$cate['cate_name']}";								
								$cate_comment = "{$cate['cate_comment']}";								
							}
						}
				?>	
				
<br>				
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">분류명 수정</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">분류
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="number" required="required" class="form-control " name="cate_admin_role" value="<?echo $cate_admin_role?>">
				<input type="hidden"  name="before_cate_admin_role" value="<?echo $cate_admin_role?>">
				<input type="hidden" name="cate_id" value="<? echo $_GET['arg2'];?>" >
			</div> 	
		</div>
 

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">분류명
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="cate_name" value="<?echo $cate_name?>">
				<input type="hidden" name="cate_id" value="<? echo $_GET['arg2'];?>" >
			</div> 	
		</div>
 

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">설명
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="cate_comment" value="<?echo $cate_comment?>">
			</div> 	
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">수정완료</button>
			</div>
		</center>
 		
		</form>
		
		 <script>
			//document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
