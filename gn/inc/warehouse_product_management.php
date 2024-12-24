<?php
require_once('db_connection.php');

// 창고 추가
function addWarehouse($code, $name) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_warehouses (warehouse_code, warehouse_name, warehouse_rdate) VALUES (:code, :name, :rdate)");
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':rdate',date("Y-m-d H:i:s"));
    $stmt->execute();
}

// 제품 추가
function addItem($code, $name) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_items (item_code, item_name,item_rdate) VALUES (:code, :name, :rdate)");
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':rdate',date("Y-m-d H:i:s"));
    $stmt->execute();
}

// 창고 목록 가져오기
function getwms_warehouses() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_warehouses");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 제품 목록 가져오기
function getwms_items() {
    global $conn;
    $stmt = $conn->query("SELECT c.cate_name item_cate, i.item_id item_id, i.item_code item_code, i.item_name item_name, i.item_rdate item_rdate  FROM wms_items as i, wms_cate as c where i.item_cate = c.cate_id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
 
 // 입고지시관리 목록 가져오기
function getwms_input_reservation() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_input_reservation order by ip_id desc");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
 
// 입고지시관리 추가
function addip($ip_item_name,$ip_quantity,$ip_date,$ip_state) {
     global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_input_reservation (ip_item_name, ip_quantity, ip_date, ip_state, rdate) VALUES (:ip_item_name, :ip_quantity, :ip_date, :ip_state, :rdate) ON DUPLICATE KEY UPDATE ip_quantity = ip_quantity + :ip_quantity ");
    $stmt->bindParam(':ip_item_name', $ip_item_name);
    $stmt->bindParam(':ip_quantity', $ip_quantity);
    $stmt->bindParam(':ip_date', $ip_date);
    $stmt->bindParam(':ip_state', $ip_state);
    $stmt->bindParam(':rdate',date("Y-m-d H:i:s"));
    $stmt->execute();
} 
 
 
// 제품카테고리 조회
function getwms_cate() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_cate");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
// 특정제품카테고리 1가지 조회 
function getwms_cate_search1($cate_id) {
    global $conn;
    $stmt = $conn->query("SELECT cate_name FROM wms_cate where cate_id = $cate_id limit 0,1");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  
 // 제품카테고리 추가
function addCate($cate_name) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_cate (cate_name,cate_rdate) VALUES (:cate_name,:cate_rdate)");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_rdate',date("Y-m-d H:i:s"));
    $stmt->execute();
}

 // 제품카테고리 업데이트
function updateCate($cate_name,$cate_id) {
    global $conn;
    $stmt = $conn->prepare("update wms_cate set cate_name = :cate_name where cate_id = :cate_id");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_id',$cate_id);
    $stmt->execute();
}

?>
