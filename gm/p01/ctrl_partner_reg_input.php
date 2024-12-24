<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?  
	if(isset($_POST['admin_name'])&&($_POST['admin_name']!="")){	

 		
		$partner_id_cnt = ck_partner_id_cnt($_POST['partner_id']);	// 중복확인		
		
		if ($partner_id_cnt[0]['partner_id_cnt'] == 0) {
			add_admin_partner($_POST['partner_id'],$_POST['admin_id'],$_POST['admin_name']);
			//echo "<script> window.opener.location.reload(); window.close();</script>";
		    exit();			
		}else{
		    echo "<script>alert('중복숫자.다시 입력바람');window.close();</script>";
			exit();	
		}		
 
	}else{
		
	  $max_partner_code = getwms_max_partner_code();
	  
		// 결과가 배열인지 확인
		if (is_array($max_partner_code) && count($max_partner_code) > 0) {
			// 배열의 첫 번째 요소가 숫자인지 확인
			if (is_numeric($max_partner_code[0]['partner_id'])) {
				$max_partner_code = $max_partner_code[0]['partner_id'] + 1;
			} else {
				// 숫자가 아닌 경우 기본값 설정
				$max_partner_code = 2001; // 2000 이후의 첫 값으로 설정
				//echo "Error: Max partner code is not numeric. Defaulting to 2001.";
			}
		} else {
			// 배열이 비어있거나 유효하지 않은 경우 기본값 설정
			//$max_partner_code = 2001; // 2000 이후의 첫 값으로 설정
			//echo "Error: Failed to retrieve max partner code. Defaulting to 2001.";
			exit();
		}

		// 확인을 위해 출력
		//echo "New partner code: " . $max_partner_code;
	}
 ?>
 
				
<br>				
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">파트너 등록</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">파트너 넘버 (자동부여)
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="partner_id" id="partner_id" readonly>
			</div> 	
		</div>
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">접속 ID (자동부여)
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="admin_id" id="admin_id" readonly>
			</div> 	
		</div>

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">파트너 이름
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="admin_name"  >
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
    document.getElementById('partner_id').value = '<?php echo $max_partner_code; ?>';
    document.getElementById('admin_id').value = 'sysid<?php echo $max_partner_code; ?>';
	document.popup_form.admin_name.focus();
</script>
		 
		 
	  
 </body>
</html>
