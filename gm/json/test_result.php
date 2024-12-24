<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/db_connection.php');

// 입력 폼에서 받은 값이 있을 때만 실행
if (isset($_GET['num1']) && $_GET['num1'] == 1) {
	
    // 창고 ID와 앵글 ID를 변수에 저장
    $warehouse_id = $_GET['warehouse_id'];
    $angle_id = $_GET['angle_id'];

    // stock_list 함수 호출
    $result = stock_list($warehouse_id, $angle_id);

    // JSON 형식으로 결과 반환
    header('Content-Type: application/json');
    echo json_encode($result);
}

// 특정앵글안에 제품 리스트
function stock_list($warehouse_id, $angle_id) {
    global $conn;
    $add_sql = ""; // 초기화

    // $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2
    // if ($result_setting[0]['set_state']=="N") {
    //     $add_sql = "  and s.quantity > 0"; 
    // } else {
    //     $add_sql = " "; 
    // }	
       
    $stmt = $conn->query("select i.item_id, i.item_name, (select cate_name from wms_cate where i.item_cate = cate_id ) as cate_name, (SELECT sum(quantity) as sum_quantity FROM wms_stock where warehouse_id = $warehouse_id and angle_id = $angle_id and item_id = i.item_id ) as item_cnt from wms_stock s left join wms_items i on  s.item_id = i.item_id  where s.warehouse_id = $warehouse_id and s.angle_id = $angle_id".$add_sql);

    // fetchAll 함수를 사용하여 결과를 배열로 변환하여 반환
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
