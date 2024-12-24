<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?  
 
 		// Check if any POST data exists
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			
			// Iterate through all POST data and display key-value pairs
			foreach ($_POST as $key => $value) {
			//	echo "Key: " . htmlspecialchars($key) . ", Value: " . htmlspecialchars($value) . "<br>";
			}
			
			//exit();
		} else {
			//echo "No POST data received.";
		}

       
 
	if(isset($_POST['admin_name'])&&($_POST['admin_name']!="")&&isset($_POST['cate_admin_role'])&&($_POST['cate_admin_role']!="")){		
 		
		$user_cnt = ck_user_cnt($_POST['admin_id']);	// 중복확인		
		
		if ($user_cnt[0]['user_cnt'] == 0) {
			//echo "변경가능";
			addAdmin($_POST['admin_id'],$_POST['admin_name'],$_POST['cate_admin_role']);
		    echo "<script>window.opener.location.reload(); window.close();</script>";
		    exit();			
		}else{
		    echo "<script>alert('중복ID.다시 입력바람');window.close();</script>";
			exit();	
		}		
 
	}
 ?>
 
<br>				
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">운영자 등록</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">

 
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">운영자 아이디 (비밀번호는 자동초기화 생성됨 : 1234)
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">			
				<input type="text" name="admin_id" id="admin_id"  class="form-control"  >
			</div> 	
		</div>
 
 
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">운영자명 
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">			
				<input type="text" name="admin_name" id="admin_name"  class="form-control"  >
			</div> 	
		</div>
 

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">분류
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">
				<select name="cate_admin_role" class="form-control" >
				<option>------------------------ 선택하세요 ------------------------</option>
				<?
				$cates = get_admin_cate_reg(0,1000);
					if ($cates) {
						$i=1;
						foreach ($cates as $cate) {
							echo "<option value='{$cate['cate_admin_role']}'>분류 {$cate['cate_admin_role']} / {$cate['cate_name']}</option>";
							$i=$i+1;	
						}
						//echo "<option value='0'>분류없음(분류없이 보관)</option>";
					} else {
						//echo "<option value='0'>분류없음(분류없이 보관)</option>";
					}		
				?>
				</select>	
			</div> 	
		</div>
 

 
		<center>
		

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;"> 
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">
				<div  style="text-align:center;">
			 
					<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
					<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
					<button type="submit" class="btn btn-success">등록완료</button>
				</div>
			</div> 	
		</div>
 
		</center>
 		
		</form>
		
		 <script>
			 document.popup_form.admin_id.focus();
		 </script>
	  
 </body>
</html>
