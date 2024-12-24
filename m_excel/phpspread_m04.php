<?php
// 출력 버퍼를 비웁니다.
ob_start();
session_start();
$partner_id = $_SESSION['partner_id'];

require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Initialize search conditions
$search_add = "";
$searchType = "ALL";
$keyword = "";
$searchStoreDateType = "STORE_EXPECTED_DATE";
$searchStartDate = "";
$searchEndDate = "";

if (isset($_GET['search_add']) && $_GET['search_add'] != "") {
    $search_add = $_GET['search_add'];
}

if (isset($_GET['searchType']) && $_GET['searchType'] != "" && $_GET['searchType'] != "ALL") {
    $searchType = $_GET['searchType'];
    $keyword = $_GET['keyword'];
    $search_add .= " and " . $searchType . " like :keyword ";
} else {
    $searchType = "ALL";
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
    if ($keyword != "") {
        $search_add = " and ((i.item_name like :keyword) ";
        $search_add .= " or (w.warehouse_name like :keyword) ";
        $search_add .= " or (a.angle_name like :keyword)) ";
    }
}

if (isset($_GET['searchStoreDateType']) && $_GET['searchStoreDateType'] != "" && $_GET['searchStoreDateType'] != "STORE_EXPECTED_DATE") {
    $searchStoreDateType = $_GET['searchStoreDateType'];
}

if (isset($_GET['searchStartDate']) && $_GET['searchStartDate'] != "" && isset($_GET['searchEndDate']) && $_GET['searchEndDate'] != "") {
    $searchStartDate = $_GET['searchStartDate'];
    $searchEndDate = $_GET['searchEndDate'];
    if ($searchStoreDateType == "STORE_EXPECTED_DATE") {
        $search_add .= " and date(s.rdate) between :start_date and :end_date ";
    }
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2

    if ($result_setting[0]['set_state'] == "N") {
        $add_sql = " AND quantity > 0";
    } else {
        $add_sql = " ";
    }

    $query = "SELECT s.*, i.item_name, IFNULL(w.warehouse_id, '/') AS warehouse_id_null, IFNULL(w.warehouse_id, '0') AS warehouse_id, IFNULL(w.warehouse_name, '배정안됨') AS warehouse_name, a.angle_name FROM wms_stock AS s LEFT JOIN wms_items AS i ON s.item_id = i.item_id LEFT JOIN wms_warehouses AS w ON s.warehouse_id = w.warehouse_id JOIN wms_angle AS a ON s.angle_id = a.angle_id AND i.partner_id = s.partner_id WHERE s.partner_id = :partner_id AND w.delYN = 'N' AND a.delYN = 'N' " . $add_sql . $search_add . " ORDER BY s.rdate DESC, s.item_id";

    // echo $query;
    // exit();

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':partner_id', $partner_id, PDO::PARAM_INT);

    if ($keyword != "") {
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    }
    if ($searchStartDate != "" && $searchEndDate != "") {
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
    'D' => array(15, 'quantity', '수량'),
    'E' => array(20, 'rdate', '업데이트일자')
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

$filename = "재고목록.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
