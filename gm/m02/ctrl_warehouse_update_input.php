<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 
 
 <!-- ////////////////////   UPDATE 처리 start  /////////////////////////////////////////////////////////// -->
<?
 
	if(isset($_POST['warehouse_id'])&&($_POST['warehouse_id']!="")){  		
		$warehouse_cnt = update_warehouse_cnt($_POST['warehouse_id'],$_POST['warehouse_name']);	// 중복확인
		
		if ($warehouse_cnt[0]['warehouse_cnt'] == 0) {
			
			$postValue = $_POST['warehouse_name']; // 'warehouse_name'는 폼에서 전송된 입력 필드의 이름입니다.		
			if (isFirstCharacterDigit($postValue)) {
				echo "<script> alert('첫 번째 글자가 숫자입니다. 문자로 입력바랍니다.');location.href='".$_SERVER['PHP_SELF']."?arg1=warehouse_update&arg2=".$_POST['warehouse_id']."';</script>";	
				exit();				
			} else {
				//echo "변경가능";
				update_warehouse($_POST['warehouse_id'],$_POST['warehouse_name']);
				echo "<script>window.opener.location.reload(); window.close();</script>";
				exit();		
			}			
		}else{
		    echo "<script>alert('중복이름.다른 명칭 바람');window.close();</script>";
			exit();	
		}
	}	
	
 
	
?>
  <!-- ////////////////////    UPDATE 처리   end   /////////////////////////////////////////////////////////// -->


	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">창고명 변경</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	<input  type="hidden" id="warehouse_id" name="warehouse_id" value="<?echo $_GET['arg2']?>">
 
		
	   <!-- ////////////////////     DB로부터 특정값 추출 start   /////////////////////////////////////////////////////////// -->
		<?
			$result = getwms_warehouse_name($_GET['arg2']);
			// 특정 데이터 1개 추출
			if (!empty($result)) {
				$warehouse_id   = $result[0]['warehouse_id'];  						
				$warehouse_code = $result[0]['warehouse_code'];  						
				$warehouse_name = $result[0]['warehouse_name'];  						
			} else {
				//echo "No data found.";
			}	
 
		?>	
	   <!-- ////////////////////     DB로부터 특정값 추출 end   /////////////////////////////////////////////////////////// -->
		
		
		<input type="hidden"   name="warehouse_id" readonly value="<?echo $warehouse_id?>">

		<div class="item form-group">
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">창고명 <span class="required" style="color:#ff0000">*</span> (제품보관시 앵글을 포함하는 장소)</label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="middle-name" class="form-control" type="text" name="warehouse_name"  required>
			</div>
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">창고명 수정완료</button>
			</div>
		</center>
 		
		</form>
		
		 <script>
			document.popup_form.warehouse_name.focus();
		 </script>
	  
 </body>
</html>
