<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?
	if(isset($_POST['warehouse_id'])&&($_POST['warehouse_id']!="")&&isset($_POST['angle_name'])&&($_POST['angle_name']!="")){		
		
		$angle_cnt = ck_angle_cnt($_POST['warehouse_id'],$_POST['angle_name']);	// 중복확인		
		
		if ($angle_cnt[0]['angle_cnt'] == 0) {
			add_angle($_POST['warehouse_id'],$_POST['angle_name']);
			echo "<script> window.opener.location.reload(); window.close();</script>";
			exit();		
		}else{
		    echo "<script>alert('중복이름.다시 입력바람');window.close();</script>";
			exit();	
		}	
		

	}		
?>


	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">앵글 삽입</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	
	   <!-- ////////////////////     DB로부터 특정값 추출 start   /////////////////////////////////////////////////////////// -->
		<?
			$result = getwms_warehouse_name($_GET['arg2']);
			// 특정 데이터 1개 추출
			if (!empty($result)) {
				$warehouse_id   = $result[0]['warehouse_id'];  						
				$warehouse_code = $result[0]['warehouse_code'];  						
				$warehouse_name = $result[0]['warehouse_name'];  	
				//echo $warehouse_id;
			} else {
				// echo "No data found.";
			}					

			$result2 = getwms_max_angle($_GET['arg2']);
			// 특정 데이터 1개 추출
			if (!empty($result2)) {						
				$angle_name = $result2[0]['angle_id'] + 1001;  						
			} else {
				//echo "No data found.";
			}					

		?>	
	   <!-- ////////////////////     DB로부터 특정값 추출 end   /////////////////////////////////////////////////////////// -->
		
		
		<input type="hidden"   name="warehouse_id" readonly value="<?echo $warehouse_id?>">

		<div class="item form-group">
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">앵글명 <span class="required" style="color:#ff0000">*</span> (창고내 제품보관시 그구분을 위한 제품을 담는 박스)</label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="middle-name" class="form-control" type="text" name="angle_name" value="AG<?echo $angle_name?>">
			</div>
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">삽입완료</button>
			</div>
		</center>
		<div style="text-align:center;color:#FFF;display:none">입력만큼 가감됩니다.  ex) -7 or 200</div>
		
		</form>
		
		 <script>
			document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
