<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?  
 
 		// Check if any POST data exists
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			
			// Iterate through all POST data and display key-value pairs
			foreach ($_POST as $key => $value) {
				echo "Key: " . htmlspecialchars($key) . ", Value: " . htmlspecialchars($value) . "<br>";
			}
			
			//exit();
		} else {
			//echo "No POST data received.";
		}

 
	if($_POST['access_name']!=""){		
		add_access($_POST['access_id'],$_POST['access_name']);
		echo "<script> window.opener.location.reload(); window.close();</script>";
		
		exit();
	}

      
	  
	  
	  
	  // 초기에 구동, 최대 id 값 +1 하기,  id값으로 넣어준다. 
	  $result = getwms_access_crud_maxid();
	  
		if (!empty($result)) {
			$specificData = $result[0]['access_id'];  
 			$access_id = $specificData + 1;
 		}
 
 ?>
 
     
 
 
<br>				
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">관리대상 등록</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	
		<input type="hidden" name="access_id" id="access_id"  value="<?echo $access_id?>" >

 
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">대상이름
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">			
				<input type="text" name="access_name" id="access_name"  class="form-control"  >
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
			 document.popup_form.access_name.focus();
		 </script>
	  
 </body>
</html>
