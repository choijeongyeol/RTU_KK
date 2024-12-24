<?php
// Start output buffering
ob_start();
session_start();
$partner_id = $_SESSION['partner_id'];

require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$add_condition = "";

// 입고상태
$searchStoreStatus = isset($_GET['searchStoreStatus']) ? $_GET['searchStoreStatus'] : 'ALL';
if ($searchStoreStatus != 'ALL' && $searchStoreStatus != '') {
    $add_condition .= " AND state = :searchStoreStatus";
}

// 검색어
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'ALL';
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
if ($keyword != '') {
    if ($searchType == 'ALL') {
			$add_condition = $add_condition." AND (( SELECT warehouse_name FROM wms_warehouses WHERE warehouse_id = i.warehouse_id ) LIKE :keyword  ";	
			$add_condition = $add_condition." or ( SELECT angle_name FROM wms_angle WHERE angle_id = i.angle_id ) LIKE :keyword ";	
			$add_condition = $add_condition." or  item_name LIKE :keyword)";		
    } elseif ($searchType == 'warehouse_name') {
        $add_condition .= " AND (SELECT  warehouse_name FROM wms_warehouses WHERE warehouse_id = i.warehouse_id ) LIKE :keyword";
    } elseif ($searchType == 'angle_name') {
        $add_condition .= " AND (SELECT  angle_name FROM wms_angle WHERE angle_id = i.angle_id) LIKE :keyword";
    } elseif ($searchType == 'item_name') {
        $add_condition .= " AND item_name LIKE :keyword";
    }
}

// 날짜 종류 선택 (입고예정일 or 입고일)
$searchStoreDateType = isset($_GET['searchStoreDateType']) ? $_GET['searchStoreDateType'] : 'STORE_EXPECTED_DATE';

// 날짜 데이터 (시작일 / 끝일)
$searchStartDate = isset($_GET['searchStartDate']) ? $_GET['searchStartDate'] : '';
$searchEndDate = isset($_GET['searchEndDate']) ? $_GET['searchEndDate'] : '';
if ($searchStartDate != '' && $searchEndDate != '') {
    if ($searchStoreDateType == 'STORE_EXPECTED_DATE') {
        $add_condition .= " AND plan_date BETWEEN :start_date AND :end_date";
    } else {
        $add_condition .= " AND rdate BETWEEN :start_date AND :end_date";
    }
}


    $query = "SELECT i.inbound_id, p.item_name, w.warehouse_name,  (SELECT angle_name FROM wms_angle WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id) AS angle_name,  (SELECT cate_name FROM wms_company WHERE cate_id = i.company_id) AS company_name,  i.planned_quantity as planned_quantity, i.inbound_quantity as inbound_quantity, i.plan_date as plan_date, i.rdate as rdate, i.state as state FROM wms_inbound i JOIN wms_items p ON p.item_id = i.product_id JOIN wms_warehouses w ON w.warehouse_id = i.warehouse_id  WHERE 1=1 and   i.delYN = 'N' and i.partner_id = ".$partner_id .$add_condition. " ORDER BY i.plan_date DESC, i.inbound_id DESC";
 

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    $stmt = $pdo->prepare($query);

    if ($keyword != '') {
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    }
    if ($searchStoreStatus != 'ALL' && $searchStoreStatus != '') {
        $stmt->bindValue(':searchStoreStatus', $searchStoreStatus, PDO::PARAM_STR);
    }
    if ($searchStartDate != '' && $searchEndDate != '') {
        $stmt->bindValue(':start_date', $searchStartDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $searchEndDate, PDO::PARAM_STR);
    }

    $stmt->execute();
    $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$cells = array(
    'A' => array(20, 'item_name', '제품명'),
    'B' => array(20, 'warehouse_name', '창고명'),
    'C' => array(20, 'angle_name', '앵글명'),
    'D' => array(15, 'planned_quantity', '예정수량'),
    'E' => array(20, 'inbound_quantity', '입고수량'),
    'F' => array(20, 'plan_date', '입고예정일'),
    'G' => array(20, 'rdate', '입고일자'),
    'H' => array(20, 'state', '상태')
);

foreach ($cells as $key => $val) {
    $cellName = $key . '1';
    $sheet->getColumnDimension($key)->setWidth($val[0]);
    $sheet->getRowDimension('1')->setRowHeight(25);
    $sheet->setCellValue($cellName, $val[2]);
    $sheet->getStyle($cellName)->getFont()->setBold(true);
    $sheet->getStyle($cellName)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($cellName)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
}

for ($i = 2; $row = array_shift($datas); $i++) {
    foreach ($cells as $key => $val) {
        $cellValue = $row[$val[1]];
        $sheet->setCellValueExplicit($key . $i, $cellValue, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->getStyle($key . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($key . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle($key . $i)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle($key . $i)->getAlignment()->setWrapText(true);
    }
}

// Clear the output buffer
ob_end_clean();

$filename = "입고지시목록.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
