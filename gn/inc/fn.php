<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php');

class UserManager {
    private $conn;


    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 사용자 등록
    public function registerUser(string $user_id, string $user_pw, string $user_role): void {
        $user_pw = password_hash($user_pw, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO wms_user (user_id, user_pw, user_role) VALUES (:user_id, :user_pw, :user_role)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_pw', $user_pw);
        $stmt->bindParam(':user_role', $user_role);
        $stmt->execute();
    }

    // 사용자 로그인
    public function loginUser(string $user_id, string $user_pw): bool {
        $stmt = $this->conn->prepare("SELECT * FROM wms_user WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($user_pw, $user['user_pw'])) {
            $sessionLifetime = 86400; // 24시간(1일)
            
            // 세션 설정을 session_start() 이전에 설정
            session_set_cookie_params($sessionLifetime);
            session_cache_limiter('private');
            ini_set("session.cookie_lifetime", (string)$sessionLifetime);
            ini_set("session.cache_expire", (string)$sessionLifetime);
            ini_set("session.gc_maxlifetime", (string)$sessionLifetime);

            session_start();

            $_SESSION['user_idx']   = $user['user_idx'];
            $_SESSION['user_id']    = $user['user_id'];
            $_SESSION['user_name']  = $user['user_name'];
            $_SESSION['user_role']  = $user['user_role'];

            return true;    
        } else {
            // 로그인 실패 시 별도의 기록은 남기지 않음
            return false;
        }
    }

    // 사용자 로그아웃
    public function logoutUser(): void {
		
        session_start();
        
        session_unset();
        session_destroy();
    }

    // 사용자 권한 확인
    public function checkUserRole(): array|false {
        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return false; // 사용자가 로그인하지 않았음
        }

        $stmt = $this->conn->prepare("SELECT user_role FROM wms_user WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $userRole = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $userRole; // 권한 반환
    }
}

$userManager = new UserManager($conn);

 
 
 
 function user_id_to_user_name(string $user_id): array {
    global $conn;	 
    $stmt = $conn->prepare("SELECT user_name FROM wms_user WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
 
 
 /////////////////////////////////////    이하 관리자모드 ///////////////////////////////////////////////////////

class AdminManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 사용자 등록
    public function registerUser(string $admin_id, string $admin_pw, string $admin_role): void {
        $admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO wms_admin (admin_id, admin_pw, admin_role) VALUES (:admin_id, :admin_pw, :admin_role)");
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':admin_pw', $admin_pw);
        $stmt->bindParam(':admin_role', $admin_role);
        $stmt->execute();
    }

    // 사용자 로그인
    public function loginAdmin(string $admin_id, string $admin_pw): bool {
        $stmt = $this->conn->prepare("SELECT * FROM wms_admin WHERE admin_id = :admin_id");
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($admin_pw, $user['admin_pw'])) {
			
		   session_start();

			$sessionLifetime = 86400; // 24시간(1일)
			
			// 세션 쿠키 수명 설정
			session_set_cookie_params($sessionLifetime);
			
			// 세션 캐시 제어 설정
			session_cache_limiter('private'); 
			
			// PHP 세션 설정 변경
			ini_set("session.cookie_lifetime", $sessionLifetime); 
			ini_set("session.cache_expire", $sessionLifetime); 
			ini_set("session.gc_maxlifetime", $sessionLifetime); 

			$_SESSION['admin_idx']   = $user['admin_idx'];
			$_SESSION['admin_id']    = $user['admin_id'];
			$_SESSION['admin_name']    = $user['admin_name'];
			$_SESSION['admin_role']  = $user['admin_role'];   
			
			
			echo "123";
			add_history('A','로그인 성공',$_SESSION['admin_role'],'');    
			return true;    
			
        } else {
			$admin_name_search = admin_id_to_admin_name($admin_id);			
			add_history('A','로그인 실패',$admin_name_search[0]['admin_name'],$admin_id);			
            return false;
        }
    }

    // 사용자 로그아웃
    public function logoutAdmin(): void {     
	    
        session_start();
        add_history('A', '로그아웃', $_SESSION['admin_role'], '');                    
        
        session_unset();
        session_destroy();
    }

    // 사용자 권한 확인
    public function checkUserRole(): array|false {
        session_start();

        if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
            return false; // 사용자가 로그인하지 않았음
        }

        $stmt = $this->conn->prepare("SELECT admin_role FROM wms_admin WHERE admin_id = :admin_id");
        $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        $stmt->execute();
        $userRole = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $userRole; // 권한 반환
    }
}

$adminManager = new AdminManager($conn);
 
 
function admin_id_to_admin_name(string $admin_id): array {
    global $conn;	
    $stmt = $conn->prepare("SELECT admin_name FROM wms_admin WHERE admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * HOME * //
 
 //  history information
function get_history_personal(int $start_record_number, int $itemsPerPage, string $add_condition, string $admin_id, int $admin_role, string $search = "", string $SearchString = ""): array {
    global $conn;	
    // 검색조건
    $condition_sql = "";
    if ($SearchString !== "") {
        // $condition_sql = " and  ".$search." like '%".$SearchString."%' ";	
    } else {
        // $condition_sql = "";	
    }

    if ($admin_role < 91) {
        $stmt = $conn->prepare("SELECT * FROM wms_history WHERE 1=1 " . $add_condition . " AND h_id = :admin_id ORDER BY h_date DESC LIMIT :start_record_number, :itemsPerPage");
        $stmt->bindParam(':admin_id', $admin_id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM wms_history WHERE 1=1 " . $add_condition . " ORDER BY h_date DESC LIMIT :start_record_number, :itemsPerPage");
    }
    $stmt->bindValue(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


 //  history information
function get_history_item_list_cate_personal(string $item, string $h_loc_code, string $admin_id): array {
    global $conn;
	$stmt = $conn->prepare("SELECT DISTINCT $item, h_loc_code, h_location FROM wms_history WHERE h_id = :admin_id AND h_loc_code = :h_loc_code ORDER BY $item ASC");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':h_loc_code', $h_loc_code);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 창고관리 * //



// 창고 추가
function addWarehouse(string $code, string $name): void {
    global $conn;
	$date = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO wms_warehouses (warehouse_code, warehouse_name, warehouse_rdate) VALUES (:code, :name, :rdate)");
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':rdate', $date);
    $stmt->execute();

    // 히스토리 추가
    add_history('A', '창고를 등록', $name, '');

    //$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$name."','','창고를 등록','m02')");
    //$stmt_history->execute();
}



function duplicate_warehouse_name(string $name): string {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_warehouses WHERE warehouse_name = :name and delYN='N'");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] != 0) {
        echo "<script>alert('이미 [$name] 있음. 창고명 변경후 등록바람.');</script>";
        return "N";
    } else {
        return "Y";
    }
}



function duplicate_angle_name(string $name, int $warehouse_id): string {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_angle WHERE angle_name = :name and warehouse_id = :warehouse_id and delYN='N'");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] != 0) {
        echo "<script>alert('이미 [$name] 있음. 앵글명 변경후 등록바람.');</script>";
        return "N";
    } else {
        return "Y";
    }
}




function make_warehose_code(){
	
	$result = getwms_warehouse_last1();

	// 특정 데이터 1개 추출
	if (!empty($result)) {
		$specificData = $result[0]['warehouse_id'];  
		$specificData = $specificData + 1000;
	}
	   $specificData = "W".$specificData;
	   
	   return $specificData;
}


function make_angle_code(){
	
	$result = getwms_warehouse_last1();

	// 특정 데이터 1개 추출
	if (!empty($result)) {
		$specificData = $result[0]['warehouse_id'];  
		$specificData = $specificData + 1000;
	}
	   $specificData = "W".$specificData;
	   
	   return $specificData;
}

// 제품코드 생성
function make_product_code($code_plus){
	
    global $conn;
	
	$current_hour = date("G");
	$current_minute = date("i");
	$current_sec = date("s");
	$current_seconds_of_the_day = $current_sec + $current_minute*60 + $current_hour*60*60;
	
	$date = date("Ymd");
	$specificData = $date*100000 + $current_seconds_of_the_day;	
 
    try {
        // 데이터베이스에서 중복된 값 확인
        $stmt = $conn->prepare("SELECT item_code FROM wms_items WHERE item_code = :specificData");
        $stmt->bindParam(':specificData', $specificData);
        $stmt->execute();
        $existing_code = $stmt->fetchColumn();

        if ($existing_code === false) {
            // 중복된 값이 없으면 현재 값을 바로 리턴
            return $specificData;
        } else {
            // 중복된 값이 있으면 해당 값에 +1 한 값을 리턴
            return $existing_code + $code_plus;
        }
    } catch (PDOException $e) {
        // 예외 처리: DB 오류 발생 시 기본 값(현재 시간 기반)을 리턴
        return $specificData;
    }
}




// 특정창고 가져오기
function getwms_warehouse_name($warehouse_id) {
    global $conn;
 
    $stmt = $conn->query("SELECT IFNULL(max(warehouse_id), '0') as warehouse_id FROM wms_warehouses where delYN = 'N'  and  warehouse_id = ".$warehouse_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 특정창고 1가지 조회 
function select_warehouse_one(int $warehouse_id): array {
    global $conn;
	$stmt = $conn->prepare("SELECT warehouse_name FROM wms_warehouses WHERE delYN = 'N' AND warehouse_id = :warehouse_id LIMIT 1");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 앵글테이블안에 창고ID가 있는지 확인, 삭제가능한 창고인지 조회 0 이면, 창고삭제가능
function stock_warehouse_count(int $warehouse_id): int {
    global $conn;
	$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_angle WHERE angle_use = 'Y' AND delYN = 'N' AND warehouse_id = :warehouse_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}  

 // 앵글삽입시 앵글명 중복검사  
function ck_angle_cnt(int $warehouse_id, string $angle_name): array {
    global $conn;
	$stmt = $conn->prepare("SELECT IFNULL(COUNT(angle_id),'0') AS angle_cnt FROM wms_angle WHERE delYN = 'N' AND warehouse_id = :warehouse_id AND angle_name = :angle_name");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_name', $angle_name);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 창고 삭제 
function del_warehouse(int $warehouse_id) {
    global $conn;
	$stmt0 = $conn->prepare("UPDATE wms_angle SET delYN = 'Y', angle_use = 'N' WHERE warehouse_id = :warehouse_id AND delYN = 'N'");
    $stmt0->bindParam(':warehouse_id', $warehouse_id);
    $stmt0->execute();    
    
    $stmt = $conn->prepare("UPDATE wms_warehouses SET delYN = 'Y' WHERE warehouse_id = :warehouse_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();    
    
    $warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
    add_history('A', '창고를 삭제', $warehouse_name[0]['warehouse_name'], $warehouse_name);    
}

// 창고앵글 삽입시, ID값 +1 하기전 최대값추출
function getwms_max_angle($warehouse_id) {
    global $conn;
 
    $stmt = $conn->query("SELECT IFNULL(max(angle_id), '0') as angle_id  FROM wms_angle");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
// 정렬순서 변경up
function order_up_angle(int $warehouse_id, int $angle_id, int $angle_order): void {
     global $conn;
	$stmt = $conn->prepare("UPDATE wms_angle AS a1   JOIN (            SELECT angle_id, MIN(angle_order) + 1 AS change_order             FROM wms_angle             WHERE delYN = 'N' AND warehouse_id = :warehouse_id AND angle_order > :angle_order        ) AS a2 ON a1.angle_id = :angle_id         SET a1.angle_order = a2.change_order");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->bindParam(':angle_order', $angle_order);
    $stmt->execute();
}

// 정렬순서 변경down
function order_down_angle(int $warehouse_id, int $angle_id, int $angle_order): void {
     global $conn;
	 $stmt = $conn->prepare("UPDATE wms_angle AS a1         JOIN (            SELECT angle_id, MAX(angle_order) - 1 AS change_order             FROM wms_angle             WHERE delYN = 'N' AND warehouse_id = :warehouse_id AND angle_order < :angle_order        ) AS a2 ON a1.angle_id = :angle_id         SET a1.angle_order = a2.change_order");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->bindParam(':angle_order', $angle_order);
    $stmt->execute();
}

// 최근생성창고ID 가져오기
function getwms_warehouse_last1(): array {
    global $conn;	
    $stmt = $conn->query("SELECT IFNULL(MAX(warehouse_id), 1) AS warehouse_id FROM wms_warehouses");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 최근생성제품ID 가져오기, 코드명 부여
function getwms_item_last1(): array {
    global $conn;	
    $stmt = $conn->query("SELECT IFNULL(item_id, '0') AS item_id FROM wms_items ORDER BY item_rdate DESC LIMIT 1");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
// 앵글등록 
// Add angle
function add_angle(int $warehouse_id, string $angle_name): void {
    global $conn;
	$date = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO wms_angle (angle_name, warehouse_id, angle_rdate) VALUES (:angle_name, :warehouse_id, :angle_rdate)");
    $stmt->bindParam(':angle_name', $angle_name);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_rdate', $date);
    $stmt->execute();    
    
    $warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
    
    add_history('A', '앵글을 삽입', $warehouse_name[0]['warehouse_name'], $angle_name);
}

 // 앵글명 업데이트  
function update_angle(int $warehouse_id, int $angle_id, string $angle_name): void {
    global $conn;
	$angle_name_before = angle_id_to_angle_name($angle_id);
    $warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
    
    $stmt = $conn->prepare("UPDATE wms_angle SET angle_name = :angle_name, angle_rdate = :angle_rdate WHERE warehouse_id = :warehouse_id AND angle_id = :angle_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->bindParam(':angle_name', $angle_name);
    $stmt->bindParam(':angle_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();

    add_history('B', '앵글명을 변경', $warehouse_name[0]['warehouse_name']."창고내 앵글을 ".$angle_name_before[0]['angle_name'], $angle_name);            
}
 // 앵글 삭제 
function del_angle(int $warehouse_id, int $angle_id, string $angle_name): void {
    global $conn;
	$stmt = $conn->prepare("UPDATE wms_angle SET delYN = 'Y', angle_use = 'N'  WHERE warehouse_id = :warehouse_id AND angle_id = :angle_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();    
    
    $warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
    
    add_history('A', '앵글을 삭제', $warehouse_name[0]['warehouse_name'], $angle_name);    
}

function warehouse_id_to_warehouse_name(int $warehouse_id): array {
    global $conn;
	$stmt = $conn->prepare("SELECT warehouse_name FROM wms_warehouses WHERE warehouse_id = :warehouse_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);    
}

function angle_id_to_angle_name(int $angle_id): array {
    global $conn;
	$stmt = $conn->prepare("SELECT angle_name FROM wms_angle WHERE angle_id = :angle_id");
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);    
}


// 특정앵글 1가지 조회 
function select_angle_one(int $warehouse_id, int $angle_id): array {
    global $conn;
    $stmt = $conn->prepare("SELECT angle_name FROM wms_angle WHERE delYN = 'N' AND warehouse_id = :warehouse_id AND angle_id = :angle_id LIMIT 1");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);    
}
 // 앵글안에 제품이 있는지 확인, 삭제가능한 앵글인지 조회 0 이면, 앵글삭제가능
function stock_count(int $warehouse_id, int $angle_id): array {
    global $conn;
    $stmt = $conn->prepare("SELECT count(*) as count FROM wms_stock WHERE warehouse_id = :warehouse_id AND angle_id = :angle_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

 // 특정앵글안에 제품들 합산 sum
function stock_sum(int $warehouse_id, int $angle_id): int {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(sum(quantity),0) as sum_quantity FROM wms_stock WHERE warehouse_id = :warehouse_id AND angle_id = :angle_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$result['sum_quantity'];
}

 // 특정앵글안에 제품들 합산 sum
function stock_warehouse_name(int $warehouse_id): string {
    global $conn;
    $stmt = $conn->prepare("SELECT warehouse_name FROM wms_warehouses WHERE warehouse_id = :warehouse_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (string)$result['warehouse_name'];
}
  
 // 특정앵글안에 제품 리스트
function stock_list(int $warehouse_id, int $angle_id): array {
    global $conn;
    $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2
    
    $add_sql = ($result_setting[0]['set_state'] === "N") ? "AND s.quantity > 0" : ""; 
	
    $stmt = $conn->prepare("SELECT i.item_id, i.item_name, (SELECT cate_name FROM wms_cate WHERE i.item_cate = cate_id) as cate_name, (SELECT sum(quantity) as sum_quantity FROM wms_stock WHERE warehouse_id = :warehouse_id AND angle_id = :angle_id AND item_id = i.item_id) as item_cnt FROM wms_stock s LEFT JOIN wms_items i ON s.item_id = i.item_id WHERE s.warehouse_id = :warehouse_id AND s.angle_id = :angle_id $add_sql");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 재고관리 > 재고목록(창고밖) 
function getStock_00(int $warehouse_id, int $angle_id, int $start_record_number, int $itemsPerPage, string $search_add, string $searchType, string $keyword): array {
    global $conn;
    $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2
	  
    if ($result_setting[0]['set_state'] === "N") {
        $add_sql = "AND s.quantity > 0"; 
    } else {
        $add_sql = ""; 
    }	
	   
    if ($searchType === "item_name") {
        $sql = "SELECT i.item_id, i.item_name, (SELECT cate_name FROM wms_cate WHERE i.item_cate = cate_id) as cate_name, (SELECT sum(quantity) as sum_quantity FROM wms_stock WHERE warehouse_id = :warehouse_id AND angle_id = :angle_id AND item_id = i.item_id) as item_cnt, s.rdate FROM wms_stock s LEFT JOIN wms_items i ON s.item_id = i.item_id WHERE s.warehouse_id = :warehouse_id AND s.angle_id = :angle_id $add_sql $search_add ORDER BY s.rdate DESC, s.item_id LIMIT :start_record_number, :itemsPerPage";
    } else {
        $sql = "SELECT i.item_id, i.item_name, (SELECT cate_name FROM wms_cate WHERE i.item_cate = cate_id) as cate_name, (SELECT sum(quantity) as sum_quantity FROM wms_stock WHERE warehouse_id = :warehouse_id AND angle_id = :angle_id AND item_id = i.item_id) as item_cnt, s.rdate FROM wms_stock s LEFT JOIN wms_items i ON s.item_id = i.item_id WHERE (SELECT cate_name FROM wms_cate WHERE i.item_cate = cate_id) LIKE :keyword AND s.warehouse_id = :warehouse_id AND s.angle_id = :angle_id $add_sql $search_add ORDER BY s.rdate DESC, s.item_id LIMIT :start_record_number, :itemsPerPage";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    
    if ($searchType !== "item_name") {
        $keyword = "%$keyword%";
        $stmt->bindParam(':keyword', $keyword);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 제품카테고리 업데이트
function update_angle_order($angle_id, $order) {
    global $conn;
    $stmt = $conn->prepare("UPDATE wms_angle SET angle_order = :order WHERE delYN = 'N' AND angle_id = :angle_id");
    $stmt->bindParam(':order', $order);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 제품관리 * //

// 제품 목록 가져오기
function getwms_items(int $start_record_number, int $itemsPerPage, string $search, string $SearchString): array {
    global $conn;
    // 검색조건
    $condition_sql = "";
    if ($SearchString != "") {
        $condition_sql = " AND i.$search LIKE :search_string";
    }

    $sql = "SELECT c.cate_expose AS item_expose, c.cate_name AS item_cate, i.item_id AS item_id, i.item_code AS item_code, i.item_name AS item_name, LEFT(i.item_rdate, 10) AS item_rdate, i.item_cate AS item_cate_num, IFNULL((SELECT SUM(quantity) AS count FROM wms_stock WHERE delYN = 'N' AND item_id = i.item_id), 0) AS sum_quantity_item FROM wms_items AS i INNER JOIN wms_cate AS c ON i.item_cate = c.cate_id WHERE i.delYN = 'N' $condition_sql ORDER BY item_rdate DESC LIMIT :start_record_number, :itemsPerPage";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

    if ($SearchString != "") {
        $search_string = "%$SearchString%";
        $stmt->bindValue(':search_string', $search_string, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// 제품 목록 가져오기
function getwms_items_list(int $start_record_number, int $itemsPerPage, string $search, string $keyword, string $searchStartDate, string $searchEndDate): array {
     global $conn;
   // 검색조건
 
    $condition_sql = "";
    if ($keyword != "") {
        if ($search == "ALL") {
            $condition_sql =" AND (i.item_name LIKE :keyword OR c.cate_name LIKE :keyword OR i.item_code LIKE :keyword)";
        } else {
			if ($search=="item_cate") {
				$condition_sql = " AND c.cate_name LIKE :keyword";
			}else{
				$condition_sql = " AND i.$search LIKE :keyword";
			}
        }
    }
    
    if ($searchStartDate != "" && $searchEndDate != "") {
        $condition_sql .= " AND item_rdate BETWEEN :start_date AND :end_date";
    }
    
    $sql = "SELECT c.cate_expose AS item_expose, c.cate_name AS item_cate, i.item_id AS item_id, i.item_code AS item_code, i.item_name AS item_name, LEFT(i.item_rdate, 10) AS item_rdate, i.item_cate AS item_cate_num, IFNULL((SELECT SUM(quantity) AS count FROM wms_stock WHERE delYN = 'N' AND item_id = i.item_id), 0) AS sum_quantity_item FROM wms_items AS i INNER JOIN wms_cate AS c ON i.item_cate = c.cate_id WHERE i.delYN = 'N' $condition_sql ORDER BY item_rdate DESC LIMIT :start_record_number, :itemsPerPage";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    
    if ($keyword != "") {
        $keyword = "%$keyword%";
        $stmt->bindValue(':keyword', $keyword, PDO::PARAM_STR);
    }
    
    if ($searchStartDate != "" && $searchEndDate != "") {
        $stmt->bindValue(':start_date', $searchStartDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $searchEndDate, PDO::PARAM_STR);
    }
    //echo $sql;exit();
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 제품관리 > 제품목록 분류변경
function m03_cate_change(string $cate_change, int $item_cate, int $item_id): void {
    global $conn;
    // 이전 카테고리 이름 가져오기
    $cate_name_before = cate_id_to_cate_name_from_item_id($item_id);
    
    // 카테고리 변경
    $stmt = $conn->prepare("UPDATE wms_items SET item_cate = :item_cate WHERE item_id = :item_id");
    $stmt->bindValue(':item_cate', $item_cate, PDO::PARAM_INT);
    $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // 변경된 카테고리 이름 가져오기
    $cate_name_after = cate_id_to_cate_name_from_item_id($item_id);
    
    // 히스토리 추가
    add_history('B', '제품의 분류를 변경', $cate_name_before[0]['cate_name'], $cate_name_after[0]['cate_name']);
}


function cate_id_to_cate_name_from_item_id(int $item_id): array {
    global $conn;
    $stmt = $conn->prepare("SELECT c.cate_name AS cate_name FROM wms_cate c JOIN wms_items i ON c.cate_id = i.item_cate WHERE i.item_id = :item_id");
    $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function duplicate_bar(string $code): string {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_items WHERE item_code = :code");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] != 0) {
        $code++;
        return duplicate_bar($code);
    } else {
        return $code;
    }
}


function duplicate_name(string $name): string {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_items WHERE item_name = :name and delYN='N'");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
	$i=1;
    if ($result['count'] != 0) {
	    echo "<script>alert('이미 [$name] 제품있음. 제품명 변경후 등록바람.');history.go(-1);</script>";
        exit();
        //$name = $name.$i;
        //return duplicate_name($name);
    } else {
        return $name;
    }
}

function duplicate_name2(string $name): string {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_items WHERE item_name = :name and delYN='N'");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] != 0) {
        echo "<script>alert('이미 [$name] 제품있음. 제품명 변경후 등록바람.');</script>";
        return "N";
    } else {
        return "Y";
    }
}


function addItem(string $code, string $name, int $item_cate) {
    global $conn;
    // 바코드 중복 검사
    $code = duplicate_bar($code);  // 중복된 바코드라면, 없는 번호를 생성해준다.
	
    $name = duplicate_name($name);  // 중복된 제품명이라면, 차단하여, 등록을 막는다.
    
    $stmt = $conn->prepare("INSERT INTO wms_items (item_code, item_name, item_rdate, item_cate) VALUES (:code, :name, :rdate, :item_cate)");
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':name', $name);
    $stmt->bindValue(':rdate', date("Y-m-d H:i:s"));
    $stmt->bindParam(':item_cate', $item_cate, PDO::PARAM_INT);
    $stmt->execute();
   
    // 제품 분류명 가져오기
    $item_cate_name = cate_id_to_cate_name($item_cate);
   
    // 히스토리 추가   
    add_history('C', '제품을 등록', $item_cate_name[0]['cate_name']."분류로", $name);           
}

function ck_item_cate_cnt(string $cate_name): array {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(cate_id), '0') AS cate_cnt FROM wms_cate WHERE delYN = 'N' AND cate_name = :cate_name");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->execute(); 
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getwms_item_name(int $item_id): array {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM wms_items WHERE item_id = :item_id");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function select_item_one(int $item_id): array {
    global $conn;
    $stmt = $conn->prepare("SELECT item_name FROM wms_items WHERE delYN = 'N' AND item_id = :item_id LIMIT 0,1");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function stock_item_count(int $item_id): array {
    global $conn;
    $stmt = $conn->prepare("SELECT SUM(quantity) AS count FROM wms_stock WHERE delYN = 'N' AND item_id = :item_id");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function del_item(int $item_id, string $item_name) {
    global $conn;
    $stmt = $conn->prepare("UPDATE wms_items SET delYN = 'Y' WHERE item_id = :item_id AND delYN = 'N'");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->execute();
    add_history('A', '제품을 삭제', $item_name, '');
}


function check_duplicate($item_name) {
    global $conn;	
    $sql = "SELECT COUNT(*) FROM wms_items WHERE item_name = :item_name AND delYN = 'N'";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

function update_item_cnt(int $item_id, string $item_name): array {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(item_id), '0') AS item_cnt FROM wms_items WHERE delYN = 'N' AND item_id <> :item_id AND item_name = :item_name");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function update_item(int $item_id, string $item_name) {
    global $conn;
    $item_name_before = item_id_to_item_name($item_id);

    $stmt = $conn->prepare("UPDATE wms_items SET item_name = :item_name, item_rdate = :item_rdate WHERE delYN = 'N' AND item_id = :item_id");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':item_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();

    add_history('B', '제품명을 변경', $item_name_before[0]['item_name'], $item_name);
}

function update_item2(int $item_id, string $item_name, string $item_code) {
     global $conn;
   $item_name_before = item_id_to_item_name($item_id);
    $item_code_before = item_id_to_item_code($item_id);

    $stmt = $conn->prepare("UPDATE wms_items SET item_name = :item_name, item_code = :item_code, item_rdate = :item_rdate WHERE delYN = 'N' AND item_id = :item_id");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':item_code', $item_code);
    $stmt->bindParam(':item_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();

    add_history('B', '제품정보를 변경', $item_name_before[0]['item_name'] . "(" . $item_code_before[0]['item_code'] . ")", $item_name . "(" . $item_code . ")");
}

function del_cate(int $cate_id, string $cate_name) {
     global $conn;
   $stmt0 = $conn->prepare("UPDATE wms_cate SET delYN = 'Y', cate_use = 'N' WHERE cate_id = :cate_id AND delYN = 'N'");
    $stmt0->bindParam(':cate_id', $cate_id);
    $stmt0->execute();

    add_history('A', '제품분류명을 삭제', $cate_name, '');
}

// 특정제품 1가지 조회
function select_cate_one(int $cate_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT cate_name FROM wms_cate WHERE delYN = 'N' AND cate_id = :cate_id LIMIT 0, 1");
    $stmt->bindParam(':cate_id', $cate_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function stock_cate_count(int $cate_id) {
     global $conn;
   $stmt = $conn->prepare("SELECT (SELECT COUNT(item_id) FROM wms_items WHERE item_cate = wms_cate.cate_id) AS count FROM wms_cate WHERE cate_id = :cate_id AND cate_id > 0");
    $stmt->bindParam(':cate_id', $cate_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getwms_cate(int $start_record_number, int $itemsPerPage) {
     global $conn;
   $stmt = $conn->prepare("SELECT cate_id, cate_name, cate_use, cate_expose, cate_rdate, delYN, (SELECT COUNT(item_id) FROM wms_items WHERE item_cate = wms_cate.cate_id and delYN='N') AS cnt_item FROM wms_cate WHERE delYN='N' AND cate_id > 0 LIMIT :start_record_number, :itemsPerPage");
    $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getwms_cate_search1(int $cate_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT cate_name FROM wms_cate WHERE cate_id = :cate_id LIMIT 0, 1");
    $stmt->bindParam(':cate_id', $cate_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 제품카테고리 추가
function addCate(string $cate_name) {
     global $conn;
   $stmt = $conn->prepare("INSERT INTO wms_cate (cate_name, cate_rdate) VALUES (:cate_name, :cate_rdate)");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();

    add_history('A', '제품분류를 등록', $cate_name, '');
}

function updateCate(string $cate_name, int $cate_id) {
     global $conn;
   $cate_name_before = cate_id_to_cate_name($cate_id);

    $stmt = $conn->prepare("UPDATE wms_cate SET cate_name = :cate_name WHERE cate_id = :cate_id");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_id', $cate_id);
    $stmt->execute();

    add_history('B', '제품분류명을 변경', $cate_name_before[0]['cate_name'], $cate_name);
} 

function is_first_ck(int $item_id, int $warehouse_id, int $angle_id, int $quantity) {		
     global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_stock WHERE item_id = :item_id AND warehouse_id = :warehouse_id AND angle_id = :angle_id");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}


// 제품목록 > 입고등록 : 앵글로 제품 넣는 과정 (앵글로 수정)
function addStock(int $item_id, int $warehouse_id, int $angle_id, int $quantity, bool $step_1) {
      global $conn;
   $rst = is_first_ck($item_id, $warehouse_id, $angle_id, $quantity);  

    if ($rst == 0) {
        $stmt = $conn->prepare("INSERT INTO wms_stock (item_id, warehouse_id, angle_id, quantity, rdate) VALUES (:item_id, :warehouse_id, :angle_id, :quantity, :rdate)");	
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':warehouse_id', $warehouse_id);
        $stmt->bindParam(':angle_id', $angle_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':rdate', date("Y-m-d H:i:s"));	
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("UPDATE wms_stock SET quantity = quantity + :quantity WHERE item_id = :item_id AND warehouse_id = :warehouse_id AND angle_id = :angle_id");
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':warehouse_id', $warehouse_id);
        $stmt->bindParam(':angle_id', $angle_id);
        $stmt->execute();
    }
    
    $item_name = item_id_to_item_name($item_id);
    $warehouse_name = ($warehouse_id == 0) ? '미지정' : warehouse_id_to_warehouse_name($warehouse_id);
    $angle_name = ($angle_id == 0) ? '' : angle_id_to_angle_name($angle_id);
    
    add_history('C', '창고 입고등록', $item_name[0]['item_name'] . ' 제품 ' . $quantity . '개를', $warehouse_name[0]['warehouse_name'] . '창고의 ' . $angle_name[0]['angle_name'] . '앵글로 ');

    // 이력 추가
    $stmt_history1 = $conn->prepare("INSERT INTO wms_in_stock_history (item_id, to_warehouse_id, angle_id, quantity, rdate, in_stock_who, in_stock_ip) VALUES (:item_id, :to_warehouse_id, :angle_id, :quantity, :rdate, :in_stock_who, :in_stock_ip)");
    $stmt_history1->bindParam(':item_id', $item_id);
    $stmt_history1->bindParam(':to_warehouse_id', $warehouse_id);
    $stmt_history1->bindParam(':angle_id', $angle_id);
    $stmt_history1->bindParam(':quantity', $quantity);
    $stmt_history1->bindParam(':rdate', date("Y-m-d H:i:s"));
    $stmt_history1->bindParam(':in_stock_who', $_SESSION['admin_name']);
    $stmt_history1->bindParam(':in_stock_ip', $_SERVER['REMOTE_ADDR']);
    $stmt_history1->execute();	
}




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 재고관리 * //
 
// 창고밖보관 제품 (앵글로 이동), 거래처 추가버전
function movetoangle_inc_company_Stock(int $item_id, int $warehouse_id, int $angle_id, int $quantity, bool $step_1) {
      global $conn;
   $conn->beginTransaction();

    try {		
        // 재고 감소
        $stmt_decrease = $conn->prepare("UPDATE wms_stock SET quantity = quantity - :quantity, rdate = :rdate WHERE item_id = :item_id AND warehouse_id = 0");
        $stmt_decrease->bindParam(':quantity', $quantity);
        $stmt_decrease->bindParam(':item_id', $item_id);
        $stmt_decrease->bindParam(':rdate', date("Y-m-d H:i:s"));
        $stmt_decrease->execute();

        // 대상 창고에 재고를 증가 또는 업데이트
        $stmt_increase = $conn->prepare("INSERT INTO wms_stock (item_id,  warehouse_id, angle_id, quantity , rdate) VALUES (:item_id, :to_warehouse_id, :to_angle_id, :quantity, :rdate) ON DUPLICATE KEY UPDATE quantity = quantity + :quantity "); 
        $stmt_increase->bindParam(':item_id', $item_id);
        $stmt_increase->bindParam(':to_warehouse_id', $warehouse_id);
        $stmt_increase->bindParam(':to_angle_id', $angle_id);
        $stmt_increase->bindParam(':quantity', $quantity);
        $stmt_increase->bindParam(':rdate', date("Y-m-d H:i:s"));
        $stmt_increase->execute();

        $item_name = item_id_to_item_name($item_id);	    
        $warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
        $angle_name = angle_id_to_angle_name($angle_id);   
 		
        add_history('C', '앵글로 이동', $item_name[0]['item_name'] . ' 제품 ' . $quantity . '개를 ', '미지정 창고에서 ' . $warehouse_name[0]['warehouse_name'] . '창고 ' . $angle_name[0]['angle_name']);

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollBack();
        return false;
    } finally {
        // 데이터베이스 연결 닫기
        $conn = null;
    }
}

// 창고안서 창고로 재고이동 (앵글로 이동)
function from_angle_moveto_angle_Stock(int $stock_id, int $to_ware, int $to_angle, int $to_cnt): bool {
      global $conn;
    $from_item_id = 0;
    $from_warehouse_id = 0;
    $from_angle_id = 0;
    $from_cnt = 0;

    $conn->beginTransaction();
    try {    
        // 현재 재고 확인
        $stmt_check = $conn->prepare("SELECT stock_id, item_id, warehouse_id, angle_id, quantity FROM wms_stock WHERE stock_id = :stock_id");
        $stmt_check->bindParam(':stock_id', $stock_id);
        $stmt_check->execute();
        $current_stock = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($current_stock === false) {
            error107($stock_id);  
            exit();
        }       
        
        if (($current_stock['quantity'] - $to_cnt) < 0) {
            error108($stock_id); 
            exit();
        }

        if ($to_cnt < 0) {
            error110($stock_id); 
            exit();
        }

        // 변수 대입, 업데이트 시킬 카운트 계산
        $from_warehouse_id = $current_stock['warehouse_id'];
        $from_angle_id     = $current_stock['angle_id'];
        $from_cnt          = $current_stock['quantity'] - $to_cnt;
        $item_id           = $current_stock['item_id'];

        if (($to_ware == $from_warehouse_id) && ($to_angle == $from_angle_id)) {  
            error109($stock_id);  
            exit();
        }

        // 재고 감소
        $stmt_decrease = $conn->prepare("UPDATE wms_stock SET quantity = :quantity  WHERE stock_id = :stock_id");
        $stmt_decrease->bindParam(':quantity', $from_cnt);
        $stmt_decrease->bindParam(':stock_id', $stock_id);
        $stmt_decrease->execute();

        // 대상 창고에 재고를 증가 또는 업데이트
        $stmt_increase = $conn->prepare("INSERT INTO wms_stock (item_id, warehouse_id, angle_id, quantity , rdate) VALUES (:item_id, :to_warehouse_id, :to_angle_id, :quantity, :rdate) ON DUPLICATE KEY UPDATE quantity = quantity + :quantity "); 
        $stmt_increase->bindParam(':item_id', $item_id);
        $stmt_increase->bindParam(':to_warehouse_id', $to_ware);
        $stmt_increase->bindParam(':to_angle_id', $to_angle);
        $stmt_increase->bindParam(':quantity', $to_cnt);
        $stmt_increase->bindParam(':rdate', date("Y-m-d H:i:s"));
        $stmt_increase->execute();

        // 이력추가 
        $item_name          = item_id_to_item_name($item_id);      
        $from_warehouse_name = warehouse_id_to_warehouse_name($from_warehouse_id);
        $from_angle_name     = angle_id_to_angle_name($from_angle_id);   
        $to_ware_name        = warehouse_id_to_warehouse_name($to_ware);
        $to_angle_name       = angle_id_to_angle_name($to_angle);  
        
        add_history('C','재고이동',$item_name[0]['item_name']." 제품 ".$to_cnt."개를 ".$from_warehouse_name[0]['warehouse_name']." 창고 ".$from_angle_name[0]['angle_name']." 앵글에서 ",$to_ware_name[0]['warehouse_name']." 창고 ".$to_angle_name[0]['angle_name']." 앵글로 "); 
    
        $conn->commit();
        
        return true;
    } catch (Exception $e) {
        $conn->rollBack();
        return false;
    } finally {
        // 데이터베이스 연결 닫기
        $conn = null;
    }
}

 function error107(int $stock_id): void {
    echo "<script>alert('현재 재고수량 오류');location.href='/gn/m04/ctrl_stock_move_input.php?stock_id={$stock_id}';</script>";
    exit();
}

function error108(int $stock_id): void {
    echo "<script>alert('재고이동 수량초과');location.href='/gn/m04/ctrl_stock_move_input.php?stock_id={$stock_id}';</script>";
    exit();
}

function error109(int $stock_id): void {
    echo "<script>alert('창고 및 앵글 동일.다시 바람.');location.href='/gn/m04/ctrl_stock_move_input.php?stock_id={$stock_id}';</script>";
    exit();
}

function error110(int $stock_id): void {
    echo "<script>alert('양수로 입력바랍니다.');location.href='/gn/m04/ctrl_stock_move_input.php?stock_id={$stock_id}';</script>";
    exit();
}


// 창고안 현재 재고 상태 가져오기 
function getStock(int $start_record_number, int $itemsPerPage, string $search_add): array {
       global $conn;
   $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2

    if ($result_setting[0]['set_state'] == "N") {
        $add_sql = " AND quantity > 0"; 
    } else {
        $add_sql = " "; 
    }

    $sql = "SELECT ";
    $sql .= "s.*,  item_name, "; 
    $sql .= "IFNULL(w.warehouse_id, '/') AS warehouse_id_null,  ";
    $sql .= "IFNULL(w.warehouse_id, '0') AS warehouse_id,  ";
    $sql .= "IFNULL(w.warehouse_name, '배정안됨') AS warehouse_name,  ";
    $sql .= "a.angle_name AS angle_name   ";
    $sql .= "FROM  ";
    $sql .= "`wms_stock` AS s  ";
    $sql .= "LEFT JOIN  ";
    $sql .= "`wms_items` AS i  ";
    $sql .= "ON s.item_id = i.item_id  ";
    $sql .= "LEFT JOIN  ";
    $sql .= "`wms_warehouses` AS w  ";
    $sql .= "ON s.warehouse_id = w.warehouse_id   ";
    $sql .= "JOIN  ";
    $sql .= "`wms_angle` AS a  ";
    $sql .= "ON s.angle_id = a.angle_id  ";
    $sql .= "WHERE  ";
    $sql .= "w.delYN = 'N'  ";
    $sql .= "AND a.delYN = 'N'  ";
    $sql .= "AND w.warehouse_id <> 0     "; 
    $sql .= $add_sql;
    $sql .= $search_add;
    $sql .= "ORDER BY s.rdate DESC, s.item_id LIMIT $start_record_number, $itemsPerPage";

    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 전체 재고 현재 재고 상태 가져오기 
function getStock_all(int $start_record_number, int $itemsPerPage, string $search_add): array {
        global $conn;
   $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2

    if ($result_setting[0]['set_state'] == "N") {
        $add_sql = " AND quantity > 0"; 
    } else {
        $add_sql = " "; 
    }

    $sql = "SELECT ";
    $sql .= "s.*,  item_name, "; 
    $sql .= "IFNULL(w.warehouse_id, '/') AS warehouse_id_null,  ";
    $sql .= "IFNULL(w.warehouse_id, '0') AS warehouse_id,  ";
    $sql .= "IFNULL(w.warehouse_name, '배정안됨') AS warehouse_name,  ";
    $sql .= "a.angle_name AS angle_name   ";
    $sql .= "FROM  ";
    $sql .= "`wms_stock` AS s  ";
    $sql .= "LEFT JOIN  ";
    $sql .= "`wms_items` AS i  ";
    $sql .= "ON s.item_id = i.item_id  ";
    $sql .= "LEFT JOIN  ";
    $sql .= "`wms_warehouses` AS w  ";
    $sql .= "ON s.warehouse_id = w.warehouse_id   ";
    $sql .= "JOIN  ";
    $sql .= "`wms_angle` AS a  ";
    $sql .= "ON s.angle_id = a.angle_id  ";
    $sql .= "WHERE  ";
    $sql .= "w.delYN = 'N'  ";
    $sql .= "AND a.delYN = 'N'  ";
    // $sql .= "AND w.warehouse_id <> 0     "; 
    $sql .= $add_sql;
    $sql .= $search_add;
    $sql .= "ORDER BY s.rdate DESC, s.item_id LIMIT $start_record_number, $itemsPerPage ";

    //echo $sql;exit();
  
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// * 입고지시관리 * //
 // 페이징처리
function paginate_addpara($totalItems, $itemsPerPage, $currentPage, $url, $addpara) {
    // 전체 페이지 수 계산
    $totalPages = ceil($totalItems / $itemsPerPage); 

    // 현재 페이지를 범위 내에 유지
    $currentPage = max(1, min($totalPages, $currentPage));

    // 이전 페이지와 다음 페이지 계산
    $prevPage = ($currentPage > 1) ? $currentPage - 1 : null;
    $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

    // 페이지 링크 생성
    $pagination = '<div class="paging" style="height:50px;padding-top:20px">';

    // 첫 페이지로 이동 링크
    $pagination .= '<a href="' . $url . '?page=1&itemsPerPage='.$itemsPerPage.$addpara.'" ><span class="glyphicon glyphicon glyphicon-backward" aria-hidden="true"></span></a> ';

    // 이전 10페이지 링크
    if ($prevPage !== null) {
        $pagination .= '<a href="' . $url . '?page=' . max(1, $currentPage - 10) . '&itemsPerPage='.$itemsPerPage.$addpara.'" ><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>';
    } else {
        $pagination .= '<a href="' . $url . '?page=1&itemsPerPage='.$itemsPerPage.$addpara.'" ><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>';
    }

    // 페이지 번호 링크 (항상 10개의 페이지만 표시)
    $startPage = max(1, $currentPage - 5); // 현재 페이지를 중심으로 앞에 5개 페이지
    $endPage = min($totalPages, $currentPage + 5); // 현재 페이지를 중심으로 뒤에 5개 페이지
    for ($i = $startPage; $i <= $endPage; $i++) {
        $activeClass = ($i == $currentPage) ? ' current' : '';
        $pagination .= ' <a href="' . $url . '?page=' . $i . '&itemsPerPage='.$itemsPerPage.$addpara.'" class="num' . $activeClass . '" style="font-size:18px;color:#666;text-decoration: none">' . ($i == $currentPage ? '<span style="font-size:20px;color:#000"> <strong>' . $i . '</strong></span>' : $i) . ' </a>&nbsp;&nbsp;';
    }

    // 다음 10페이지 링크
    if ($nextPage !== null) {
        $pagination .= '<a href="' . $url . '?page=' . min($totalPages, $currentPage + 10) . '&itemsPerPage='.$itemsPerPage.$addpara.'" ><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a> ';
    } else {
        $pagination .= '<a href="' . $url . '?page=' . $totalPages . '&itemsPerPage='.$itemsPerPage.$addpara.'" ><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a> ';
    }

    // 마지막 페이지로 이동 링크
    $pagination .= '<a href="' . $url . '?page='.$totalPages.'&itemsPerPage='.$itemsPerPage.$addpara.'"  ><span class="glyphicon glyphicon glyphicon-forward" aria-hidden="true"></span></a> ';

    $pagination .= '</div>';

    return $pagination;
}


if (!isset($_POST['page_name'])) { $_POST['page_name'] = ""; }

// 입고지시관리 신규등록 start //	
 if (($_POST['page_name']!="")&&($_POST['page_name']=="inbound_write")) {  // inbound_write (입고지시 등록으로부터 받은 값)
	 
	// POST로 전달된 데이터 수신
	$product_ids = $_POST['product_id'];
	$company_ids = $_POST['company_id'];
	$warehouse_id = $_POST['warehouse_id'];
	$angle_id = $_POST['angle_id'];
	$planned_quantities = $_POST['planned_quantity'];
	
    // inbound_quantity 값이 빈 문자열이나 null인 경우 0으로 처리
    $inbound_quantities = array();

        foreach ($_POST['inbound_quantity'] as $quantity) {
            if (!empty($quantity)) {
                $inbound_quantities[] = $quantity;
            } else {
                $inbound_quantities[] = 0;
            }
        }
	
	$plan_date = $_POST['plan_date'];
	$page_name = $_POST['page_name'];
 

	try {
		// MySQL 연결
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		// 오류 출력을 위한 예외 처리
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// 입력된 데이터를 wms_inbound 테이블에 저장 (입고일은 여기선 저장하지 않음)
		$stmt = $conn->prepare("INSERT INTO wms_inbound (product_id, company_id, warehouse_id, angle_id, planned_quantity, inbound_quantity,plan_date) VALUES (:product_id, :company_id, :warehouse_id,:angle_id, :planned_quantity, :inbound_quantity, :plan_date) ON DUPLICATE KEY UPDATE planned_quantity = planned_quantity + :planned_quantity");

		// 각 필드에 대해 반복하여 값을 바인딩하고 쿼리를 실행
		for ($i = 0; $i < count($product_ids); $i++) {
			$stmt->bindParam(':product_id', $product_ids[$i]);
			$stmt->bindParam(':company_id', $company_ids[$i]);
			$stmt->bindParam(':warehouse_id', $warehouse_id[$i]);
			$stmt->bindParam(':angle_id', $angle_id[$i]);
			$stmt->bindParam(':planned_quantity', $planned_quantities[$i]);
			$stmt->bindParam(':inbound_quantity', $inbound_quantities[$i]);
			$stmt->bindParam(':plan_date', $plan_date);
			//$stmt->bindParam(':rdate',null);
			$stmt->execute();
			
		}


		// 각 필드에 대해 반복하여 값을 바인딩하고 	//stock 실제 입고처리 진행	// 3개면, count 1,2,3
	
		for ($i = 0; $i < count($product_ids); $i++) {
		
			if ($planned_quantities[$i] == $inbound_quantities[$i] ) {	
				addStock($product_ids[$i],$warehouse_id[$i],$angle_id[$i],$inbound_quantities[$i],'N'); // step = Y 면 앵글에 보관 / 아니면 N	
				
				//addStock(7,3,25,7,'N'); // step = Y 면 앵글에 보관 / 아니면 N	
				//echo $i."<BR>";
				//echo "product_ids[$i]:".$product_ids[$i]."<BR>";
				//echo "warehouse_id[$i]:".$warehouse_id[$i]."<BR>";
				//echo "angle_id[$i]:".$angle_id[$i]."<BR>";
				//echo "inbound_quantities[$i]:".$inbound_quantities[$i]."<BR>";
				//echo "company_ids[$i]:".$company_ids[$i]."<BR>";
				//echo "<HR>";	
				update_wms_inbound_same_quantity_state_1($product_ids[$i],$warehouse_id[$i],$angle_id[$i],$company_ids[$i],$plan_date);
			}
		}

		echo "입고지시가 성공적으로 저장되었습니다.";		
	} catch(PDOException $e) {
		// 오류 발생 시 에러 메시지 출력
		echo "오류: " . $e->getMessage();
	}
  // MySQL 연결 종료
  $conn = null;
 }
// 입고지시관리 신규등록 end //
 


// 인바운드 입고예정수량 및 입고된 수량 가져오기
function getwms_wms_inbound_cnt($inbound_id) {
    global $conn;
 
    $stmt = $conn->query("SELECT planned_quantity, inbound_quantity, (planned_quantity - inbound_quantity) as able_inbound_quantity, plan_date FROM wms_inbound where  inbound_id = ".$inbound_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 인바운드 입고삭제용 정보 가져오기
function getwms_wms_inbound_info($inbound_id) {
       global $conn;
    $sql = "SELECT ";
    $sql .= "i.inbound_id as inbound_id, p.item_name AS item_name, ";
    $sql .= "w.warehouse_name AS warehouse_name, ";
    $sql .= "( ";
    $sql .= "SELECT angle_name ";
    $sql .= "FROM wms_angle ";
    $sql .= "WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
    $sql .= ") AS angle_name, ";
    $sql .= "( ";
    $sql .= "SELECT cate_name ";
    $sql .= "FROM wms_company ";
    $sql .= "WHERE cate_id = i.company_id ";
    $sql .= ") as company_name, ";
    $sql .= "i.planned_quantity, ";
    $sql .= "i.inbound_quantity, ";
    $sql .= "i.plan_date, ";
    $sql .= "i.rdate, ";
    $sql .= "i.state ";
    $sql .= "FROM ";
    $sql .= "wms_inbound i ";
    $sql .= "JOIN ";
    $sql .= "wms_items p ON p.item_id = i.product_id ";
    $sql .= "JOIN ";
    $sql .= "wms_warehouses w ON w.warehouse_id = i.warehouse_id and  i.inbound_id = :inbound_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':inbound_id', $inbound_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 예정수량과, 입고 수량이 같은지 비교, 같으면 입고처리 진행을 위한 조회.
function ck_state($inbound_id){
       global $conn;
    $sql = "SELECT count(state) as cnt FROM wms_inbound WHERE planned_quantity = inbound_quantity AND inbound_id = :inbound_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':inbound_id', $inbound_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

function update_inbound($inbound_id, $inbound_cnt, $plan_date) {	
       global $conn;
	$stmt = $conn->prepare("UPDATE wms_inbound SET inbound_quantity = inbound_quantity + :inbound_quantity WHERE inbound_id = :inbound_id");
	$stmt->bindParam(':inbound_id', $inbound_id);
	$stmt->bindParam(':inbound_quantity', $inbound_cnt);
	$stmt->execute();	
	
	$result = ck_state($inbound_id); // 예정수량과, 입고 수량이 같은지 비교하여 같으면 입고처리 진행
	if ($result[0]['cnt'] == 1) { 
		$stmt = $conn->prepare("UPDATE wms_inbound SET state = 1, rdate = CURDATE() WHERE inbound_id = :inbound_id");
		$stmt->bindParam(':inbound_id', $inbound_id);
		$stmt->execute();	
		
		$result2 = get_wms_inbound_item($inbound_id);	
		
		$item_id = $result2[0]['product_id'];
		$to_ware = $result2[0]['warehouse_id'];
		$to_angle = $result2[0]['angle_id'];
		$qua = $result2[0]['inbound_quantity'];
		$step = 'N';
		
		// 실제 입고처리
		addStock($item_id, $to_ware, $to_angle, $qua, $step); // step = Y 면 앵글에 보관, 아니면 N
	}
}

// 인바운드 삭제 
function del_inbound($inbound_id) {	
       global $conn;
	$stmt = $conn->prepare("DELETE FROM wms_inbound WHERE inbound_id = :inbound_id");
	$stmt->bindParam(':inbound_id', $inbound_id);
	$stmt->execute();	
}

// 입고지시 목록 가져오기
function getwms_inbounds($start_record_number, $itemsPerPage, $search, $searchString) {
    global $conn;

    $sql = "SELECT ";
    $sql .= "    i.inbound_id as inbound_id, p.item_name AS item_name,  ";
    $sql .= "    w.warehouse_name AS warehouse_name, ";
    $sql .= "    ( ";
    $sql .= "        SELECT angle_name  ";
    $sql .= "        FROM wms_angle  ";
    $sql .= "        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
    $sql .= "    ) AS angle_name, ";
	    
    $sql .= "    (  ";
    $sql .= "        SELECT cate_name  ";
    $sql .= "        FROM wms_company  ";
    $sql .= "        WHERE cate_id = i.company_id  ";
    $sql .= "    ) as company_name,  ";
    $sql .= "    i.planned_quantity,  ";
    $sql .= "    i.inbound_quantity,  ";
    $sql .= "    i.plan_date,  ";
    $sql .= "    i.rdate,  ";
    $sql .= "    i.state ";
    $sql .= "FROM  ";
    $sql .= "    wms_inbound i   ";
    $sql .= "JOIN  ";
    $sql .= "    wms_items p ON p.item_id = i.product_id ";
    $sql .= "JOIN  ";
    $sql .= "    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
    $sql .= $searchString;	
    $sql .= " AND i.delYN = 'N' ORDER BY i.plan_date DESC, i.inbound_id DESC LIMIT $start_record_number,$itemsPerPage ";
	//echo $sql;exit();
    $stmt = $conn->query($sql);	
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result; 
}
 
  
// 입고지시서 팝업 (입고등록) 처리를 위한, item 가져오기
function get_wms_inbound_item($inbound_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT product_id, warehouse_id, angle_id, inbound_quantity FROM wms_inbound WHERE inbound_id = :inbound_id");
    $stmt->bindParam(':inbound_id', $inbound_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 출고지시관리 * //
if (!isset($_POST['page_name'])) { $_POST['page_name'] = ""; }
 
// 출고지시관리 신규등록 start //	
 if (($_POST['page_name']!="")&&($_POST['page_name']=="outbound_write")) {  // outbound_write (출고지시 등록으로부터 받은 값)
	 
	// POST로 전달된 데이터 수신
	$product_ids = $_POST['product_id'];
	$company_ids = $_POST['company_id'];
	$warehouse_id = $_POST['warehouse_id'];
	$angle_id = $_POST['angle_id'];
	$planned_quantities = $_POST['planned_quantity'];
	//$outbound_quantities = $_POST['outbound_quantity'];
	
    // outbound_quantities 값이 빈 문자열이나 null인 경우 0으로 처리
    $outbound_quantities = array();

        foreach ($_POST['outbound_quantity'] as $quantity) {
            if (!empty($quantity)) {
                $outbound_quantities[] = $quantity;
            } else {
                $outbound_quantities[] = 0;
            }
        }	
	
	$plan_date = $_POST['plan_date'];
	$page_name = $_POST['page_name'];

	try {
		// MySQL 연결
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		// 오류 출력을 위한 예외 처리
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// 입력된 데이터를 wms_outbound 테이블에 저장 (출고일은 여기선 저장하지 않음)
		$stmt = $conn->prepare("INSERT INTO wms_outbound (product_id, company_id, warehouse_id, angle_id, planned_quantity, outbound_quantity,plan_date) VALUES (:product_id, :company_id, :warehouse_id,:angle_id, :planned_quantity, :outbound_quantity, :plan_date) ON DUPLICATE KEY UPDATE planned_quantity = planned_quantity + :planned_quantity");
 

		// 각 필드에 대해 반복하여 값을 바인딩하고 쿼리를 실행
		for ($i = 1; $i < count($product_ids); $i++) {
			$stmt->bindParam(':product_id', $product_ids[$i]);
			$stmt->bindParam(':company_id', $company_ids[$i]);
			$stmt->bindParam(':warehouse_id', $warehouse_id[$i]);
			$stmt->bindParam(':angle_id', $angle_id[$i]);
			$stmt->bindParam(':planned_quantity', $planned_quantities[$i]);
			$stmt->bindParam(':outbound_quantity', $outbound_quantities[$i]);
			$stmt->bindParam(':plan_date', $plan_date);
			//$stmt->bindParam(':rdate',null);
			
			$stmt->execute();
			
		}
		

		// 각 필드에 대해 반복하여 값을 바인딩하고 	//stock 실제 출고처리 진행	// 3개면, count 1,2,3
		for ($i = 1; $i < count($product_ids); $i++) {
		
			if (($planned_quantities[$i] == $outbound_quantities[$i] )&&($i>0)) {	
				add_outStock($product_ids[$i],$warehouse_id[$i],$angle_id[$i],$outbound_quantities[$i],'N',$company_ids[$i]); // step = Y 면 앵글에 보관 / 아니면 N	
				
				//add_outStock(7,3,25,7,'N',2); // step = Y 면 앵글에 보관 / 아니면 N	
				//echo $i."<BR>";
				//echo "product_ids[$i]:".$product_ids[$i]."<BR>";
				//echo "warehouse_id[$i]:".$warehouse_id[$i]."<BR>";
				//echo "angle_id[$i]:".$angle_id[$i]."<BR>";
				//echo "outbound_quantities[$i]:".$outbound_quantities[$i]."<BR>";
				//echo "company_ids[$i]:".$company_ids[$i]."<BR>";
				//echo "<HR>";	
				update_wms_outbound_same_quantity_state_1($product_ids[$i],$warehouse_id[$i],$angle_id[$i],$company_ids[$i],$plan_date);
			}
		}

         // 폼 데이터 출력
		//echo "<pre>";
		//print_r($_POST);
		//echo "</pre>";

		echo "출고지시가 성공적으로 저장되었습니다.!";
	} catch(PDOException $e) {
		// 오류 발생 시 에러 메시지 출력
		echo "오류: " . $e->getMessage();
	}
  // MySQL 연결 종료
  $conn = null;
 }
// 출고지시관리 신규등록 end //

// 출고 카운트 검사과정 (item_id, warehouse_id, angle_id, company_name 조건 검사하여, 존재하는 수량보다 더 많이 빼는지, 적당한지 확인)
function is_able_ck($item_id, $warehouse_id, $angle_id, $quantity, $company_id) {
    global $conn;
    $sql = "SELECT COUNT(stock_id) AS count2 FROM wms_stock WHERE item_id = :item_id AND warehouse_id = :warehouse_id AND angle_id = :angle_id AND delYN = 'N' AND quantity >= :quantity";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

  
  
//  입고지시목록 완료처리 : wms_inbound 테이블 state = 1 처리,  입고지시 등록시, 예상수량 및 입고수량을 동일 등록시, 완료처리하기
function update_wms_inbound_same_quantity_state_1($item_id, $warehouse_id, $angle_id, $company_id, $plan_date) {
    global $conn;

    $stmt = $conn->prepare("UPDATE wms_inbound SET state = 1, rdate = CURDATE() WHERE product_id = :item_id AND warehouse_id = :warehouse_id AND angle_id = :angle_id AND plan_date = :plan_date AND delYN = 'N'");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->bindParam(':plan_date', $plan_date);
    $stmt->execute();
}

  
//  출고지시목록 완료처리 : wms_outbound 테이블 state = 1 처리,  출고지시 등록시, 예상수량 및 출고수량을 동일 등록시, 완료처리하기
function update_wms_outbound_same_quantity_state_1($item_id, $warehouse_id, $angle_id, $company_id, $plan_date) {
    global $conn;

    $stmt = $conn->prepare("UPDATE wms_outbound SET state = 1, rdate = CURDATE() WHERE product_id = :item_id AND warehouse_id = :warehouse_id AND angle_id = :angle_id AND company_id = :company_id AND plan_date = :plan_date AND delYN = 'N'");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':plan_date', $plan_date);
    $stmt->execute();
}

  
//  출고등록 : 앵글의 제품수 차감 (출고)
function add_outStock($item_id, $warehouse_id, $angle_id, $quantity, $step, $company_id) {
    global $conn;

    $company_name = company_id_to_company_name($company_id);
    $item_name = item_id_to_item_name($item_id);
    $warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
    $angle_name = angle_id_to_angle_name($angle_id);

    $stmt = $conn->prepare("UPDATE wms_stock SET quantity = quantity - :quantity WHERE item_id = :item_id AND warehouse_id = :warehouse_id AND angle_id = :angle_id AND delYN = 'N'");
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();

    add_history('C', '출고완료', $item_name[0]['item_name'] . "제품 " . $quantity . "개를 " . $company_name[0]['cate_name'] . "업체", $warehouse_name[0]['warehouse_name'] . "창고의 " . $angle_name[0]['angle_name'] . " 앵글로부터");

    // 이력 추가
    $stmt_history1 = $conn->prepare("INSERT INTO wms_out_stock_history (item_id, to_warehouse_id, angle_id, quantity, rdate, out_stock_who, out_stock_ip) VALUES (:item_id, :to_warehouse_id, :angle_id, :quantity, :rdate, :out_stock_who, :out_stock_ip)");
    $stmt_history1->bindParam(':item_id', $item_id);
    $stmt_history1->bindParam(':to_warehouse_id', $warehouse_id);
    $stmt_history1->bindParam(':angle_id', $angle_id);
    $stmt_history1->bindParam(':quantity', $quantity);
    $stmt_history1->bindParam(':rdate', date("Y-m-d H:i:s"));
    $stmt_history1->bindParam(':out_stock_who', $_SESSION['admin_name']);
    $stmt_history1->bindParam(':out_stock_ip', $_SERVER['REMOTE_ADDR']);

    $stmt_history1->execute();
}


// 예정수량과 , 출고 수량이 같은지 비교, 같으면 출고처리 진행을 위한 카운트조회
function out_ck_state($outbound_id){
    global $conn;
    $sql = "SELECT COUNT(state) AS cnt,    product_id,      warehouse_id,                    angle_id,                    outbound_quantity, 
                   CASE WHEN warehouse_id = 0 THEN 'N' ELSE 'Y' END AS step,                    (SELECT cate_name FROM wms_company WHERE cate_id = wms_outbound.company_id) AS company_name,                    wms_outbound.company_id AS company_id             FROM wms_outbound             WHERE planned_quantity = outbound_quantity AND outbound_id = :outbound_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':outbound_id', $outbound_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}


// 아웃바운드 출고예정수량 및 출고된 수량 가져오기
function getwms_wms_outbound_cnt($outbound_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT planned_quantity,                                     outbound_quantity,                                     (planned_quantity - outbound_quantity) as able_outbound_quantity,                                     plan_date                              FROM wms_outbound                             WHERE outbound_id = :outbound_id");
    $stmt->bindParam(':outbound_id', $outbound_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

 // 아웃바운드 출고수량 업데이트  
function update_outbound($outbound_id, $outbound_cnt, $plan_date) {
    global $conn;
	
	// 1. wms_outbound에 출고수량 업데이트
	$stmt = $conn->prepare("UPDATE wms_outbound SET outbound_quantity = outbound_quantity + :outbound_quantity WHERE outbound_id = :outbound_id");
	$stmt->bindParam(':outbound_id', $outbound_id);
	$stmt->bindParam(':outbound_quantity', $outbound_cnt);
	$stmt->execute();	
		
    // 2. 예정수량과 출고수량 비교하여 출고 가능 여부 확인
	$result = out_ck_state($outbound_id); 
	
	if ($result[0]['cnt'] == 1) { 
		$item_id = $result[0]['product_id'];
		$warehouse_id = $result[0]['warehouse_id'];
		$angle_id = $result[0]['angle_id'];
		$qua = $result[0]['outbound_quantity'];
		$step = $result[0]['step'];
		$company_name = $result[0]['company_name'];		
		$company_id = $result[0]['company_id'];		
	
		// 출고 가능한지 먼저 확인
		$count2 = 0;
		$result_ck = is_able_ck($item_id, $warehouse_id, $angle_id, $qua, $company_id);  	
		$count2 = $result_ck[0]['count2'];
			
		if ($count2 == 1) {	
			// 출고 완료 처리
			$stmt = $conn->prepare("UPDATE wms_outbound SET state = 1, rdate = CURDATE() WHERE planned_quantity = outbound_quantity AND outbound_id = :outbound_id");
			$stmt->bindParam(':outbound_id', $outbound_id);
			$stmt->execute();	
			
			// 실제 출고 처리
			add_outStock($item_id, $warehouse_id, $angle_id, $qua, $step, $company_id); 
		} else { 
			echo "<script>alert('실재고수량과 출고수량이 일치하지 않습니다.');</script>";
		}					
	}
}


// 아웃바운드 출고삭제용 정보 가져오기
function getwms_wms_outbound_info($outbound_id) {
    global $conn;
 
    $sql = "SELECT ";
    $sql .= "    i.outbound_id AS outbound_id, p.item_name AS item_name,  ";
    $sql .= "    w.warehouse_name AS warehouse_name, ";
    $sql .= "    ( ";
    $sql .= "        SELECT angle_name  ";
    $sql .= "        FROM wms_angle  ";
    $sql .= "        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
    $sql .= "    ) AS angle_name, ";
    $sql .= "    ( ";
    $sql .= "        SELECT cate_name  ";
    $sql .= "        FROM wms_company  ";
    $sql .= "        WHERE cate_id = i.company_id ";
    $sql .= "    ) AS company_name,  ";
    $sql .= "    i.planned_quantity,  ";
    $sql .= "    i.outbound_quantity,  ";
    $sql .= "    i.plan_date,  ";
    $sql .= "    i.rdate,  ";
    $sql .= "    i.state ";
    $sql .= "FROM ";
    $sql .= "    wms_outbound i   ";
    $sql .= "JOIN  ";
    $sql .= "    wms_items p ON p.item_id = i.product_id ";
    $sql .= "JOIN  ";
    $sql .= "    wms_warehouses w ON w.warehouse_id = i.warehouse_id AND i.outbound_id = :outbound_id";
	
	$stmt = $conn->prepare($sql);
    $stmt->bindParam(':outbound_id', $outbound_id);
    $stmt->execute();
    
    return $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 아웃바운드 삭제 
function del_outbound($outbound_id) {
    global $conn;
		
    $stmt = $conn->prepare("UPDATE wms_outbound SET delYN = 'Y' WHERE outbound_id = :outbound_id");
    $stmt->bindParam(':outbound_id', $outbound_id);
    $stmt->execute();	
    
    // add_history('B','창고명을 변경',$warehouse_name_before[0]['warehouse_name'],$warehouse_name);			
}



// 출고지시 목록 가져오기
function getwms_outbounds($start_record_number, $itemsPerPage, $search, $SearchString) {
    global $conn;

    //$stmt = $conn->query("SELECT p.item_name as item_name, (SELECT w.warehouse_name  from  wms_outbound i  JOIN wms_warehouses w  on w.warehouse_id = i.warehouse_id where  p.item_id = i.product_id) as warehouse_name, (select angle_name from wms_angle where angle_id = i.angle_id and warehouse_id = i.warehouse_id) as angle_name, company_name, planned_quantity, outbound_quantity, plan_date, rdate, state  from  wms_outbound i  JOIN wms_items p  on p.item_id = i.product_id  where 1=1 ".$condition_sql."  order by i.plan_date desc limit $start_record_number,$itemsPerPage");

    $sql = "	SELECT ";
    $sql .= "	    i.outbound_id as outbound_id, p.item_name AS item_name,  ";
    $sql .= "	    w.warehouse_name AS warehouse_name, ";
    $sql .= "	    ( ";
    $sql .= "	       SELECT angle_name  ";
    $sql .= "	        FROM wms_angle  ";
    $sql .= "	        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
    $sql .= "	    ) AS angle_name, ";

    $sql .= "	    (  ";
    $sql .= "	       SELECT cate_name  ";
    $sql .= "	       from wms_company  ";
    $sql .= "	       where cate_id = i.company_id  ";
    $sql .= "	    ) as company_name,  ";
    // $sql = $sql."	    i.company_id,  ";
    $sql .= "	    i.planned_quantity,  ";
    $sql .= "	   i.outbound_quantity,  ";
    $sql .= "	    i.plan_date,  ";
    $sql .= "	    i.rdate,  ";
    $sql .= "	    i.state, ";

    $sql .= "	     COALESCE(( ";
    $sql .= "	       SELECT quantity ";
    $sql .= "	       from wms_stock ";
    $sql .= "	       where item_id = i.product_id AND warehouse_id = i.warehouse_id AND angle_id = i.angle_id ";
    $sql .= "	     ), 0)  as stock_quantity ";

    $sql .= "	FROM  ";
    $sql .= "	    wms_outbound i   ";
    $sql .= "	JOIN  ";
    $sql .= "	    wms_items p ON p.item_id = i.product_id ";
    $sql .= "	JOIN  ";
    $sql .= "	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
    // $sql = $sql." WHERE  (  SELECT cate_name FROM wms_company  WHERE cate_id = i.company_id  )  like '%B%'	";	
    $sql .= $SearchString;	
    $sql .= " and i.delYN = 'N' order by i.plan_date desc, i.outbound_id desc limit $start_record_number,$itemsPerPage ";
    //echo $sql;
    $stmt = $conn->query($sql);	
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result; 
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * HISTORY관리 * //
// Get current 입고 stock_history information
function get_in_Stock_history_detail($start_record_number, $itemsPerPage) {
    global $conn;
    $stmt = $conn->query("SELECT s.item_id, s.to_warehouse_id, s.wms_stock_id, s.angle_id, s.quantity, s.rdate, s.in_stock_who, s.in_stock_ip, i.item_name, v.angle_name, v.warehouse_name        FROM wms_in_stock_history s        LEFT JOIN view_warehouse_angle v ON s.angle_id = v.angle_id AND s.to_warehouse_id = v.warehouse_id        LEFT JOIN wms_items i ON s.item_id = i.item_id        ORDER BY s.rdate DESC        LIMIT $start_record_number, $itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get current 입고 stock_history information
function get_in_Stock_history_day_cnt7() {
    global $conn;
    $stmt = $conn->query("SELECT DATE_SUB(CURDATE(), INTERVAL n DAY) AS input_day, COALESCE(SUM(quantity), 0) AS total_sum FROM (     SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3     UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 ) AS dates LEFT JOIN wms_in_stock_history ON DATE(rdate) = DATE_SUB(CURDATE(), INTERVAL n DAY) WHERE DATE_SUB(CURDATE(), INTERVAL n DAY) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE_SUB(CURDATE(), INTERVAL n DAY) ORDER BY DATE_SUB(CURDATE(), INTERVAL n DAY)");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get current 입고 stock_history information
function get_in_Stock_history_day_cnt30() {
    global $conn;
    $stmt = $conn->query("SELECT DATE_FORMAT(date_range.date, '%Y-%m-%d') AS input_day, COALESCE(SUM(s.quantity), 0) AS total_sum        FROM (            SELECT DATE_SUB(CURDATE(), INTERVAL n DAY) AS date            FROM (                SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3                UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6                UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9                UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12                UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15                UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18                UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21                UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24                UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27                UNION ALL SELECT 28 UNION ALL SELECT 29            ) AS date_range        ) AS date_range        LEFT JOIN wms_in_stock_history s ON DATE_FORMAT(date_range.date, '%Y-%m-%d') = DATE_FORMAT(s.rdate, '%Y-%m-%d')        WHERE date_range.date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)        GROUP BY DATE_FORMAT(date_range.date, '%Y-%m-%d')        ORDER BY DATE_FORMAT(date_range.date, '%Y-%m-%d')");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get current 출고 stock_history information
function get_out_Stock_history_detail($start_record_number, $itemsPerPage) {
    global $conn;
    $stmt = $conn->query("SELECT s.item_id, s.to_warehouse_id, s.wms_stock_id, s.angle_id, s.quantity, s.rdate, s.out_stock_who, s.out_stock_ip, i.item_name, v.angle_name, v.warehouse_name        FROM wms_out_stock_history s        LEFT JOIN view_warehouse_angle v ON s.angle_id = v.angle_id AND s.to_warehouse_id = v.warehouse_id        LEFT JOIN wms_items i ON s.item_id = i.item_id        ORDER BY s.rdate DESC        LIMIT $start_record_number, $itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get current 출고 stock_history information
function get_out_Stock_history_day_cnt7() {
    global $conn;
    $stmt = $conn->query("SELECT DATE_SUB(CURDATE(), INTERVAL n DAY) AS input_day, COALESCE(SUM(quantity), 0) AS total_sum        FROM (            SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3            UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6        ) AS dates        LEFT JOIN wms_out_stock_history ON DATE(rdate) = DATE_SUB(CURDATE(), INTERVAL n DAY)        WHERE DATE_SUB(CURDATE(), INTERVAL n DAY) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)        GROUP BY DATE_SUB(CURDATE(), INTERVAL n DAY)        ORDER BY DATE_SUB(CURDATE(), INTERVAL n DAY)");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get current 출고 stock_history information
function get_out_Stock_history_day_cnt30() {
    global $conn;
    $stmt = $conn->query("SELECT DATE_FORMAT(date_range.date, '%Y-%m-%d') AS input_day, COALESCE(SUM(s.quantity), 0) AS total_sum        FROM (            SELECT DATE_SUB(CURDATE(), INTERVAL n DAY) AS date            FROM (                SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3                UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6                UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9                UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12                UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15                UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18                UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21                UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24                UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27                UNION ALL SELECT 28 UNION ALL SELECT 29            ) AS date_range        ) AS date_range        LEFT JOIN wms_out_stock_history s ON DATE_FORMAT(date_range.date, '%Y-%m-%d') = DATE_FORMAT(s.rdate, '%Y-%m-%d')        WHERE date_range.date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)        GROUP BY DATE_FORMAT(date_range.date, '%Y-%m-%d')        ORDER BY DATE_FORMAT(date_range.date, '%Y-%m-%d')");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Get history information
function get_history($start_record_number, $itemsPerPage, $add_condition) {
    global $conn;

    $stmt = $conn->query("SELECT * FROM wms_history WHERE 1=1 $add_condition ORDER BY h_date DESC LIMIT $start_record_number, $itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get history information for home slide menu
function get_history_item_list($item) {
    global $conn;
    $stmt = $conn->query("SELECT DISTINCT $item, h_loc_code, h_location FROM wms_history ORDER BY $item ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get history information for home
function get_history_item_list_cate($item, $h_loc_code) {
    global $conn;
    $stmt = $conn->query("SELECT DISTINCT $item, h_loc_code, h_location FROM wms_history WHERE h_loc_code = '$h_loc_code' ORDER BY $item ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 운영자관리 * //
// 조회
function get_admin_cate_add_cate_use($start_record_number, $itemsPerPage, $sql_cate_use) {
    global $conn;
    $stmt = $conn->prepare("SELECT cate_id, cate_name, cate_use, cate_expose, cate_rdate, cate_comment, cate_admin_role, (SELECT COUNT(*) FROM wms_admin WHERE admin_use='Y' AND wms_admin_cate.cate_admin_role = wms_admin.admin_role) as use_admin_role_cnt, (SELECT COUNT(*) FROM wms_admin WHERE admin_use='N' AND wms_admin_cate.cate_admin_role = wms_admin.admin_role) as notuse_admin_role_cnt  FROM wms_admin_cate WHERE cate_admin_role <> 100 AND cate_use $sql_cate_use AND cate_admin_role <= :admin_role ORDER BY cate_admin_role DESC LIMIT :start_record_number, :itemsPerPage");
    $stmt->bindParam(':admin_role', $_SESSION['admin_role'], PDO::PARAM_INT);
    $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_admin_cate_reg($start_record_number, $itemsPerPage) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM wms_admin_cate WHERE cate_admin_role <= :admin_role AND cate_use = 'Y' AND cate_expose = 'Y' ORDER BY cate_admin_role DESC LIMIT :start_record_number, :itemsPerPage");
    $stmt->bindParam(':admin_role', $_SESSION['admin_role'], PDO::PARAM_INT);
    $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_admin_Cate($cate_admin_role, $cate_name, $cate_comment) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_admin_cate (cate_name, cate_rdate, cate_comment, cate_admin_role) VALUES (:cate_name, :cate_rdate, :cate_comment, :cate_admin_role)");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindValue(':cate_rdate', date("Y-m-d H:i:s"));
    $stmt->bindParam(':cate_comment', $cate_comment);
    $stmt->bindParam(':cate_admin_role', $cate_admin_role);
    $stmt->execute();
}

function get_admin_cate_search1($cate_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM wms_admin_cate WHERE cate_id = :cate_id LIMIT 0,1");
    $stmt->bindParam(':cate_id', $cate_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 분류명수정, 운영자카테고리 업데이트
function update_admin_Cate($cate_admin_role, $cate_name, $cate_id, $cate_comment, $before_cate_admin_role) {
    global $conn;
    $stmt = $conn->prepare("UPDATE wms_admin_cate SET cate_admin_role = :cate_admin_role, cate_name = :cate_name, cate_comment = :cate_comment WHERE cate_id = :cate_id");
    $stmt->bindParam(':cate_admin_role', $cate_admin_role);
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_comment', $cate_comment);
    $stmt->bindParam(':cate_id', $cate_id);
    $stmt->execute();

    $stmt2 = $conn->prepare("UPDATE wms_admin SET admin_role = :new_admin_role WHERE admin_role = :before_admin_role");
    $stmt2->bindParam(':new_admin_role', $cate_admin_role);
    $stmt2->bindParam(':before_admin_role', $before_cate_admin_role);
    $stmt2->execute();
}


// 사용자 목록 가져오기
function getwms_users2($start_record_number, $itemsPerPage, $search, $SearchString) {
    global $conn;

    // 검색조건
    $condition_sql = "";
    if ($SearchString != "") {
        $condition_sql = " AND  $search LIKE :search_string ";
    }

    //  where 1 = 1 ".$condition_sql."
        $stmt = $conn->prepare("SELECT * FROM wms_user  where 1=1 $condition_sql ORDER BY user_rdate DESC LIMIT :start_record_number, :itemsPerPage");


    if ($SearchString != "") {
        $stmt->bindValue(':search_string', '%' . $SearchString . '%');
    }
    $stmt->bindValue(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 운영자 목록 가져오기
function getwms_users($start_record_number, $itemsPerPage, $search, $SearchString) {
    global $conn;

    // 검색조건
    $condition_sql = "";
    if ($SearchString != "") {
        $condition_sql = " AND a.$search LIKE :search_string ";
    }

    //  where 1 = 1 ".$condition_sql."
    if ($_SESSION['admin_role'] == "100") {
        $stmt = $conn->prepare("SELECT * FROM wms_admin a JOIN wms_admin_cate c ON a.admin_role = c.cate_admin_role $condition_sql ORDER BY c.cate_admin_role DESC LIMIT :start_record_number, :itemsPerPage");
    } else {
        $stmt = $conn->prepare("SELECT * FROM wms_admin a JOIN wms_admin_cate c ON a.admin_role = c.cate_admin_role AND admin_role < 100 $condition_sql ORDER BY c.cate_admin_role DESC LIMIT :start_record_number, :itemsPerPage");
    }

    if ($SearchString != "") {
        $stmt->bindValue(':search_string', '%' . $SearchString . '%');
    }
    $stmt->bindValue(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 운영자 추가
function addUser($user_id, $user_name) {
    global $conn;
    $user_pw = password_hash('1234', PASSWORD_DEFAULT);
	$user_token = strrev(substr(password_hash(date("Y-m-d H:i:s"), PASSWORD_DEFAULT), -32)); 
    $stmt = $conn->prepare("INSERT INTO wms_user (user_id, user_name, user_pw, user_rdate, user_token) VALUES (:user_id, :user_name, :user_pw, :user_rdate, :user_token)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':user_pw', $user_pw);
    $stmt->bindParam(':user_token', $user_token);
    $stmt->bindValue(':user_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();
}

// 운영자 추가
function addAdmin($admin_id, $admin_name, $cate_admin_role) {
    global $conn;
    $admin_pw = password_hash('1234', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO wms_admin (admin_id, admin_name, admin_pw, admin_role, admin_rdate) VALUES (:admin_id, :admin_name, :admin_pw, :admin_role, :admin_rdate)");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':admin_name', $admin_name);
    $stmt->bindParam(':admin_pw', $admin_pw);
    $stmt->bindParam(':admin_role', $cate_admin_role);
    $stmt->bindValue(':admin_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();
}

// 운영자 분류명등록 분류숫자 중복검사
function ck_cate_cnt($role_num) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(cate_id), '0') AS role_cnt FROM `wms_admin_cate` WHERE delYN = 'N' AND cate_admin_role = :role_num");
    $stmt->bindParam(':role_num', $role_num);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 사용자등록 아이디 중복검사
function ck_user2_cnt($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(user_id), '0') AS user_cnt FROM `wms_user` WHERE delYN = 'N' AND user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 운영자등록 아이디 중복검사
function ck_user_cnt($admin_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(admin_id), '0') AS user_cnt FROM `wms_admin` WHERE delYN = 'N' AND admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 거래처 중복검사
function ck_company_cnt($cate_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(cate_id), '0') as cate_cnt FROM `wms_company` WHERE delYN = 'N' and cate_name = :cate_name");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 거래처 조회
function getwms_company($start_record_number, $itemsPerPage) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM wms_company WHERE delYN = 'N' and cate_name <> '미지정' LIMIT :start_record_number, :itemsPerPage");
    $stmt->bindValue(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 거래처명 1가지 조회
function getwms_company_search1($cate_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT cate_name FROM wms_company WHERE cate_id = :cate_id LIMIT 1");
    $stmt->bindParam(':cate_id', $cate_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 거래처 추가
function addcompany($cate_name) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_company (cate_name, cate_rdate) VALUES (:cate_name, :cate_rdate)");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindValue(':cate_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();
    
    add_history('A', '거래처를 등록', $cate_name, '');
}

// 거래처 업데이트
function updatecompany($cate_name, $cate_id) {
    global $conn;
    
    $cate_name_before = getwms_company_search1($cate_id);
    
    $stmt = $conn->prepare("UPDATE wms_company SET cate_name = :cate_name WHERE cate_id = :cate_id");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_id', $cate_id);
    $stmt->execute();

    add_history('B', '거래처명을 변경', $cate_name_before[0]['cate_name'], $cate_name);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 접근권한관리 * //
// 접근관리 max 값 가져오기 (초기에 접근관리 대상추가시, id값 부여를 위해 값을 확인)
function getwms_access_crud_maxid() {
    global $conn;
    $stmt = $conn->query("SELECT IFNULL(max(access_id), '1') as access_id FROM wms_access_crud");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 접근관리 추가
function add_access($access_id, $access_name) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_access_crud (access_id, access_name, access_type, access_value, access_rdate, access_order) VALUES (:access_id, :access_name, 'R', '99', :access_rdate, :access_order)");
    $stmt->bindParam(':access_id', $access_id);
    $stmt->bindParam(':access_name', $access_name);
    $stmt->bindParam(':access_rdate', date("Y-m-d H:i:s"));
    $stmt->bindParam(':access_order', $access_id);
    $stmt->execute();
    
    $stmt->bindParam(':access_type', $access_type);
    $stmt->execute();
}

// 접근관리 조회
function wms_access() {
    global $conn;
    $stmt = $conn->query("SELECT cate_admin_role, cate_name FROM wms_admin_cate WHERE cate_admin_role > 1 AND cate_admin_role < 100 AND cate_use = 'Y' ORDER BY cate_admin_role DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 접근관리 조회 출력
function wms_access_crud($access_id, $access_type) {
    global $conn;
    $stmt = $conn->prepare("SELECT a.access_id AS access_id, c.cate_name AS cate_name, c.cate_admin_role AS cate_admin_role, a.access_type AS access_type FROM wms_admin_cate c INNER JOIN wms_access_crud a ON a.access_value = c.cate_admin_role WHERE c.cate_use = 'Y' AND a.access_type = :access_type AND a.access_id = :access_id ORDER BY c.cate_admin_role DESC");
    $stmt->bindParam(':access_type', $access_type);
    $stmt->bindParam(':access_id', $access_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 분류 추가하기 (일반)
function wms_access_add($access_id, $access_type) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM wms_admin_cate WHERE cate_admin_role <> 100 AND cate_admin_role <> 0 AND cate_admin_role <> 1 AND cate_use = 'Y' AND cate_admin_role NOT IN (SELECT access_value FROM wms_access_crud WHERE access_id = :access_id AND access_type = :access_type) ORDER BY cate_admin_role DESC");
    $stmt->bindParam(':access_id', $access_id);
    $stmt->bindParam(':access_type', $access_type);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 분류 추가하기 (특정, SYS)
function wms_access_add_onlysystme($access_id, $access_type) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM wms_admin_cate WHERE cate_admin_role <> 100 AND cate_admin_role <> 0 AND cate_admin_role <> 1 AND cate_use = 'Y' AND cate_admin_role NOT IN (SELECT access_value FROM wms_access_crud WHERE access_id = :access_id AND access_type = :access_type) AND cate_admin_role > 90 ORDER BY cate_admin_role DESC");
    $stmt->bindParam(':access_id', $access_id);
    $stmt->bindParam(':access_type', $access_type);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 접근권한관리 목록 가져오기
function getwms_access_crud($start_record_number, $itemsPerPage, $search, $SearchString) {
    global $conn;
    
	// 세션 sql_mode 설정 변경
	$conn->exec("SET SESSION sql_mode=(SELECT REPLACE(@@SESSION.sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
	
    // 검색조건
    $condition_sql = "";
    if ($SearchString!="") {
        $condition_sql = " AND a.".$search." LIKE '%".$SearchString."%' ";    
    } else {
        $condition_sql = "";    
    }
    
    // where 1 = 1 ".$condition_sql."  
    $stmt = $conn->query("SELECT * FROM wms_access_crud GROUP BY access_id ORDER BY access_order, access_id");
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result; 
}
// 접근권한관리 역할지정
function m04_role_add($m04_access_id, $m04_access_name, $m04_access_type, $m04_access_value) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_access_crud (access_id, access_name, access_type, access_value) VALUES (:access_id, :access_name, :access_type, :access_value)");
    $stmt->bindParam(':access_id', $m04_access_id);
    $stmt->bindParam(':access_name', $m04_access_name);
    $stmt->bindParam(':access_type', $m04_access_type);
    $stmt->bindParam(':access_value', $m04_access_value);
    $stmt->execute();
}
 
// 접근권한관리 일괄삽입 (이미 삽입건 제외하고 넣기)
function m04_role_add_all($m04_access_id, $m04_access_name, $m04_access_type, $m04_access_value) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_access_crud (access_id, access_name, access_type, access_value) SELECT :access_id, :access_name, :access_type, cate_admin_role FROM `wms_admin_cate` WHERE cate_admin_role NOT IN (0, 1, 100) AND cate_use = 'Y' AND cate_admin_role NOT IN (SELECT access_value FROM wms_access_crud WHERE access_id = :access_id AND access_type = :access_type)");
    $stmt->bindParam(':access_id', $m04_access_id);
    $stmt->bindParam(':access_name', $m04_access_name);
    $stmt->bindParam(':access_type', $m04_access_type);
    $stmt->execute();
}

// 전체 유효분류
//SELECT cate_admin_role FROM `wms_admin_cate` where cate_admin_role not in (0,1,100) and cate_use = 'Y'  // 2,5,41,61,99

//select access_value from wms_access_crud where access_id = '4' and access_type = 'D'   //  99, 61

//SELECT cate_admin_role FROM `wms_admin_cate` where cate_admin_role not in (0,1,100) and cate_use = 'Y' and  cate_admin_role not in (select access_value from wms_access_crud where access_id = '4' and access_type = 'D')  // 2, 5, 41

//INSERT INTO wms_access_crud (access_id, access_name, access_type, access_value) SELECT '4', '제품카테고리', 'D', cate_admin_role FROM `wms_admin_cate` where cate_admin_role not in (0,1,100) and cate_use = 'Y' and  cate_admin_role not in (select access_value from wms_access_crud where access_id = '4' and access_type = 'D')  // 2, 5, 41

// 접근권한관리 항목삭제
function m04_role_del($access_id,$access_type,$role) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM wms_access_crud WHERE access_id = :access_id AND access_type = :access_type AND access_value = :role");
    $stmt->bindParam(':access_id', $access_id);
    $stmt->bindParam(':access_type', $access_type);
    $stmt->bindParam(':role', $role);
    $stmt->execute();	
}

//접근권한관리 > 관리목록 대상 오름정렬시키기
function list_up_sorting($num){
    global $conn;
	$num_under = $num - 1;
    $stmt = $conn->prepare("UPDATE wms_access_crud SET access_order = 0 WHERE access_order = :num_under");
    $stmt->bindParam(':num_under', $num_under);
    $stmt->execute();	
	
    $stmt = $conn->prepare("UPDATE wms_access_crud SET access_order = :num_under  WHERE access_order = :num");
    $stmt->bindParam(':num_under', $num_under);
    $stmt->bindParam(':num', $num);
    $stmt->execute();		
	
    $stmt = $conn->prepare("UPDATE wms_access_crud SET access_order = :num  WHERE access_order = 0");
    $stmt->bindParam(':num', $num);
    $stmt->execute();		
} 
 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 시스템관리 * //
// 시스템설정 목록 조회
function getwms_sys_status($start_record_number,$itemsPerPage) {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_setting limit $start_record_number,$itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 

// 특정시스템설정명 추출하기
function getwms_set_name($set_id) {
    global $conn;
	
    $stmt = $conn->prepare("SELECT *  FROM wms_setting WHERE set_id = :set_id");
    $stmt->bindParam(':set_id', $set_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  
 //  업데이트 중복검사  
function update_set_cnt($set_id,$set_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(set_id), '0') AS item_cnt FROM wms_setting WHERE set_id <> :set_id AND set_name = :set_name");
    $stmt->bindParam(':set_id', $set_id, PDO::PARAM_INT);
    $stmt->bindParam(':set_name', $set_name, PDO::PARAM_STR);
    $stmt->execute(); 
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
// 시스템항목명 업데이트  
function update_set($set_id, $set_var, $col) {
    global $conn;
    $stmt = null;
    if ($col == "set_name") {
        $stmt = $conn->prepare("UPDATE wms_setting SET set_name = :set_var, set_rdate = :set_rdate WHERE set_id = :set_id");
    } elseif ($col == "set_comment") {
        $stmt = $conn->prepare("UPDATE wms_setting SET set_comment = :set_var, set_rdate = :set_rdate WHERE set_id = :set_id");
    }
    $stmt->bindParam(':set_id', $set_id);
    $stmt->bindParam(':set_var', $set_var);
    $stmt->bindParam(':set_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();						
}
 
// Add 시스템설정 항목추가
function add_setsys_col($set_name, $set_comment) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_setting (set_name, set_comment, set_rdate) VALUES (:set_name, :set_comment, :set_rdate)");
    $stmt->bindParam(':set_name', $set_name);
    $stmt->bindParam(':set_comment', $set_comment);
    $stmt->bindParam(':set_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();	
	
    $stmt = $conn->prepare("UPDATE wms_setting JOIN (SELECT MAX(set_id) + 1 AS next_set_id FROM wms_setting) AS max_set_id SET wms_setting.set_id = max_set_id.next_set_id WHERE wms_setting.set_id = 0");
    $stmt->execute();			
}
 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 슬라이드메뉴 * //

// 관리자모드 현접속자 권한명 출력
function cate_name(){
    global $conn;	
	$stmt = $conn->prepare("SELECT c.cate_name as cate_name FROM wms_admin_cate c JOIN wms_admin a ON c.cate_admin_role = a.admin_role WHERE a.admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 공통다중사용 * //

// 창고앵글 일괄삭제 setting 값 가져오기
function getwms_setting_state($set_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT set_state FROM wms_setting WHERE set_id = :set_id");
    $stmt->bindParam(':set_id', $set_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  
 // 사용자 비밀번호 초기화
function user_reset_pw($user_id) {
    global $conn;
    $user_pw = password_hash('1234', PASSWORD_DEFAULT);	
    $stmt = $conn->prepare("UPDATE wms_user SET user_pw = :user_pw WHERE user_id = :user_id");
    $stmt->bindParam(':user_pw', $user_pw);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}
  

 // 사용자 활성/비활성 변경
function user_change_state($user_id, $user_use) {
    global $conn;
    $stmt = $conn->prepare("UPDATE wms_user SET user_use = :user_use WHERE user_id = :user_id");
    $stmt->bindParam(':user_use', $user_use);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}

 // 사용자 비밀번호 변경 
function user_change_pw($user_id, $user_pw) {
    global $conn;
    $user_pw = password_hash($user_pw, PASSWORD_DEFAULT);	
    $stmt = $conn->prepare("UPDATE wms_user SET user_pw = :user_pw WHERE user_id = :user_id");
    $stmt->bindParam(':user_pw', $user_pw);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}

 // 운영자 비밀번호 초기화
function reset_pw($user_id) {
    global $conn;
    $admin_pw = password_hash('1234', PASSWORD_DEFAULT);	
    $stmt = $conn->prepare("UPDATE wms_admin SET admin_pw = :admin_pw WHERE admin_id = :admin_id");
    $stmt->bindParam(':admin_pw', $admin_pw);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
}
 
 // 운영자 비밀번호 변경
function change_pw($admin_id, $admin_pw) {
    global $conn;
    $admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);	
    $stmt = $conn->prepare("UPDATE wms_admin SET admin_pw = :admin_pw WHERE admin_id = :admin_id");
    $stmt->bindParam(':admin_pw', $admin_pw);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
}
function permission_ck($where, $type, $who){
    global $conn;
    $stmt = $conn->prepare("SELECT CASE WHEN COUNT(*) = 0 THEN 'F' ELSE 'T' END AS pm_rst FROM wms_access_crud WHERE (access_name = :where AND access_type = :type AND access_value = :who) OR ('100' = :who)");
    $stmt->bindParam(':where', $where);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':who', $who);
    $stmt->execute();
    $result = $stmt->fetchColumn();
    return $result;
}

// 제품분류명 가져오기
function cate_id_to_cate_name($item_cate){
    global $conn;
    $stmt = $conn->prepare("SELECT cate_name FROM wms_cate WHERE cate_id = :item_cate");
    $stmt->bindParam(':item_cate', $item_cate);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);		
} 

// 제품분류번호 가져오기
function cate_name_to_cate_id($item_cate){
    global $conn;
    $stmt = $conn->prepare("SELECT cate_id FROM wms_cate WHERE cate_name = :item_cate");
    $stmt->bindParam(':item_cate', $item_cate);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // 단일 행을 가져옴

    // 결과가 있는지 확인하고 cate_id를 반환
    if ($result) {
        return $result['cate_id'];
    } else {
        return null; // 결과가 없는 경우 null 반환
    }	
} 
 
// 입출고 거래처명 가져오기
function company_id_to_company_name($item_cate){
    global $conn;
    $stmt = $conn->prepare("SELECT cate_name FROM wms_company WHERE cate_id = :item_cate");
    $stmt->bindParam(':item_cate', $item_cate);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}
 
// 입출고 거래처 ID 가져오기
function company_name_to_company_id($item_cate){
    global $conn;
    $stmt = $conn->prepare("SELECT cate_id FROM wms_company WHERE cate_name = :item_cate");
    $stmt->bindParam(':item_cate', $item_cate);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}

// 창고 목록 가져오기
function getwms_warehouses($start_record_number, $itemsPerPage, $search, $SearchString) {
    global $conn;
	
	// 검색조건
	$condition_sql = "";
	if ($SearchString != "") {
		$condition_sql = " WHERE ".$search." LIKE '%".$SearchString."%' ";	
	} else {
		$condition_sql = " WHERE 1 = 1 ";	
	}
	
    $stmt = $conn->prepare("SELECT warehouse_id, warehouse_code, warehouse_name, warehouse_rdate, IFNULL(( SELECT SUM(IFNULL((SELECT SUM(quantity) AS sum_quantity FROM wms_stock WHERE warehouse_id = a.warehouse_id AND angle_id = a.angle_id), 0)) AS sum_quantity FROM wms_angle a WHERE a.delYN = 'N' AND a.warehouse_id = w.warehouse_id), 0) AS sum_quantity_warehouse, ( SELECT COUNT(angle_id) FROM wms_angle WHERE angle_id <> 0 AND warehouse_id = w.warehouse_id AND delYN = 'N') AS angle_cnt FROM wms_warehouses w ".$condition_sql." AND warehouse_id <> 0 AND delYN = 'N' LIMIT :start_record_number, :itemsPerPage");
    $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// 창고 목록 가져오기
function getwms_warehouses_exist_angle($start_record_number, $itemsPerPage, $search, $SearchString) {
    global $conn;
	
	// 검색조건
	$condition_sql = "";
	if ($SearchString != "") {
		$condition_sql = " WHERE ".$search." LIKE '%".$SearchString."%' ";	
	} else {
		$condition_sql = " WHERE 1 = 1 ";	
	}
	
    $stmt = $conn->prepare("SELECT     warehouse_id,     warehouse_code,     warehouse_name,     warehouse_rdate,     IFNULL(
					(
						SELECT 
							SUM(IFNULL(
								(
									SELECT 
										SUM(quantity) AS sum_quantity 
									FROM 
										wms_stock 
									WHERE 
										warehouse_id = a.warehouse_id 
										AND angle_id = a.angle_id
								), 
								0
							)) AS sum_quantity 
						FROM 
							wms_angle a 
						WHERE 
							a.delYN = 'N' 
							AND a.warehouse_id = w.warehouse_id
					), 
					0
				) AS sum_quantity_warehouse, 
				(
					SELECT 
						COUNT(angle_id) 
					FROM 
						wms_angle 
					WHERE 
						angle_id <> 0 
						AND warehouse_id = w.warehouse_id 
						AND delYN = 'N'
				) AS angle_cnt 
			FROM 
				wms_warehouses w  
			" .$condition_sql."
				AND warehouse_id <> 0 
				AND delYN = 'N'
			HAVING 
		angle_cnt >= 1 LIMIT :start_record_number, :itemsPerPage");
    $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 창고명 업데이트 중복검사
function update_warehouse_cnt($warehouse_id, $warehouse_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(warehouse_id), '0') AS warehouse_cnt FROM wms_warehouses WHERE delYN = 'N' AND warehouse_id <> :warehouse_id AND warehouse_name = :warehouse_name");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':warehouse_name', $warehouse_name);
    $stmt->execute(); 
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}  

// 창고명 업데이트  
function update_warehouse($warehouse_id, $warehouse_name) {
    global $conn;
	
	$warehouse_name_before = warehouse_id_to_warehouse_name($warehouse_id);
	
	$stmt = $conn->prepare("UPDATE wms_warehouses SET warehouse_name = :warehouse_name, warehouse_rdate = :warehouse_rdate WHERE delYN = 'N' AND warehouse_id = :warehouse_id");
	$stmt->bindParam(':warehouse_id', $warehouse_id);
	$stmt->bindParam(':warehouse_name', $warehouse_name);
	$stmt->bindParam(':warehouse_rdate', date("Y-m-d H:i:s"));
	$stmt->execute();	
		
	add_history('B', '창고명을 변경', $warehouse_name_before[0]['warehouse_name'], $warehouse_name);			
}

function getwms_angle_namelist($warehouse_id) {
    global $conn;
 
    $stmt = $conn->prepare("SELECT a.angle_id AS angle_id, a.angle_name AS angle_name, a.warehouse_id AS warehouse, a.angle_use AS angle_use, a.angle_rdate AS rdate, a.angle_order AS angle_order,  IFNULL((SELECT SUM(quantity) AS sum_quantity FROM wms_stock WHERE warehouse_id = :warehouse_id AND angle_id = a.angle_id), 0) AS sum_quantity FROM wms_angle a WHERE delYN = 'N' AND warehouse_id = :warehouse_id ORDER BY angle_order DESC");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);	
} 

// 앵글로 재고이동 또는 제품입고등록시 거래처리스트 불러오기
function getwms_company_namelist() {
    global $conn;
 
    $stmt = $conn->query("SELECT cate_id, cate_name FROM wms_company WHERE delYN = 'N' ORDER BY cate_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

// 제품명 이름추출
function item_id_to_item_name($item_id){
    global $conn;
    $stmt = $conn->prepare("SELECT item_name FROM wms_items WHERE item_id = :item_id");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);		
} 
 
// 제품바코드 이름추출
function item_id_to_item_code($item_id){
    global $conn;
    $stmt = $conn->prepare("SELECT item_code FROM wms_items WHERE item_id = :item_id");
    $stmt->bindParam(':item_id', $item_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);		
} 
// 히스토리 추가 
// 히스토리 추가 
 function add_history($h_type,$h_action,$h_col1,$h_col2){
    session_start(); 
    global $conn;
	$date = date("Y-m-d H:i:s");	
	// 히스토리추가
	if ($h_action=="앵글내 제품목록 조회") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고의 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="앵글을 삽입") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고에 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="앵글을 삭제") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고의 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="창고를 등록") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','','".$h_action."','m02')");		
	}else if ($h_action=="창고를 삭제") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','','".$h_action."','m02')");		
	}else if ($h_action=="창고명을 변경") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','".$h_col2."','".$h_action."','m02')");		
	}else if ($h_action=="앵글명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','".$h_col2."','".$h_action."','m02')");		
	}else if ($h_action=="로그인 성공") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		
	}else if ($h_action=="로그인 실패") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$h_col1."','".$h_col2."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		
	}else if ($h_action=="로그아웃") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		
	}else if ($h_action=="제품을 등록") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품의 분류를 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품정보를 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품을 삭제") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if (($h_action=="창고 입고등록") || ($h_action=="창고 입고등록")) {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
	}else if ($h_action=="제품분류를 등록") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1." 이름으로 ".$h_col2."','','".$h_action."','m03')");		
	}else if ($h_action=="제품분류명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품분류명을 삭제") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="재고이동") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','재고관리','".$h_col1."','".$h_col2."','".$h_action."','m04')");		
	}else if ($h_action=="앵글로 이동") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','재고관리','".$h_col1."','".$h_col2."','".$h_action."','m04')");		
	}else if ($h_action=="거래처를 등록") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1." 이름으로 ".$h_col2."','','".$h_action."','m06')");		
	}else if ($h_action=="거래처명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
	}else if ($h_action=="출고완료") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
	}

    $stmt_history->execute();
}



function list_total_cnt($table_name) {
    global $conn;
    $stmt = $conn->query("SELECT count(*) as count FROM ".$table_name);
    $result= $stmt->fetch(PDO::FETCH_ASSOC);
	return $result['count'];
}

// 페이징처리
function paginate($totalItems, $itemsPerPage, $currentPage, $url) {
    // 전체 페이지 수 계산
    $totalPages = ceil($totalItems / $itemsPerPage); 

    // 현재 페이지를 범위 내에 유지
    $currentPage = max(1, min($totalPages, $currentPage));

    // 이전 페이지와 다음 페이지 계산
    $prevPage = ($currentPage > 1) ? $currentPage - 1 : null;
    $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

    // 페이지 링크 생성
    $pagination = '<div class="paging" style="height:50px;padding-top:20px">';
    $pagination .= '<a href="' . $url . '?page=1&itemsPerPage=' . $itemsPerPage . '"><span class="glyphicon glyphicon glyphicon-backward" aria-hidden="true"></span></a> ';

    // 이전 페이지 링크
    if ($prevPage !== null) {
        $pagination .= '<a href="' . $url . '?page=' . $prevPage . '&itemsPerPage=' . $itemsPerPage . '"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>';
    } else {
        $pagination .= '<a href="' . $url . '?page=1&itemsPerPage=' . $itemsPerPage . '"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>';
    }

    // 페이지 번호 링크
    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = ($i == $currentPage) ? ' current' : '';
        $pagination .= '<a href="' . $url . '?page=' . $i . '&itemsPerPage=' . $itemsPerPage . '" class="num' . $activeClass . '" style="font-size:18px;color:#666;text-decoration: none"><span>' . $i . '</span></a>';
    }

    // 다음 페이지 링크
    if ($nextPage !== null) {
        $pagination .= '<a href="' . $url . '?page=' . $nextPage . '&itemsPerPage=' . $itemsPerPage . '"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a> ';
    } else {
        if ($nextPage === "") {
            $nextPage = $totalPages;
        }
        $pagination .= '<a href="' . $url . '?page=' . $nextPage . '&itemsPerPage=' . $itemsPerPage . '"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a> ';
    }

    $pagination .= '<a href="' . $url . '?page=' . $totalPages . '&itemsPerPage=' . $itemsPerPage . '"><span class="glyphicon glyphicon glyphicon-forward" aria-hidden="true"></span></a> ';

    $pagination .= '</div>';

    return $pagination;
}
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
// 앞자리 문자가 숫자인지 판별하는 함수
function isFirstCharacterDigit($value) {
    if (!is_string($value) || strlen($value) === 0) {
        return false;
    }
    
    // 문자열의 첫 번째 문자가 숫자인지 확인
    return ctype_digit($value[0]);
}
?>	