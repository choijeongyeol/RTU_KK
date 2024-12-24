<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469">
 
<script>
    function compareNumbers() {
        var num1 = parseFloat(document.getElementById("number1").value);
        var num2 = parseFloat(document.getElementById("number2").value);
        var num3;
        if (isNaN(num1) || isNaN(num2)) {
            document.getElementById("result").innerText = "올바른 숫자를 입력하세요.";
        } else {
            if (num1 > num2) {
               alert(num2+"이하로 넣어주세요");
			   return;
            }
        }
		num3 = num2 - num1;
		return num3;
    }
	
    function submitForm() {
        var form = document.getElementById("popup_form");		
	   	var returnValue = compareNumbers();
        if (returnValue >= 0 ){form.submit(); }
    }	
	
</script> 
 
 
 
 
 
 
 
 
 
 
 
 <?
 
// 오류 출력을 화면에 허용하기
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

 ?>
 
 
  <?
	if(isset($_POST['item_id'])&&($_POST['item_id']!="")&&isset($_POST['qua'])&&($_POST['qua']!="")){	
 
		// Check if any POST data exists
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			
			// Iterate through all POST data and display key-value pairs
			foreach ($_POST as $key => $value) {
			//	echo "Key: " . htmlspecialchars($key) . ", Value: " . htmlspecialchars($value) . "<br>";
			}
			
		} else {
			//echo "No POST data received.";
		}
 
		$to_ware  = $_POST['to_ware'];
		$to_angle = $_POST['to_angle'];
 
		$step_1 = "Y";
		if ($to_ware=="") { $to_ware = 0;}
		if ($to_angle=="") { $to_angle = 0;}
		//if ($step_1!="Y") { $step_1="N";}
 
          
		//echo "<br>item_id".$_POST['item_id'];  
		//echo "<br>to_ware".$to_ware;  
		//echo "<br>to_angle".$to_angle;  
		//echo "<br>qua".$_POST['qua'];  
		//echo "<br>step_1".$step_1;  
		//echo "<br>";  
         
		//exit(); 
        
		movetoangle_inc_company_Stock($_POST['item_id'],$to_ware,$to_angle,$_POST['qua'],$step_1); // $_POST['step_1'] = Y 면 앵글로 이동
		echo "<script> window.opener.location.reload(); window.close();</script>";
	}		
?>

     <? // 리스트로부터 입고등록 버튼누르면 받는 item_id 유지
	 if($_GET['arg2']!="") $item_id=$_GET['arg2'];
	 if($_POST['item_id']!="") $item_id=$_POST['item_id'];
	 
	 if($_GET['arg3']!="") $max_cnt=$_GET['arg3'];
	 if($_POST['max_cnt']!="") $max_cnt=$_POST['max_cnt'];
	 
	 
	 ?>
	   
	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">앵글로 제품이동</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form" id="popup_form" method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	<input type="hidden" name="ID" class="w90" value=<?echo $_SESSION['admin_name']?> readonly>
	<input type="hidden" name="save_YN" > <!-- 보관장소(앵글)선택값 보관 -->
	<input type="hidden" name="to_ware_v" > <!-- 임시 창고선택값 보관 -->
	<input type="hidden" name="to_angle_v" > <!-- 임시 앵글값 보관 -->
<!-- <input type="hidden" name="to_company_v" > --> <!-- 임시 업체 보관 -->
	<input type="hidden" name="cnt_v" > <!-- 카운트 보관 -->
	<input type="hidden" name="item_id" value="<? echo $item_id;?>" > <!-- 카운트 보관 -->
	
	<input type="hidden" name="step_1" value="Y" > <!-- step_1 -->
	<input type="hidden" name="step_2" > <!-- step_2 -->
	<input type="hidden" name="step_3" > <!-- step_3 -->
	<input type="hidden" name="max_cnt" value= "<?echo $max_cnt?>" > 

	
	<?
				
		if ($_POST['step_2']!=""){
	     $step_2 = $_POST['step_2'];
	   }		
		if ($_POST['step_3']!=""){
	     $step_3 = $_POST['step_3'];
	   }		
		if ($_POST['save_YN']!=""){
	     $save_YN = $_POST['save_YN'];
	   }		
		if ($_POST['to_ware_v']!=""){
	     $to_ware_v = $_POST['to_ware_v'];
	   }
	   if ($_POST['to_angle_v']!=""){
	     $to_angle_v = $_POST['to_angle_v'];
	   }
 
	?>
	    
       <? if ($save_YN=="Y" || 1==1) {?>		
		<div class="item form-group">
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">창고 선택 <span class="required" style="color:#ff0000">*</span></label>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">
				<select id="to_ware" name="to_ware" onchange="fn_step_1(this.value)"  style="width:200px">창고 선택
							 <option value='0'>--- 선택하세요 -----</option> 		
				<?
				$warehouses = getwms_warehouses(0,1000,'','');
					if ($warehouses) {
						$i=1;
						foreach ($warehouses as $warehouse) {
							if ($to_ware_v == "{$warehouse['warehouse_id']}") {
								$selected = "selected";
							}else{
								$selected = "";
							}
							echo "<option value='{$warehouse['warehouse_id']}' ".$selected." >{$warehouse['warehouse_name']}</option>";
							$i=$i+1;	
						}
					} 	
				?>
				</select>				
			</div>
		</div>

       <? 
	    }?>		
 			
       <? if ($step_2=="Y") {?>		
		<div class="item form-group">
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">앵글 선택 <span class="required" style="color:#ff0000">*</span></label>
			<div class="col-md-6 col-sm-6 " style="margin-top:5px">
			
		<?         
			$angle_lists = getwms_angle_namelist($to_ware_v);
			if (isset($angle_lists)) {	
		?>
				<select id="to_angle" name="to_angle" required onchange="fn_step_2(this.value)" style="width:200px">앵글 선택
							 <option value='0'>--- 선택하세요 -----</option> 		
				<?
					if ($angle_lists) {
						$i=1;
						foreach ($angle_lists as $angle_list) {
							if ($to_angle_v == "{$angle_list['angle_id']}") {
								$selected = "selected";
							}else{
								$selected = "";
							}
							echo "<option value='{$angle_list['angle_id']}' ".$selected." >{$angle_list['angle_name']}</option>";
							$i=$i+1;	
						}
					} 	
				?>
				</select>			 
		<?	 
			}		
		?>				
			</div>
		</div>			
		<?	 
		}		
		?>		
  <input type="hidden" name="to_company" value='미지정'>
 			
       <? if ($step_3=="Y") {?>				
		<div class="item form-group" >
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">수량 <span class="required" style="color:#ff0000">*</span></label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:0px">
				<input id="number1" class="form-control" type="number" name="qua"  required placeholder="최소수량 1이상, 최대<?echo $max_cnt?>이하 정수"  style="width:300px">
			</div>
		</div>
		
		<input id="number2"  type="hidden" value="<?echo $max_cnt?>" >
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="button" class="btn btn-success" onclick="submitForm()">앵글로 이동완료</button>
		</div>
		</center>		
		
		<?	 
		}		
		?>		
 
		</form>
		
		 <script>
			//document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
