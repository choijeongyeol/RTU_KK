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


 
	if(isset($_POST['user_name'])&&($_POST['user_name']!="")){		

		$user_cnt = ck_user2_cnt($_POST['user_id']);	// 중복확인		
		
		if ($user_cnt[0]['user_cnt'] == 0) {
			//echo "변경가능";
			addUser($_POST['user_id'],$_POST['user_name']);
		    echo "<script>window.opener.location.reload(); window.close();</script>";
		    exit();			
		}else{
		    echo "<script>alert('중복ID.다시 입력바람');window.close();</script>";
			exit();	
		}		
 
	}
 ?>
 
<br>				
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">사용자 등록</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">

 
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">사용자 아이디 (비밀번호는 자동초기화 생성됨 : 1234)
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">			
				<input type="text" name="user_id" id="user_id"  class="form-control"  >
			</div> 	
		</div>
 
 
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">사용자명 
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">			
				<input type="text" name="user_name" id="user_name"  class="form-control"  >
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
			 document.popup_form.user_id.focus();
		 </script>
	  
 </body>
</html>
