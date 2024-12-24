<?php include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
</head>
<body style="overflow: hidden;background:#405469">
<?

$wh_ids = search_warehouse_zero();

if ($wh_ids) {
    foreach ($wh_ids as $row) {
        $wh_id = $row['warehouse_id'];
      // echo "Found Warehouse ID: " . $wh_id . "<br>";

        // 각 창고 ID마다 앵글 ID 검색
        $ag_ids = search_angle_zero($wh_id);

        if ($ag_ids) {
            foreach ($ag_ids as $row2) {
                $ag_id = $row2['angle_id'];
            //    echo "Warehouse ID: " . $wh_id . ", Angle ID: " . $ag_id . "<br>";
            }
        } else {
            echo "앵글을 찾을 수 없거나 오류가 발생했습니다. (Warehouse ID: $wh_id)<br>";
        }
    }
} else {
    echo "창고를 찾을 수 없거나 오류가 발생했습니다.";
}

?>


<?php
 


// 오류 출력을 화면에 허용하기
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

?>


<?php
if(isset($_POST['item_id']) && ($_POST['item_id']!="") && isset($_POST['qua']) && ($_POST['qua']!="")){   

    // Check if any POST data exists
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Iterate through all POST data and display key-value pairs
        foreach ($_POST as $key => $value) {
            //echo "Key: " . htmlspecialchars($key) . ", Value: " . htmlspecialchars($value) . "<br>";
        }
        
    } else {
        //echo "No POST data received.";
    }

    $to_ware  = $_POST['to_ware'];
    $to_angle = $_POST['to_angle'];
    //$to_company = $_POST['to_company'];
    $step_1 = $_POST['step_1'];
    if ($to_ware=="") { $to_ware = $wh_id;}
    if ($to_angle=="") { $to_angle = $ag_id;}
    if ($step_1!="Y") { $step_1="N";}
 
          
    echo "<br>item_id".$_POST['item_id'];  
    echo "<br>to_ware".$to_ware;  
    echo "<br>to_angle".$to_angle;  
    echo "<br>qua".$_POST['qua'];  
    echo "<br>step_1".$step_1;  
    //echo "<br>to_company".$to_company;  
    echo "<br>";  
         
    //exit(); 
        
    // addStock($_POST['item_id'],$to_ware,$to_angle,$_POST['qua'],$step_1,'회사A'); // $_POST['step_1'] = Y 면 앵글에 보관 / 아니면 N
     addStock($_POST['item_id'],$to_ware,$to_angle,$_POST['qua'],$step_1); // $_POST['step_1'] = Y 면 앵글에 보관 / 아니면 N
    echo "<script> window.opener.location.reload(); window.close();</script>";
}       
?>

<?php // 리스트로부터 입고등록 버튼누르면 받는 item_id 유지
if($_GET['arg2']!="") $item_id=$_GET['arg2'];
if($_POST['item_id']!="") $item_id=$_POST['item_id'];
?>

<br />
<center><h2><span style="font-size:18px;font-weight:bold;color:#fff">제품 입고등록</span></h2></center>
<div class="ln_solid"></div>      
<form name="popup_form"  method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<input type="hidden" name="ID" class="w90" value="<?php echo $_SESSION['admin_name'] ?>" readonly>
<input type="hidden" name="save_YN" > <!-- 보관장소(앵글)선택값 보관 -->
<input type="hidden" name="to_ware_v" > <!-- 임시 창고선택값 보관 -->
<input type="hidden" name="to_angle_v" > <!-- 임시 앵글값 보관 -->
 <input type="hidden" name="cnt_v" > <!-- 카운트 보관 -->
<input type="hidden" name="item_id" value="<?php echo $item_id; ?>" > <!-- 카운트 보관 -->

<input type="hidden" name="step_1" value="<?php if ($_POST['save_YN']=="Y"){ echo "Y"; } ?>" > <!-- step_1 -->
<input type="hidden" name="step_2" > <!-- step_2 -->
<input type="hidden" name="step_3" > <!-- step_3 -->

<?php
if ($_POST['step_1']!=""){
    $step_1 = $_POST['step_1'];
}       
if ($_POST['step_2']!=""){
    $step_2 = $_POST['step_2'];
}       
if ($_POST['step_3']!=""){
    $step_3 = $_POST['step_3'];
}       
if ($_POST['save_YN']!=""){
    $save_YN = $_POST['save_YN'];
}       
if ($_POST['to_ware_v']!=""){
    $to_ware_v = $_POST['to_ware_v'];
}
if ($_POST['to_angle_v']!=""){
    $to_angle_v = $_POST['to_angle_v'];
}
?>

<div class="item form-group">
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">보관장소<span class="required" style="color:#ff0000">*</span></label>
    <div class="col-md-6 col-sm-6" style="margin-top:5px">
        <span style="color:#fff">지정</span> <input type="radio" name="save_in_angle" onclick="goto_step_1('Y')" <?php if ($save_YN=="Y") { echo "checked"; } ?>> &nbsp;&nbsp; / &nbsp;&nbsp; <span style="color:#fff"> 미지정</span>  <input type="radio" name="save_in_angle"  onclick="goto_step_1('N')"  <?php if ($save_YN=="N") { echo "checked"; } ?>> 
    </div>
</div>   

<?php if ($save_YN=="Y") {?>
<div class="item form-group">
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">창고 선택 <span class="required" style="color:#ff0000">*</span></label>
    <div class="col-md-6 col-sm-6" style="margin-top:5px">
        <select id="to_ware" name="to_ware" onchange="fn_step_1(this.value)"  style="width:200px">창고 선택
                        <option value='0'>--- 선택하세요 -----</option>       
        <?php
        $warehouses = getwms_warehouses_exist_angle(0,1000,'','');
            if ($warehouses) {
                $i=1;
                foreach ($warehouses as $warehouse) {
                    if ($to_ware_v == "{$warehouse['warehouse_id']}") {
                        $selected = "selected";
                    }else{
                        $selected = "";
                    }
                   // echo "<option value='{$warehouse['warehouse_id']}' ".$selected." >{$warehouse['warehouse_name']}</option>";
                    echo "<option value='{$warehouse['warehouse_id']}' ".$selected." >{$warehouse['warehouse_name']}</option>";
                    $i=$i+1;    
                }
            }   
        ?>
        </select>                
    </div>
</div>

<?php 
 }?>     

<?php if ($step_2=="Y") {?>
<div class="item form-group">
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">앵글 선택 <span class="required" style="color:#ff0000">*</span></label>
    <div class="col-md-6 col-sm-6" style="margin-top:5px">
    
<?php         
    $angle_lists = getwms_angle_namelist($to_ware_v);
    if (isset($angle_lists)) {    
?>
        <select id="to_angle" name="to_angle" required onchange="fn_step_2(this.value)" style="width:200px">앵글 선택
                        <option value='0'>--- 선택하세요 -----</option>       
        <?php
            if ($angle_lists) {
                $i=1;
                foreach ($angle_lists as $angle_list) {
                    if ($to_angle_v == "{$angle_list['angle_id']}") {
                        $selected = "selected";
                    }else{
                        $selected = "";
                    }
                    echo "<option value='{$angle_list['angle_id']}' ".$selected." >{$angle_list['angle_name']}</option>";
                    $i=$i+1;    
                }
            }   
        ?>
        </select>             
<?php   
    }  
}
?>              
    </div>
</div>          
                 
<?php if ($step_3=="Y") {?>
<div class="item form-group" >
    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align"  style="color:#fff">수량 <span class="required" style="color:#ff0000">*</span></label>
    <div class="col-md-6 col-sm-6" style="margin-bottom:0px">
        <input id="middle-name" class="form-control" type="number" name="qua"  required placeholder="최소수량 1이상"  style="width:200px">
    </div>
</div>

<center>
<div  style="text-align:center;">
     
        <button class="btn btn-primary" type="button" onclick="window.close()">취소</button>
        <!-- <button class="btn btn-primary" type="reset">리셋</button> -->
        <button type="submit" class="btn btn-success">입고등록 완료</button>
</div>
</center>        

<?php   
    }       
?>      

</form>

<script>
    //document.popup_form.angle_name.focus();
</script>

</body>
</html>
