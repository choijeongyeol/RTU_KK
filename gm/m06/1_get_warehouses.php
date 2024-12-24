 <?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/db_connection.php'); 

// 세션 시작
session_start();

// 세션에서 partner_id 가져오기
$partner_id = isset($_SESSION['partner_id']) ? $_SESSION['partner_id'] : null;

if ($partner_id === null) {
    // partner_id가 세션에 설정되어 있지 않은 경우, 세션 종료 및 리다이렉트
    session_unset();  // 모든 세션 변수 삭제
    session_destroy();  // 세션 종료
    header("Location: /login.php");  // 로그인 페이지로 리다이렉트 (경로는 실제 로그인 페이지 경로로 변경)
    exit();
}

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
    $stmt = $conn->prepare("SELECT  DISTINCT(warehouse_id), warehouse_name FROM wms_stock_details where  item_id = '".$item_id."'  and  partner_id = :partner_id   order by warehouse_name asc");
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT); // 바인딩
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
 
  