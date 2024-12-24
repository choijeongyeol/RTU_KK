<?php
// db_connection.php 파일을 include하여 데이터베이스 연결
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/db_connection.php'); 

// 시작 날짜와 종료 날짜를 GET으로 받아옴
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

try {
    // 모든 날짜를 포함하는 날짜 범위를 생성합니다.
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end_date);
    $realEnd->add($interval);
    $period = new DatePeriod(new DateTime($start_date), $interval, $realEnd);

    // 날짜 범위 내에서 각 날짜의 입고량을 조회하고 데이터를 구성합니다.
    $query = "SELECT DATE(rdate) AS date, COALESCE(SUM(quantity), 0) AS total_quantity "; // COALESCE 함수를 사용하여 입고량이 없는 경우 0으로 반환
    $query .= "FROM wms_in_stock_history "; 
    $query .= "WHERE DATE(rdate) BETWEEN :start_date AND :end_date "; 
    $query .= "GROUP BY DATE(rdate)";
    $statement = $conn->prepare($query);
    
    $response = array();
    $dates = array();
    $quantities = array();

    // 날짜 범위 내에서 각 날짜의 입고량을 조회합니다.
    foreach ($period as $date) {
        $statement->execute(array(':start_date' => $date->format('Y-m-d'), ':end_date' => $date->format('Y-m-d')));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $dates[] = $date->format('Y-m-d');
        $quantities[] = isset($result['total_quantity']) ? (int)$result['total_quantity'] : 0;
    }

    // 결과를 JSON 형식으로 반환합니다.
    $response['dates'] = $dates;
    $response['quantities'] = $quantities;
    echo json_encode($response);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
