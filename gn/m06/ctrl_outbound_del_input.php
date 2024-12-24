<?php include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
</head>
<body style="overflow: hidden; background:#405469">

<?php
if(isset($_POST['item_name']) && ($_POST['item_name']!="")){  		
     del_outbound($_POST['outbound_id']); // 주석처리된 함수 호출을 해제하여 사용할 수 있습니다.
    //echo "<script>alert('test');</script>"; // 테스트 용도로 메시지를 출력합니다.
	echo "<script> window.opener.location.reload(); window.close();</script>";
    exit();
}
?>

<br />
<center><h2><span style="font-size:18px; font-weight:bold; color:#fff">출고지시 삭제</span></h2></center>
<div class="ln_solid"></div>

<script>
function submitForm() {
    var confirmDelete = confirm("삭제 후 복구는 되지 않습니다. 정말 삭제하시겠습니까?");
    if (confirmDelete) {
        var form = document.getElementById("popup_form");
        form.submit();
    }
}
</script>

<?php
$result = getwms_wms_outbound_info($_GET['arg2']);
if (!empty($result)) {
    $item_name = $result[0]['item_name'];  						
    $warehouse_name = $result[0]['warehouse_name'];  						
    $angle_name = $result[0]['angle_name'];  						
} else {
    //echo "No data found.";
}	
?>

<form name="popup_form" id="popup_form" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
    <input type="hidden" name="outbound_id" value="<?php echo $_GET['arg2'] ?>" >

    <div class="item form-group">
        <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align" style="color:#fff">제품명</label>
        <div class="col-md-6 col-sm-6" style="margin-bottom:6px">
            <input class="form-control" type="text" name="item_name" value="<?php echo $item_name ?>" readonly >
        </div>
    </div>

    <div class="item form-group">
        <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align" style="color:#fff">창고명</label>
        <div class="col-md-6 col-sm-6" style="margin-bottom:6px">
            <input class="form-control" type="text" name="warehouse_name" value="<?php echo $warehouse_name ?>" readonly>
        </div>
    </div>

    <div class="item form-group">
        <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align" style="color:#fff">앵글명 <span class="required" style="color:#ff0000">*</span></label>
        <div class="col-md-6 col-sm-6" style="margin-bottom:6px">
            <input class="form-control" type="text" id="angle_name" name="angle_name"  value="<?php echo $angle_name ?>" readonly>
        </div>
    </div>  

    <center><div><span style="color:#fff; margin-padding:10px">삭제 후 복구는 되지 않습니다.</span></div></center><br>

    <center>
        <div style="text-align:center;">
            <button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
            <button type="button" class="btn btn-success" style="background:#ff0000" onclick="submitForm()">삭제완료</button>
        </div>
    </center>

</form>
 
</body>
</html>
