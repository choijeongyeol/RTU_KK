<?php
// 출력 버퍼를 비웁니다.
ob_start();
session_start();
$partner_id = $_SESSION['partner_id'];

require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
// 기본 검색 조건 초기화
$search_add = "";
$keyword = "";
$searchStartDate = "";
$searchEndDate = "";

if (isset($_GET['search_add']) && $_GET['search_add'] != "") {
    $search_add = $_GET['search_add'];
}

$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : "ALL";
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";

if ($searchType == "ALL" && $keyword != "") {
    $search_add .= " and (i.item_name LIKE :keyword OR c.cate_name LIKE :keyword OR i.item_code LIKE :keyword) ";
	
} elseif ($keyword != "") {
	if ($searchType=="item_cate") {
		$search_add .= " AND (c.cate_name LIKE :keyword) ";
	}else{
		$search_add .= " AND (i.$searchType LIKE :keyword) ";
	}	
}

$searchStoreDateType = isset($_GET['searchStoreDateType']) ? $_GET['searchStoreDateType'] : "STORE_EXPECTED_DATE";
$searchStartDate = isset($_GET['searchStartDate']) ? $_GET['searchStartDate'] : "";
$searchEndDate = isset($_GET['searchEndDate']) ? $_GET['searchEndDate'] : "";

if ($searchStartDate != "" && $searchEndDate != "") {
    if ($searchStoreDateType == "STORE_EXPECTED_DATE") {
        $search_add .= " and date(s.rdate) between :start_date and :end_date ";
    }
}

$list_condition = " wms_stock AS s LEFT JOIN wms_items AS i ON s.item_id = i.item_id LEFT JOIN wms_warehouses AS w ON s.warehouse_id = w.warehouse_id JOIN wms_angle AS a ON s.angle_id = a.angle_id WHERE 1=1 and w.delYN = 'N' AND a.delYN = 'N'  AND i.partner_id = ".$partner_id . $search_add;

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 기본 쿼리
    $query = "SELECT c.cate_expose AS item_expose, c.cate_name AS item_cate, i.item_id AS item_id, i.item_code AS item_code, i.item_name AS item_name, LEFT(i.item_rdate, 10) AS item_rdate, i.item_cate AS item_cate_num, IFNULL((SELECT SUM(quantity) AS count FROM wms_stock WHERE delYN = 'N' AND item_id = i.item_id), 0) AS sum_quantity_item FROM wms_items AS i INNER JOIN wms_cate AS c ON i.item_cate = c.cate_id WHERE i.delYN = 'N'   AND i.partner_id = ".$partner_id . $search_add . " ORDER BY item_rdate DESC";

    $stmt = $pdo->prepare($query);
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
    'A' => array(15, 'item_cate', '분류명'),
    'B' => array(20, 'item_name', '제품명'),
    'C' => array(20, 'item_code', '바코드 숫자'),
    'D' => array(20, 'item_rdate', '등록일')
);

foreach ($cells as $key => $val) {
    $cellName = $key.'1';
    $sheet->getColumnDimension($key)->setWidth($val[0]);
    $sheet->getRowDimension('1')->setRowHeight(25);
    $sheet->setCellValue($cellName, $val[2]);
    $sheet->getStyle($cellName)->getFont()->setBold(true);
    $sheet->getStyle($cellName)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($cellName)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
}

// 데이터 스타일 설정 및 가운데 정렬
for ($i = 2; $row = array_shift($datas); $i++) {
    foreach ($cells as $key => $val) {
        $cellValue = $row[$val[1]];
        $sheet->setCellValueExplicit($key.$i, $cellValue, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->getStyle($key.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($key . $i)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle($key.$i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }
}

// 출력 버퍼를 비우고 닫습니다.
ob_end_clean();

$filename = "제품목록.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
