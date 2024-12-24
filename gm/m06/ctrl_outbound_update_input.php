<?php include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
</head>
<body style="overflow: hidden;background:#405469"> 

<?php
if(isset($_POST['able_outbound_quantity']) && ($_POST['able_outbound_quantity']!="")){  		
    //$warehouse_cnt = update_warehouse_cnt($_POST['warehouse_id'],$_POST['warehouse_name']);	// 중복확인

    update_outbound($_POST['outbound_id'],$_POST['able_outbound_quantity'],$_POST['plan_date']);
    echo "<script>window.opener.location.reload(); window.close();</script>";
    exit();			
}	
?>

<br />
<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">출고 처리</span></h2></center>
<div class="ln_solid"></div>		
<form name="popup_form" id="popup_form" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<input type="hidden" name="outbound_id" value="<?echo $_GET['arg2']?>" >
<?php
$result = getwms_wms_outbound_cnt($_GET['arg2']);
if (!empty($result)) {
    $planned_quantity = $result[0]['planned_quantity'];  						
    $outbound_quantity = $result[0]['outbound_quantity'];  						
    $able_outbound_quantity = $result[0]['able_outbound_quantity'];  						
    $plan_date = $result[0]['plan_date'];  						
} else {
    //echo "No data found.";
}	
?>

<script>
 
function submitForm() {
    var form = document.getElementById("popup_form");		

    var num1 = document.popup_form.able_outbound_quantity.value; // 입력받는 수량
    var num2 = document.getElementById("number2").value;	
	
    if (parseInt(num1) <= parseInt(num2) ){
        form.submit(); 
    } else {

        alert("출고수량을 정상적으로 입력해주세요.");
		return;
    }
}	
</script> 

<div class="item form-group">
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">총 예정수량</label>
    <div class="col-md-6 col-sm-6" style="margin-bottom:6px">
        <input class="form-control" type="text" name="planned_quantity" value="<?php echo $planned_quantity ?>"  readonly>
    </div>
</div>

<div class="item form-group">
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">출고된 수량 (이전)</label>
    <div class="col-md-6 col-sm-6" style="margin-bottom:6px">
        <input class="form-control" type="text" name="outbound_quantity" value="<?php echo $outbound_quantity ?>" readonly>
    </div>
</div>

<div class="item form-group">
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">출고수량 <span class="required" style="color:#ff0000">*</span> (이번)</label>
    <div class="col-md-6 col-sm-6" style="margin-bottom:6px">
        <input class="form-control" type="number" id="number1" name="able_outbound_quantity" min="1" max="<?php echo $able_outbound_quantity ?>" required>
    </div>
</div>  

<input id="number2" type="hidden" value="<?php echo $able_outbound_quantity ?>" >

<center><div><span style="color:#fff;margin-padding:10px">출고수량이 총예정수량과 같게되면, 자동 출고완료 처리됨</span></div></center><br>

<center>
<div style="text-align:center;">
    <button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
    <button type="button" class="btn btn-success" onclick="submitForm()">출고 등록</button>
</div>
</center>

<input type="hidden" name="plan_date" value="<? echo $plan_date;?>" >
</form>

<script>
document.popup_form.able_outbound_quantity.focus();
</script>

</body>
</html>
