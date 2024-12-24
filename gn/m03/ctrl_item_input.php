<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
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

 
	if(isset($_POST['code'])&&($_POST['code']!="")&&isset($_POST['name'])&&($_POST['name']!="")){	
			addItem($_POST['code'],$_POST['name'],$_POST['item_cate']);
			echo "<script> window.opener.location.reload(); window.close();</script>";		
	}
 ?>
 
<br>				
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">제품 등록</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">

 
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">제품명
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">			
				<input type="text" name="name" id="name"  class="form-control"  >
			</div> 	
		</div>
 

		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">제품분류
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">
				<select name="item_cate" class="form-control" >분류 선택
				<?
				$cates = getwms_cate(0,1000);
					if ($cates) {
						$i=1;
						foreach ($cates as $cate) {
							echo "<option value='{$cate['cate_id']}'>{$cate['cate_name']}</option>";
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
 
 
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">코드명 (자동생성)
			</label> 
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">
				<?
 
					$current_hour = date("G");
					$current_minute = date("i");
					$current_sec = date("s");
					$current_seconds_of_the_day = $current_sec + $current_minute*60 + $current_hour*60*60;
					
					$date = date("Ymd");
					$specificData = $date*100000 + $current_seconds_of_the_day;				
				?>				
				<input type="text" name="code" id="code"   value="<? echo $specificData;?>"  class="form-control"  >
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
			 document.popup_form.name.focus();
		 </script>
	  
 </body>
</html>
