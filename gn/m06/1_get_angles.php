 <?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php'); 

// 선택한 제품, 거래처, 창고 정보 받아오기
$item_id = $_POST['item_id'];
$company_id = $_POST['company_id'];
$warehouse_id = $_POST['warehouse_id'];

try {
    // MySQL 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 오류 출력을 위한 예외 처리
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 창고 목록 가져오기
    //$stmt = $conn->prepare("SELECT  DISTINCT(angle_id), angle_name FROM wms_stock_details where item_id = '".$item_id."' AND company_id = '".$company_id."' AND warehouse_id = '".$warehouse_id."' order by angle_name asc");
    $stmt = $conn->prepare("SELECT  DISTINCT(angle_id), angle_name FROM wms_stock_details where item_id = '".$item_id."'  AND warehouse_id = '".$warehouse_id."' order by angle_name asc");
    $stmt->execute();
    $angle_names = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	    echo "<option value=''>앵글 선택</option>";
    // 가져온 업체 목록을 selectbox 형태로 출력
    foreach ($angle_names as $angle_name) {
        echo "<option value='" . $angle_name['angle_id'] . "'>" . $angle_name['angle_name'] . "</option>";
    }
} catch(PDOException $e) {
    // 오류 발생 시 에러 메시지 출력
    echo "오류: " . $e->getMessage();
}

// MySQL 연결 종료
$conn = null;
?>
 
  