 <?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php'); 

// POST로 전달된 제품 ID를 가져옵니다.
$item_id = $_POST['item_id'];
$company_id = $_POST['company_id'];

try {
    // MySQL 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 오류 출력을 위한 예외 처리
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 창고 목록 가져오기
   // $stmt = $conn->prepare("SELECT  DISTINCT(warehouse_id), warehouse_name FROM wms_stock_details where  item_id = '".$item_id."'  and company_id = '".$company_id."' order by warehouse_name asc");
    $stmt = $conn->prepare("SELECT  DISTINCT(warehouse_id), warehouse_name FROM wms_stock_details where  item_id = '".$item_id."'  order by warehouse_name asc");
    $stmt->execute();
    $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	    echo "<option value=''>창고 선택</option>";
    // 가져온 업체 목록을 selectbox 형태로 출력
    foreach ($warehouses as $warehouse) {
        echo "<option value='" . $warehouse['warehouse_id'] . "'>" . $warehouse['warehouse_name'] . "</option>";
    }
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
 
  