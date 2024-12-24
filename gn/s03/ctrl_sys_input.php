<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 <?
	if(isset($_POST['set_name'])&&($_POST['set_name']!="")&&isset($_POST['set_comment'])&&($_POST['set_comment']!="")){		
		add_setsys_col($_POST['set_name'],$_POST['set_comment']);
		echo "<script> window.opener.location.reload(); window.close();</script>";
		exit();
	}		
?>


	<br />
	<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">시스템설정 항목추가</span></h2></center>
	<div class="ln_solid"></div>		
	<form name="popup_form"  method="post" action="<?echo $_SERVER['PHP_SELF']?>">
 
		<div class="item form-group">
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">기능항목</label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="set_name" class="form-control" type="text" name="set_name"  >
			</div>
		</div>

		<div class="item form-group">
			<label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">설명</label>
			<div class="col-md-6 col-sm-6 " style="margin-bottom:30px">
				<input id="set_comment" class="form-control" type="text" name="set_comment"  >
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
			document.popup_form.angle_name.focus();
		 </script>
	  
 </body>
</html>
