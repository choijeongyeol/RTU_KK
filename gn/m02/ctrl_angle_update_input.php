<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 
 
 <!-- ////////////////////   UPDATE 처리 start  /////////////////////////////////////////////////////////// -->
<?
	if(isset($_POST['warehouse_id'])&&($_POST['warehouse_id']!="")&&isset($_POST['angle_id'])&&($_POST['angle_id']!="")&&isset($_POST['angle_name'])&&($_POST['angle_name']!="")){	
		
		$angle_cnt = ck_angle_cnt($_POST['warehouse_id'],$_POST['angle_name']);	// 중복확인		
		
		if ($angle_cnt[0]['angle_cnt'] == 0) {
			update_angle($_POST['warehouse_id'],$_POST['angle_id'],$_POST['angle_name']);	
			echo "<script>window.opener.location.reload(); window.close();</script>";
			exit();	
		}else{
		    echo "<script>alert('중복이름.다시 입력바람');window.close();</script>";
			exit();	
		}			
	}		
?>
  <!-- ////////////////////    UPDATE 처리   end   /////////////////////////////////////////////////////////// -->


	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">앵글명 변경</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	<input  type="hidden" id="warehouse_id" name="warehouse_id" value="<?echo $_GET['arg2']?>">
	<input  type="hidden" id="angle_id" name="angle_id" value="<?echo $_GET['arg3']?>">
 
		
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
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">앵글명 <span class="required" style="color:#ff0000">*</span> (창고내 제품보관시 그구분을 위한 제품을 담는 박스)</label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="middle-name" class="form-control" type="text" name="angle_name"  required>
			</div>
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">앵글명 수정완료</button>
			</div>
		</center>
 		
		</form>
		
		 <script>
			document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
