<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?  
	if(isset($_POST['warehouse_code'])&&($_POST['warehouse_code']!="")&&isset($_POST['to_ware_name'])&&($_POST['to_ware_name']!="")){
		
		$postValue = $_POST['to_ware_name']; // 'to_ware_name'는 폼에서 전송된 입력 필드의 이름입니다.		
		//if (isFirstCharacterDigit($postValue)) {
		//	echo "<script> alert('첫 번째 글자가 숫자입니다. 문자로 입력바랍니다.');location.href='/gn/m02/ctrl_warehouse_input.php';</script>";	
		//	exit();				
		//} else {
			//echo "첫 번째 문자가 숫자가 아닙니다.";
			addWarehouse($_POST['warehouse_code'],$_POST['to_ware_name']);
			echo "<script> window.opener.location.reload(); window.close();</script>";	
			exit();			
		//}
	}
 ?>
 

	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">창고 등록</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
		<div class="item form-group">
			<label class="col-form-label col-md-3 col-sm-3 label-align" style="color:#fff">코드명 (창고식별고유코드 / 자동생성 / 수정불가)<!-- <span class="required">*</span> -->
			</label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<?
				    $result = getwms_warehouse_last1();

					// 특정 데이터 1개 추출
					if (!empty($result)) {
						$specificData = $result[0]['warehouse_id'];  
						//echo $specificData;
						$specificData = $specificData + 1000;
						
					} else {
						//echo "No data found.";
					}
				?>	
				<input class="form-control" type="text" name="warehouse_code"  readonly value=W<? echo $specificData;?>  >
			</div>
		</div> 
 
		<div class="item form-group" >
			<label class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff;">창고명 (생성하세요) <?//echo $specificData;?>
			</label><br>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:10px">
				<input type="text" required="required" class="form-control " name="to_ware_name" value="W<?echo $specificData;?>">
			</div> 	
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">등록완료</button>
			</div>
		</center>
		<div style="text-align:center;color:#FFF;display:none">입력만큼 가감됩니다.  ex) -7 or 200</div>
		
		</form>
		
		 <script>
			//document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
