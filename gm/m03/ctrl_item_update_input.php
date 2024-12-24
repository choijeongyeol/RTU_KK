<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 
 
 <!-- ////////////////////   UPDATE 처리 start  /////////////////////////////////////////////////////////// -->
<?
 
	if(isset($_POST['item_id'])&&($_POST['item_id']!="")){  		
		$item_cnt = update_item_cnt($_POST['item_id'],$_POST['item_name']);	// 중복확인
		
		if ($item_cnt[0]['item_cnt'] == 0) {
			//echo "변경가능";
			update_item2($_POST['item_id'],$_POST['item_name'],$_POST['item_code']);
		    echo "<script>window.opener.location.reload(); window.close();</script>";
		    exit();			
		}else{
		    echo "<script>alert('제품명중복.다시입력바람');window.close();</script>";
			exit();	
		}
	}	
	
 
	
?>
  <!-- ////////////////////    UPDATE 처리   end   /////////////////////////////////////////////////////////// -->


	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">제품정보 변경</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	<input  type="hidden" id="item_id" name="item_id" value="<?echo $_GET['arg2']?>">
 
		
	   <!-- ////////////////////     DB로부터 특정값 추출 start   /////////////////////////////////////////////////////////// -->
		<?
			$result = getwms_item_name($_GET['arg2']);
			// 특정 데이터 1개 추출
			if (!empty($result)) {
				$item_id   = $result[0]['item_id'];  						
				$item_code = $result[0]['item_code'];  						
				$item_name = $result[0]['item_name'];  						
			} else {
				//echo "No data found.";
			}	
 
		?>	
	   <!-- ////////////////////     DB로부터 특정값 추출 end   /////////////////////////////////////////////////////////// -->
		
		
		<input type="hidden"   name="item_id" readonly value="<?echo $item_id?>">

		<div class="item form-group">
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">제품명 </label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="middle-name" class="form-control" type="text" name="item_name"  value="<?echo $item_name?>" required>
			</div>
 
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">제품바코드 </label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="middle-name" class="form-control" type="text" name="item_code"  value="<?echo $item_code?>" maxlength="13" required>
			</div>
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">제품정보 수정완료</button>
			</div>
		</center>
 		
		</form>
	 
	  
 </body>
</html>
