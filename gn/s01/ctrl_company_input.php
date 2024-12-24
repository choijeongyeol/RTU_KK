<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?  
	if(isset($_POST['cate_name'])&&($_POST['cate_name']!="")){		
		
		$cate_cnt = ck_company_cnt($_POST['cate_name']);	// 중복확인		
		
		if ($cate_cnt[0]['cate_cnt'] == 0) {
			addcompany($_POST['cate_name']);
			echo "<script> window.opener.location.reload(); window.close();</script>";
			exit();	
		}else{
		    echo "<script>alert('중복이름.다시 입력바람');window.close();</script>";
			exit();	
		}					
	}
 ?>
 
				
<br>				
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">입출고 업체명 등록</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">업체명
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="cate_name" value="<?echo $cate_name?>">
				<input type="hidden" name="cate_id" value="<? echo $_GET['arg2'];?>" >
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
