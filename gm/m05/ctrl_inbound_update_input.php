<?php include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
</head>
<body style="overflow: hidden;background:#405469"> 

<?php

$plan_date ="";  
if (isset($_GET['plan_date'])) {$plan_date = $_GET['plan_date'];}
if (isset($_POST['plan_date'])) {$plan_date = $_POST['plan_date'];}
if (isset($_GET['planned_quantity'])) {$planned_quantity = $_GET['planned_quantity'];}
if (isset($_POST['planned_quantity'])) {$planned_quantity = $_POST['planned_quantity'];}


if(isset($_POST['able_inbound_quantity']) && ($_POST['able_inbound_quantity']!="")){  		
    //$warehouse_cnt = update_warehouse_cnt($_POST['warehouse_id'],$_POST['warehouse_name']);	// 중복확인

    update_inbound($_POST['inbound_id'],$_POST['able_inbound_quantity'],$plan_date,$planned_quantity);
    echo "<script>window.opener.location.reload(); window.close();</script>";
    exit();			
}	
?>

<br />
<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">입고 처리</span></h2></center>
<div class="ln_solid"></div>		
<form name="popup_form" id="popup_form" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<input type="hidden" name="inbound_id" value="<?echo $_GET['arg2']?>" >
<?php
$result = getwms_wms_inbound_cnt($_GET['arg2']);
if (!empty($result)) {
    $planned_quantity = $result[0]['planned_quantity'];  						
    $inbound_quantity = $result[0]['inbound_quantity'];  						
    $able_inbound_quantity = $result[0]['able_inbound_quantity'];  	
    $plan_date = $result[0]['plan_date'];  		
} else {
    //echo "No data found.";
}	
?>

<script>
 
function submitForm() {
    var form = document.getElementById("popup_form");		


    var before_number = document.getElementById("before_number").value;	// 이전 입고된 수량
    
    var num1 = document.popup_form.able_inbound_quantity.value; // 입력받는 수량
    var num2 = document.getElementById("number2").value;	
	
    var ispossible = parseInt(num1) + parseInt(before_number);
    
	if (parseInt(ispossible) < 0 )	{  alert("입고수량이 음수입니다.정상입력바랍니다."); return; }
 
	
    if (parseInt(num1) <= parseInt(num2) ){
        form.submit(); 
    } else {

        alert("입고수량을 정상적으로 입력해주세요.");
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
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">입고된 수량 (이전)</label>
    <div class="col-md-6 col-sm-6" style="margin-bottom:6px">
        <input class="form-control" type="text" name="inbound_quantity" value="<?php echo $inbound_quantity ?>" readonly>
    </div>
</div>

<div class="item form-group">
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">입고수량 <span class="required" style="color:#ff0000">*</span> (이번)</label>
    <div class="col-md-6 col-sm-6" style="margin-bottom:6px">
        <input class="form-control" type="number" id="number1" name="able_inbound_quantity" min="1" max="<?php echo $able_inbound_quantity ?>" required>
    </div>
</div>  

<input id="before_number" type="hidden" value="<?php echo $inbound_quantity ?>" >

<input id="number2" type="hidden" value="<?php echo $able_inbound_quantity ?>" >
<input id="plan_date" type="hidden" value="<?php echo $plan_date?>" >

<center><div><span style="color:#fff;margin-padding:10px">입고수량이 총예정수량과 같게되면, 자동 입고완료 처리됨</span></div></center><br>

<center>
<div style="text-align:center;">
    <button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
    <button type="button" class="btn btn-success" onclick="submitForm()">입고 등록</button>
</div>
</center>

</form>

<script>
document.popup_form.able_inbound_quantity.focus();
</script>

</body>
</html>
