<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 
 
 <!-- ////////////////////   UPDATE 처리 start  /////////////////////////////////////////////////////////// -->
<?
 
	if(isset($_POST['set_id'])&&($_POST['set_id']!="")){  	
		
		if ($_POST['set_name']!="") {
			$set_cnt = update_set_cnt($_POST['set_id'],$_POST['set_name']);	// 중복확인
			
			if ($set_cnt[0]['set_cnt'] == 0) {
				//echo "변경가능";
				update_set($_POST['set_id'],$_POST['set_name'],'set_name');
				echo "<script>window.opener.location.reload(); window.close();</script>";
				exit();			
			}else{
				echo "<script>alert('이름중복!');window.close();</script>";
				exit();	
			}
		}else{
			$set_cnt = update_set_cnt($_POST['set_id'],$_POST['set_comment']);	// 중복확인
			
			if ($set_cnt[0]['set_cnt'] == 0) {
				//echo "변경가능";
				update_set($_POST['set_id'],$_POST['set_comment'],'set_comment');
				echo "<script>window.opener.location.reload(); window.close();</script>";
				exit();			
			}else{
				echo "<script>alert('이름중복!');window.close();</script>";
				exit();	
			}
		}
	}	
	
 
	
?>
  <!-- ////////////////////    UPDATE 처리   end   /////////////////////////////////////////////////////////// -->


		
	   <!-- ////////////////////     DB로부터 특정값 추출 start   /////////////////////////////////////////////////////////// -->
		<?
			$result = getwms_set_name($_GET['arg2']);
			// 특정 데이터 1개 추출
			if (!empty($result)) {
				$set_id   = $result[0]['set_id'];  						
				$set_name = $result[0]['set_name'];  						
				$set_comment = $result[0]['set_comment'];  						
			} else {
				//echo "No data found.";
			}	
 
		?>	
	   <!-- ////////////////////     DB로부터 특정값 추출 end   /////////////////////////////////////////////////////////// -->
		


	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">기능항목명칭 수정</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	<input  type="hidden" id="set_id" name="set_id" value="<?echo $_GET['arg2']?>">
 

		
		<input type="hidden"   name="set_id" readonly value="<?echo $set_id?>">

		<div class="item form-group">
			<? if ($_GET['col']=="set_name") { ?>
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">기능항목 </label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="middle-name" class="form-control" type="text" name="set_name"  value="<?echo $set_name?>" required>
			<?}else{?>
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">설명 </label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="middle-name" class="form-control" type="text" name="set_comment"  value="<?echo $set_comment?>" required>
			<?}?>
			</div>
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				<button type="submit" class="btn btn-success">수정완료</button>
			</div>
		</center>
 		
		</form>
	 
	  
 </body>
</html>
