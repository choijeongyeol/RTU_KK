<?php
require_once('db_connection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');
 
 
function update_sys_partner_use($partner_id, $admin_use) {
    global $conn;
	$delYN = "N";
	if ($admin_use=="Y") {
		$delYN = "N";
	}else{
		$delYN = "Y";
	}
    $stmt = $conn->prepare("UPDATE wms_admin SET admin_use = :admin_use , delyn = :delYN  WHERE partner_id = :partner_id");
    $stmt->bindParam(':admin_use', $admin_use);
    $stmt->bindParam(':partner_id', $partner_id);
    $stmt->bindParam(':delYN', $delYN);
    $stmt->execute();
	
	if ($admin_use=="D") {  // 파트너 삭제
		del_admin_partner($partner_id);
	}
}
 
 
function update_m03_cate_expose($cate_id, $cate_expose) {
    global $conn;
    $stmt = $conn->prepare("UPDATE wms_admin_cate SET cate_expose = :cate_expose WHERE cate_id = :cate_id");
    $stmt->bindParam(':cate_expose', $cate_expose);
    $stmt->bindParam(':cate_id', $cate_id);
    $stmt->execute();
}
 
function update_m03_cate_list($cate_id, $cate_use) {
    global $conn;
    $stmt = $conn->prepare("UPDATE wms_admin_cate SET cate_use = :cate_use WHERE cate_id = :cate_id");
    $stmt->bindParam(':cate_use', $cate_use);
    $stmt->bindParam(':cate_id', $cate_id);
    $stmt->execute();
}
  

if (isset($_POST['sn'])&&($_POST['sn']!="")&&isset($_POST['Process'])&&($_POST['Process']!="")) {
	if ($_POST['location2']=="m03_cate_list") {
		update_m03_cate_list($_POST['sn'],$_POST['Process']);			
	}elseif ($_POST['location2']=="m03_cate_expose") {
		update_m03_cate_expose($_POST['sn'],$_POST['Process']);			
	}elseif ($_POST['location2']=="sys_partner_use") {
		update_sys_partner_use($_POST['sn'],$_POST['Process']);			
	}else{
		update_status_input($_POST['sn'],$_POST['Process']);			
	}
	
	echo "<script> window.top.location.reload();</script>";
	echo "<script>alert('처리여부가 수정되었습니다.!!');</script>";		
	
}
  	
	
?> 