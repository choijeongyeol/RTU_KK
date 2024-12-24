<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 

 
 <!-- ////////////////////   UPDATE 처리 start  /////////////////////////////////////////////////////////// -->
<?
	if(isset($_POST['warehouse_id'])&&($_POST['warehouse_id']!="")&&isset($_POST['angle_id'])&&($_POST['angle_id']!="")&&isset($_POST['angle_name'])&&($_POST['angle_name']!="")){		
		del_angle($_POST['warehouse_id'],$_POST['angle_id'],$_POST['angle_name']);	
		echo "<script>window.opener.location.reload(); window.close();</script>";
		//exit();
	}		
?>
  <!-- ////////////////////    UPDATE 처리   end   /////////////////////////////////////////////////////////// -->




	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">앵글 삭제</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
	<input  type="hidden" id="warehouse_id" name="warehouse_id" value="<?echo $_GET['arg2']?>">
	<input  type="hidden" id="angle_id" name="angle_id" value="<?echo $_GET['arg3']?>">
 
		
 <?
    // 화면에 앵글이름 출력을 위한 처리 
     if ((isset($_GET['arg2']))&&(isset($_GET['arg3']))){  // arg2 = warehouse_id ,  arg3 = angle_id
			$result = select_angle_one($_GET['arg2'],$_GET['arg3']);
	
			// 특정 데이터 1개 추출
			if (!empty($result)) {						
				$angle_name = $result[0]['angle_name'];  	
			} else {
				//echo "No data found.";	
			}		
     } 
	 
	 
	 // 삭제가능 앵글인지 검사
	  $result2 =  stock_count($_GET['arg2'],$_GET['arg3']);	// 삭제가능한 앵글인지 검사한다.
	  $stock_conut = $result2[0]['count'];  // 0 이면 앵글삭제 가능		 
	
?>
 		
 
		<div class="item form-group">
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">앵글명 <span class="required" style="color:#ff0000">*</span> (창고내 제품보관시 그구분을 위한 제품을 담는 박스)</label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="middle-name" class="form-control" type="text" name="angle_name" value="<?echo $angle_name?>" readonly>
			</div>
		</div>
 
		<center>
		<div  style="text-align:center;">
			 
				<button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
				<!-- <button class="btn btn-primary" type="reset">리셋</button> -->
				
				
				<? if ($stock_conut==0) { ?>
					<button type="submit" class="btn btn-success">앵글 삭제</button>
					<br><br><span style="color:#fff">앵글안에 제품이 없습니다.<br>
					삭제가 가능합니다.</span>
				<?  }else{ ?>
				<button type="button" class="btn btn-success" style="background:#ff0000">삭제 불가</button>
					<br><br><span style="color:#fff"><? echo $stock_conut?>개의 제품이 앵글에 있습니다.<br>
					앵글안에 제품이 있으면, 삭제가 안됩니다</span>				
				<?  }?>				
			</div>
		</center>
 		
		</form>
		
		 <script>
			document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
