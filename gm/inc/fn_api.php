<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/db_connection.php');

class UserManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 사용자 등록
    public function registerUser($user_id, $user_pw, $user_role) {
        $user_pw = password_hash($user_pw, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO wms_user (user_id, user_pw, user_role) VALUES (:user_id, :user_pw, :user_role)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_pw', $user_pw);
        $stmt->bindParam(':user_role', $user_role);
        $stmt->execute();
    }





    // 사용자 로그인(안씀. 다른용도 스웨거로그인)
    public function loginUser($user_id, $user_pw) {
		
        $stmt = $this->conn->prepare("SELECT * FROM wms_user WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($user_pw, $user['user_pw'])) {
			
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

			$_SESSION['user_idx']   = $user['user_idx'];
			$_SESSION['user_id']    = $user['user_id'];
			$_SESSION['user_name']    = $user['user_name'];
			$_SESSION['user_role']  = $user['user_role'];   
			echo "<script>location.href='/swagger-ui/index.html';</script>";
        } else {
	
			// exit();	 
            return false;
        }
    }
 
 
 

    // 사용자 로그인
    public function loginUser2($user_id, $user_pw) {
		
		//$_SESSION[token]=date("YmdHis"); // 토큰생성 
		//$_SESSION[tokensave]=$_SESSION[token]; //토큰을 다른 세션에 저장
		//$rand_token=mt_rand(1,9999);
		//$_SESSION[fake]=$rand_token;//봇 테러 방지를 위한 꼼수
		//$faketoken=$_SESSION[tokensave]-$_SESSION[fake];	
		
		$headers = getallheaders();
		$bearer_key = '';

		if (isset($headers['bearer_key'])) {
			 $bearer_key = $headers['bearer_key'];
		}

        $stmt = $this->conn->prepare("SELECT * FROM wms_user WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($user_pw, $user['user_pw'])) {
			
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

			$_SESSION['user_idx']   = $user['user_idx'];
			$_SESSION['user_id']    = $user['user_id'];
			$_SESSION['user_name']    = $user['user_name'];
			$_SESSION['user_role']  = $user['user_role'];   
			

			//add_history('A','로그인 성공',$_SESSION['user_role'],'');   
			add_history('A','로그인 성공',$user['user_id'],$user['user_name']);   


			// 101 이용중지 계정
			if ($user['user_use']=="N") {

				$response_arr = [
					"header" => [
						"resultCode" => 101,
						"codeName" => "이용중지 계정"
					],
					"body" => [
						"data" => null,
						"msg" => "이용중지된 계정입니다."
					]
				];
 
				header('Content-Type: application/json');	
				echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
				
			// 102 삭제계정				
			}elseif($user['delYN']=="Y"){
				$response_arr = [
					"header" => [
						"resultCode" => 102,
						"codeName" => "삭제 계정"
					],
					"body" => [
						"data" => null,
						"msg" => "삭제처리된 계정입니다."
					]
				];
 
				header('Content-Type: application/json');	
				echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);	
				
			}else{
			// 200  정상로그인
				$response_arr = [
					"header" => [
						"resultCode" => 200,
						//"userToken" => "MTY4NjkxNjE0NjQ4M0l1ZW55Q2dsR3A=", //$bearer_key, 
						"codeName" => "SUCCESS"
					],
					"body" => [
						"data" => [					
							"user_idx" => $user['user_idx'],
							"user_id" => $user['user_id'],
							"user_name" => $user['user_name'],
							"user_pw" => $user['user_pw'],
							"user_role" => $user['user_role'],
							"user_rdate" => $user['user_rdate'],
							"user_token" => $user['user_token'],
							"user_use" => $user['user_use'],
							"delYN" => $user['delYN']
						]
					]
				];
 
				header('Content-Type: application/json');	
				//header('token: MTY4NjkxNjE0NjQ4M0l1ZW55Q2dsR3A=');
				echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

				exit();
				return true;  					
			}
 
			
        } else {
			$user_name_search = user_id_to_user_name($user_id);			
			//add_history('A','로그인 실패',$user_name_search[0]['user_name'],$user_id);	
			add_history('A','로그인 실패','사용자',$user_id);	
			
			$user = [];
			$user = ["NOT_FOUND_USER"] + $user;

			$user["codeName"] = $user["0"];
			unset($user["0"]);
			
			
			$response_arr = [
				"header" => [
					"resultCode" => 100,
					"codeName" => "NOT_FOUND_USER"
				],
				"body" => [
					"data" => null,
					"msg" => "아이디와 비밀번호를 확인 해 주세요."
				]
			];


			    header('Content-Type: application/json');
				 echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);	
			// exit();	 
            return false;
        }
    }
	
 
    // 사용자 로그아웃
    public function logoutUser() {				
        session_start();
		//add_history('A','로그아웃',$_SESSION['user_role'],'');					
		
        session_unset();
        session_destroy();
    }

    // 사용자 권한 확인
    public function checkUserRole() {
        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return false; // 사용자가 로그인하지 않았음
        }

        $stmt = $this->conn->prepare("SELECT user_role FROM wms_user WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();  //$userRole = $stmt->fetchColumn();
       
        $userRole = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        return $userRole; // === $requiredRole;
    }
	
}
 

$userManager = new UserManager($conn);
 

// API_NUM 선언 // 클라이언트에서 POST로 전송된 데이터 받기
$API_NUM = 0;
$token = 0; 


if (isset($_GET['API_NUM'])) {	$API_NUM = $_GET['API_NUM']; }
if ($API_NUM == 0) { $API_NUM = $_POST['API_NUM'];  }
if (isset($_POST['token'])) { $token = $_POST['token'];  } 
 
if ($API_NUM==1) {	session_start();} 
 
 
if ($API_NUM==7) {
	
	 $itemsPerPage = 10;  
	 if (isset($_GET['itemsPerPage'])) { $itemsPerPage = $_GET['itemsPerPage']; }
	 $page = 1;
	 if (isset($_GET['page'])) { $page = $_GET['page'];}	 
	 $start_record_number = ($page-1)*$itemsPerPage;
	
	/////////////////   검색  start ////////////////////////////////////////////////////////////////////////
	// 검색어
	 $add_condition     =""; $searchType = ""; 	  $keyword="";
	 
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL";  $keyword = $_GET['keyword'];
		
		$search_add = " and (( item_name like '%".$_GET['keyword']."%'  ) ";
		$search_add = $search_add." or ( warehouse_name like '%".$_GET['keyword']."%'  ) ";
		$search_add = $search_add." or ( angle_name like '%".$_GET['keyword']."%'  )) ";		
		
        $add_condition = $search_add;
		
	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];  $keyword = $_GET['keyword'];
		$search_add = " and ".$searchType." like '%".$_GET['keyword']."%' ";	
        $add_condition = $search_add;
	}

    // 날짜 종류 선택 (출고예정일 or 출고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 출고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
	}else{ // 검색을 했으면,
		//$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
	}

	
	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$search_add = $search_add." and date(s.rdate)  between '". $searchStartDate."' and  '". $searchEndDate."' ";	 
 	   }else{
			//$add_condition = $add_condition." and rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 
   
    $api7_add_condition = ""; 
    $api7_add_condition = $add_condition; 
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////	 
} 
 
if ($API_NUM==9) {
	
	 $itemsPerPage = 10;  
	 if (isset($_GET['itemsPerPage'])) { $itemsPerPage = $_GET['itemsPerPage']; }
	 $page = 1;
	 if (isset($_GET['page'])) { $page = $_GET['page'];}	 
	 $start_record_number = ($page-1)*$itemsPerPage;
	
	/////////////////   검색  start ////////////////////////////////////////////////////////////////////////
	  // 출고상태 
	  $searchStoreStatus = ""; 
	  if (($_GET['searchStoreStatus']=="")||($_GET['searchStoreStatus']=="ALL")) {
		  $searchStoreStatus = "ALL"; 
		  $add_condition     ="";
	  }else{
		  $searchStoreStatus = $_GET['searchStoreStatus']; 
		  $add_condition=" and state = ".$_GET['searchStoreStatus'];
	  }		

	// 검색어
	  $searchType = ""; 
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL"; 

			$add_condition = $add_condition." WHERE (( SELECT warehouse_name FROM wms_warehouses WHERE warehouse_id = i.warehouse_id ) like '%".$_GET['keyword']."%'  ";	
			$add_condition = $add_condition." or ( SELECT angle_name FROM wms_angle WHERE angle_id = i.angle_id ) like '%".$_GET['keyword']."%' ";	
			$add_condition = $add_condition." or ( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%".$_GET['keyword']."%' ";	
			$add_condition = $add_condition." or  item_name like '%".$_GET['keyword']."%')";			
		
	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];
		
 		if ($searchType=="company_name") {
			$add_condition = $add_condition." WHERE ( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%".$_GET['keyword']."%' ";	
		}
 		if ($searchType=="warehouse_name") {
			$add_condition = $add_condition." WHERE ( SELECT  warehouse_name FROM wms_warehouses WHERE warehouse_id = i.warehouse_id  ) like '%".$_GET['keyword']."%' ";	
		}
 		if ($searchType=="angle_name") {
			$add_condition = $add_condition." WHERE ( SELECT  angle_name FROM wms_angle WHERE angle_id = i.angle_id  ) like '%".$_GET['keyword']."%' ";	
		}
		if ($searchType=="item_name") {
			$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
		}
	}

    // 날짜 종류 선택 (출고예정일 or 출고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 출고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
		//$add_condition = $add_condition." and plan_date = ";
	}else{ // 검색을 했으면,
		$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
	}

	
	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$add_condition = $add_condition." and plan_date  between '". $searchStartDate."' and  '". $searchEndDate."' ";	   
	   }else{
			$add_condition = $add_condition." and rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 
    $api9_add_condition = ""; 
    $api9_add_condition = $add_condition; 
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////	
 	if ($searchType=="item_name") { 
		$list_condition  = " wms_outbound i  JOIN wms_items p  on p.item_id = i.product_id ";
		$list_condition  = $list_condition." where 1=1 ".$add_condition." and  i.delYN = 'N'";
	}
 	if ($searchType=="warehouse_name") { 
		$list_condition  = " wms_outbound i  JOIN wms_items p  on p.item_id = i.product_id  ";
		$list_condition = $list_condition." JOIN  wms_warehouses w ON w.warehouse_id = i.warehouse_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N'";
	}
 	if ($searchType=="angle_name") { 
		$list_condition  = " wms_outbound i  JOIN wms_items p  on p.item_id = i.product_id  ";
		$list_condition = $list_condition." JOIN  wms_angle a ON a.angle_id = i.angle_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N'";
	}
 	if (($searchType=="")||($searchType=="ALL")) { 
		$list_condition  = " wms_outbound i  JOIN wms_items p  on p.item_id = i.product_id  ";
		$list_condition = $list_condition." JOIN  wms_warehouses w ON w.warehouse_id = i.warehouse_id ";		
		$list_condition = $list_condition." JOIN  wms_angle a ON a.angle_id = i.angle_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N'";
	}		
}
 if ($API_NUM==10) {
	 
	 $itemsPerPage = 10;  
	 if (isset($_GET['itemsPerPage'])) { $itemsPerPage = $_GET['itemsPerPage']; }
	 $page = 1;
	 if (isset($_GET['page'])) { $page = $_GET['page'];}
	 $start_record_number = ($page-1)*$itemsPerPage;

	/////////////////   검색  start ////////////////////////////////////////////////////////////////////////
	  // 입고상태 
	  $searchStoreStatus = ""; 
	  if (($_GET['searchStoreStatus']=="")||($_GET['searchStoreStatus']=="ALL")) {
		  $searchStoreStatus = "ALL"; 
		  $add_condition     ="";
	  }else{
		  $searchStoreStatus = $_GET['searchStoreStatus']; 
		  $add_condition=" and state = ".$_GET['searchStoreStatus'];
	  }		


	// 검색어
	  $searchType = ""; 
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL"; 
			$add_condition = $add_condition." WHERE (( SELECT warehouse_name FROM wms_warehouses WHERE warehouse_id = i.warehouse_id ) like '%".$_GET['keyword']."%'  ";	
			$add_condition = $add_condition." or ( SELECT angle_name FROM wms_angle WHERE angle_id = i.angle_id ) like '%".$_GET['keyword']."%' ";	
			$add_condition = $add_condition." or  item_name like '%".$_GET['keyword']."%')";
		

	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];
	
 		if ($searchType=="warehouse_name") {
			$add_condition = $add_condition." WHERE ( SELECT  warehouse_name FROM wms_warehouses WHERE warehouse_id = i.warehouse_id  ) like '%".$_GET['keyword']."%' ";	
		}
 		if ($searchType=="angle_name") {
			$add_condition = $add_condition." WHERE ( SELECT  angle_name FROM wms_angle WHERE angle_id = i.angle_id  ) like '%".$_GET['keyword']."%' ";	
		}
		if ($searchType=="item_name") {
			$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
		}
	}


    // 날짜 종류 선택 (입고예정일 or 입고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 입고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
		//$add_condition = $add_condition." and plan_date = ";
	}else{ // 검색을 했으면,
		$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
	}

	
	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$add_condition = $add_condition." and plan_date  between '". $searchStartDate."' and  '". $searchEndDate."' ";	   
	   }else{
			$add_condition = $add_condition." and rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 
   $api10_add_condition = ""; 
   $api10_add_condition = $add_condition;
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////
 	if ($searchType=="item_name") { 
		$list_condition  = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id ";
		$list_condition  = $list_condition." where 1=1 ".$add_condition." and  i.delYN = 'N'";
	}
 	if ($searchType=="warehouse_name") { 
		$list_condition  = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id  ";
		$list_condition = $list_condition." JOIN  wms_warehouses w ON w.warehouse_id = i.warehouse_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N'";
	}
 	if ($searchType=="angle_name") { 
		$list_condition  = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id  ";
		$list_condition = $list_condition." JOIN  wms_angle a ON a.angle_id = i.angle_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N'";
	}
 	if (($searchType=="")||($searchType=="ALL")) { 
		$list_condition  = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id  ";
		$list_condition = $list_condition." JOIN  wms_warehouses w ON w.warehouse_id = i.warehouse_id ";		
		$list_condition = $list_condition." JOIN  wms_angle a ON a.angle_id = i.angle_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N'";
	}	 	 
 }
 
 if ($API_NUM==14) {
	 
	 $itemsPerPage = 10;  
	 if (isset($_GET['itemsPerPage'])) { $itemsPerPage = $_GET['itemsPerPage']; }
	 $page = 1;
	 if (isset($_GET['page'])) { $page = $_GET['page'];}
	 $start_record_number = ($page-1)*$itemsPerPage;

	/////////////////   검색  start ////////////////////////////////////////////////////////////////////////
	  // 입고상태 
	  $searchStoreStatus = ""; 
	  if (($_GET['searchStoreStatus']=="")||($_GET['searchStoreStatus']=="ALL")) {
		  $searchStoreStatus = "ALL"; 
		  $add_condition     ="";
	  }else{
		  $searchStoreStatus = $_GET['searchStoreStatus']; 
		  $add_condition=" and state = ".$_GET['searchStoreStatus'];
	  }		

	// 검색어
	  $searchType = ""; 
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL"; 
			$add_condition = $add_condition." and (( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%".$_GET['keyword']."%' ";	
			$add_condition = $add_condition." or  item_name like '%".$_GET['keyword']."%')";
		
	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];
		
 		if ($searchType=="company_name") {
			$add_condition = $add_condition." WHERE ( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%".$_GET['keyword']."%' ";	
		}
		if ($searchType=="item_name") {
			$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
		}
	}

    // 날짜 종류 선택 (입고예정일 or 입고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 입고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
		//$add_condition = $add_condition." and plan_date = ";
	}else{ // 검색을 했으면,
		$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
	}

	
	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$add_condition = $add_condition." and plan_date  between '". $searchStartDate."' and  '". $searchEndDate."' ";	   
	   }else{
			$add_condition = $add_condition." and rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 
   $api14_add_condition = ""; 
   $api14_add_condition = $add_condition;
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////
 	if ($searchType=="item_name") { 
		$list_condition = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id where 1=1 ".$add_condition." and  i.delYN = 'N'";
	}
 	if (($searchType=="company_name") || (($searchType=="")||($searchType=="ALL"))) { 
		$list_condition = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id  ".$add_condition." and  i.delYN = 'N'";
	}	 	 
 }
 
 
 if ($API_NUM==15) {
	
	 $itemsPerPage = 10;  
	 if (isset($_GET['itemsPerPage'])) { $itemsPerPage = $_GET['itemsPerPage']; }
	 $page = 1;
	 if (isset($_GET['page'])) { $page = $_GET['page'];}	 
	 $start_record_number = ($page-1)*$itemsPerPage;
	
	/////////////////   검색  start ////////////////////////////////////////////////////////////////////////
	  // 출고상태 
	  $searchStoreStatus = ""; 
	  if (($_GET['searchStoreStatus']=="")||($_GET['searchStoreStatus']=="ALL")) {
		  $searchStoreStatus = "ALL"; 
		  $add_condition     ="";
	  }else{
		  $searchStoreStatus = $_GET['searchStoreStatus']; 
		  $add_condition=" and state = ".$_GET['searchStoreStatus'];
	  }		

	// 검색어
	  $searchType = ""; 
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL"; 
			$add_condition = $add_condition."  and  (( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%".$_GET['keyword']."%' ";	
			$add_condition = $add_condition." or  item_name like '%".$_GET['keyword']."%')";
		
	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];
		
 		if ($searchType=="company_name") {
			$add_condition = $add_condition." WHERE ( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%".$_GET['keyword']."%' ";	
		}
		if ($searchType=="item_name") {
			$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
		}
	}

    // 날짜 종류 선택 (출고예정일 or 출고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 출고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
		//$add_condition = $add_condition." and plan_date = ";
	}else{ // 검색을 했으면,
		$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
	}

	
	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$add_condition = $add_condition." and plan_date  between '". $searchStartDate."' and  '". $searchEndDate."' ";	   
	   }else{
			$add_condition = $add_condition." and rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 
    $api15_add_condition = ""; 
    $api15_add_condition = $add_condition; 
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////	 
}
  
// 토큰을 사용하여 작업 수행
// 예: 토큰을 검증하고 사용자 인증을 확인하는 코드를 여기에 작성
 
if (($token != 0)&&($_SESSION['user_id']!="")) { //
	
}
  

$cnt_i = 0; // 중복 실행 방지

if ($cnt_i < 1) {
	
	$cnt_i = 1;
	
	// API_NUM에 따라 작업 분기 
	switch ($API_NUM) {
		case 1 : 
			// 로그인 API 처리
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 로그인 작업을 수행할 수 있습니다.		
			$user_id = $_POST['user_id'];
			$user_pw = $_POST['user_pw']; 
		 
			$userManager->loginUser2($user_id, $user_pw);
			
			break;
		case 2 : 
			// 창고 코드 자동생성 API 처리		
			$code = $_POST['warehouse_code'];
			$name = $_POST['warehouse_name'];	
			api2_create_warehouse_code();
			break;
		case 3 : 
			// 창고 등록 API 처리
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 창고 등록 작업을 수행할 수 있습니다.		
			$code = $_POST['warehouse_code'];
			$name = $_POST['warehouse_name'];	
			api3_addWarehouse($code, $name);
			break;
		case 4 : 
			// 창고 단건삭제 API 처리
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 작업을 수행할 수 있습니다.		
			$code = $_POST['warehouse_id'];
			api4_del_warehouse($code);
			break;
		case 5 : 
			// 창고 목록 API 처리
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 창고 등록 작업을 수행할 수 있습니다.		
			api5_getwms_warehouses($search,$SearchString);
			break;
		case 6 : 
			// 제품 목록 API 처리
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 제품목로 조회 작업을 수행할 수 있습니다.		
			api6_getwms_items_list($start_record_number,$itemsPerPage,$search,$keyword,$searchStartDate,$searchEndDate);
			break;
		case 7 : 
			// 재고 목록 API 처리(창고 통합)
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 창고 재고조회 작업을 수행할 수 있습니다.		
			api7_getStock_all($start_record_number,$itemsPerPage,$search_add);
			break;
		case 8 : 
			// 요청 처리
			handleJsonRequest();
			// 재고이동 
            api8_from_angle_moveto_angle_Stock($stock_id,$to_ware,$to_angle,$to_cnt);
			break;
		case 9 : 
			// 출고지시목록API 처리 
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 출고지시 목록조회 수행할 수 있습니다.		
			api9_getwms_outbounds($start_record_number,$itemsPerPage,$search,$api9_add_condition);
			break;
		case 10 : 
			// 입고지시목록API 처리 
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 입고지시 목록조회 수행할 수 있습니다.		
			api10_getwms_inbounds($start_record_number,$itemsPerPage,$search,$api10_add_condition);
			break;
		case 11 : 
			// 입고지시 입고API 처리 
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 입고를 수행할 수 있습니다.		
			//$inbound_id = $_POST['inbound_id'];
			//$inbound_quantity = $_POST['inbound_quantity'];				
			//api11_update_inbound($inbound_id,$inbound_quantity) ;
			
			$body = file_get_contents('php://input');
			$input_data = json_decode($body, true);

			if ($input_data) {
				$inbound_id = $input_data['inbound_id'] ?? null;
				$inbound_quantity = $input_data['inbound_quantity'] ?? null;
				api11_update_inbound($inbound_id, $inbound_quantity);
			} else {
				echo json_encode(['error' => 'Invalid request body']);
				http_response_code(400);
			}			

			break;
		case 12 : 
			// 출고지시 출고API 처리 
			// 전달받은 JSON 데이터 파싱 및 처리
			$body = file_get_contents('php://input');
			$input_data = json_decode($body, true);

			if ($input_data) {
				$outbound_id = $input_data['outbound_id'] ?? null;
				$outbound_quantity = $input_data['outbound_quantity'] ?? null;
				api12_update_outbound($outbound_id, $outbound_quantity);
			} else {
				echo json_encode(['error' => 'Invalid request body']);
				http_response_code(400);
			}
			break;
		case 13 : 
			// 입고지시 입고복수API 처리 
			//$inbound_data = $_POST['inbound_data'];		
			api13_update_inbound() ;
			break;
		case 14 : 
			// 입고지시목록API 1날짜 처리 
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 입고지시 목록조회 수행할 수 있습니다.		
			api14_getwms_inbounds($start_record_number,$itemsPerPage,$search,$api14_add_condition);
			break;
		case 15 : 
			// 출고지시목록API 1날짜 처리 
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 출고지시 목록조회 수행할 수 있습니다.		
			api15_getwms_outbounds($start_record_number,$itemsPerPage,$search,$api15_add_condition);
			break;
		case 16 : 
			// 출고지시 출고복수API 처리 
			api16_update_outbound();
			break;
		case 17 : 
			// 창고 목록 API 처리
			// 이 부분에서 $token을 사용하여 사용자 인증을 확인하고 창고 등록 작업을 수행할 수 있습니다.		
			api17_getwms_warehouses_angles();
			break;
			
			
		default :
		   // 처리할 수 없는 API_NUM이 전달된 경우		
			break;
	} 
}
 
function user_id_to_user_name($user_id){
    global $conn;
    $stmt = $conn->query("SELECT user_name FROM wms_user where user_id = '".$user_id."'");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}
  
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 창고관리 * //





//////// 숫자는 숫자로
function convertToNumbers(&$array) {
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            convertToNumbers($value);
        } elseif (is_numeric($value)) {
            $value = $value + 0; // 숫자 값으로 변환
        }
    }
}



// 토큰 유효성 검사 
function ck_token_cnt($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(user_id),'0') as cnt FROM `wms_user` WHERE  delYN = 'N'  and user_token = '".$token."'");
    $stmt->execute(); 
    $token_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $token_cnt;
}


// 토큰 유효성 검사 
function ck_token_user($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT user_id, user_name FROM `wms_user` WHERE  delYN = 'N'  AND user_token = '".$token."'");
   // $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute(); 
    $token_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $token_user;
}


function token_auth(){ // Authorization 헤더에서 토큰 값을 가져옴

    // HTTP_AUTHORIZATION 헤더를 확인하여 토큰을 가져옴
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
    } elseif (function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        echo 'Neither apache_request_headers nor getallheaders functions are available.';
        return 'Header functions not available.';
    }

    // 헤더 이름을 소문자로 변환하여 비교
    $headers = array_change_key_case($headers, CASE_LOWER);

    // 'authorization' 헤더가 있는지 확인하고 출력
    if (isset($headers['authorization'])) {
        $authorizationHeader = $headers['authorization'];
        $token = $authorizationHeader;
    }

	return $token;
}
 

function token_auth22222() {
    $token = null;
    $headers = [];

    // getallheaders() 또는 apache_request_headers() 사용
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
    }


    // $_SERVER를 사용하여 Authorization 헤더 확인
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
       // $headers['Authorization'] = "MTY4NjkxNjE0NjQ4M0l1ZW55Q2dsR3A=";
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        //$headers['Authorization'] = "MTY4NjkxNjE0NjQ4M0l1ZW55Q2dsR3A=";
    }

    // 디버그: 모든 헤더 출력
   // echo "<pre>";
   // print_r($headers);
   // echo "</pre>";

    if (isset($headers['Authorization'])) {
        $authorizationHeader = $headers['Authorization'];
        $token = $authorizationHeader;
    } else {
        $token = 'Authorization Header not found.';
    }

    return $token;
}

// 창고 추가
function api3_addWarehouse($code, $name) {

	$token = token_auth();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

    if ($tk_cnt[0]['cnt'] > 0) {  
		// 토큰이 유효함. 정상진행
		global $conn;
		$date = date("Y-m-d H:i:s");
		$stmt = $conn->prepare("INSERT INTO wms_warehouses (warehouse_code, warehouse_name, warehouse_rdate) VALUES (:code, :name, :rdate)");
		$stmt->bindParam(':code', $code);
		$stmt->bindParam(':name', $name);
		$stmt->bindParam(':rdate',$date);
		
		
		// 데이터베이스 작업 실행 및 에러 처리
		if (!$stmt->execute()) {

			http_response_code(500); // 서버 오류 상태 코드
			exit(json_encode(["error" => "Database error"], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
		}else{

			// 히스토리추가	
			//add_history('A','창고를 등록',$name,'');		
				$response_arr = [
					"header" => [
						"resultCode" => 200,
						"codeName" => "SUCCESS"
					],
					"body" => [
						"data" => null
					]
				];	
					
					header('Content-Type: application/json');		
					echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
					
				exit();		
		}		
		
    }else{ // 토큰이 없음 	(토큰값이 다름) 토큰에 해당하는 사용자가 없습니다.
		api_error102();
    }

} 
 
function api2_create_warehouse_code(){

	$token = token_auth();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상
	
		$result = getwms_warehouse_last1();

		// 특정 데이터 1개 추출
		if (!empty($result)) {
			$specificData = $result[0]['warehouse_id'];  
			//echo $specificData;
			$specificData = $specificData + 1000;
			$specificData = "W".$specificData;
		} else {
			//echo "No data found.";
			$specificData = "W1000";
		}

    if ($tk_cnt[0]['cnt'] > 0) {  
			$response_arr = ["header" => ["resultCode" => 200,"codeName" => "SUCCESS"],
				             "body"   => ["warehouse_code" => $specificData]
			];
		header('Content-Type: application/json');		
		//echo json_encode($response, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);			
		echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);			
    }else{ // 토큰이 없음(안맞음)
		api_error102();	
    }
}
 

// 창고 삭제
function api4_del_warehouse($warehouse_id) {
 
	$token = token_auth();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

		
    global $conn;

    try {
        // 트랜잭션 시작
        $conn->beginTransaction();

        // wms_angle 업데이트
        $stmt0 = $conn->prepare("UPDATE wms_angle SET delYN = 'Y', angle_use = 'N' WHERE warehouse_id = :warehouse_id AND delYN = 'N'");
        $stmt0->bindParam(':warehouse_id', $warehouse_id);
        $stmt0->execute();
        $angle_rows_updated = $stmt0->rowCount();

        // wms_warehouses 업데이트
        $stmt = $conn->prepare("UPDATE wms_warehouses SET delYN = 'Y' WHERE warehouse_id = :warehouse_id AND delYN = 'N'");
        $stmt->bindParam(':warehouse_id', $warehouse_id);
        $stmt->execute();
        $warehouse_rows_updated = $stmt->rowCount();

        //if ($angle_rows_updated > 0 || $warehouse_rows_updated > 0) {
        if ($warehouse_rows_updated > 0) {
            // 창고 이름 가져오기
            $warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);

            // 히스토리 추가
            add_history('A', '창고를 삭제', $warehouse_name[0]['warehouse_name'], $warehouse_name);

            // 트랜잭션 커밋
            $conn->commit();

			if ($tk_cnt[0]['cnt'] > 0) {  
				
				// 성공적인 JSON 응답
				header('Content-Type: application/json');
				echo json_encode([
					'header' => [
						'resultCode' => 200,
						'codeName' => 'SUCCESS'
					],
					'body' => [
						'data' => [
							'warehouse_id' => $warehouse_id,
						],
						'message' => '창고가 성공적으로 삭제되었습니다.'						
					]
				]);
			}else{ // 토큰이 없음(안맞음)
				api_error102();	
			}		
        } else {
            // 트랜잭션 롤백
            $conn->rollBack();

            // 삭제 대상이 없음을 알리는 JSON 응답
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 404,
                    'codeName' => 'NOT_FOUND'
                ],
                'body' => [
						'data' => [
							'warehouse_id' => $warehouse_id,
						],
						'message' => '삭제할 창고가 없습니다.'		
                ]
            ]);
        }
    } catch (Exception $e) {
        // 트랜잭션 롤백
        $conn->rollBack();

        // 오류 JSON 응답
        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 500,
                'codeName' => 'ERROR'
            ],
            'body' => [
                'message' => '창고 삭제 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ]
        ]);
    }
} 
 
 
function api5_getwms_warehouses($search, $SearchString) {
 
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행
        global $conn;

        // 검색조건
        $condition_sql = "";
        $params = [];
        if (!empty($SearchString) && !empty($search)) {
            $condition_sql = " WHERE $search LIKE :searchString";
            $params[':searchString'] = '%' . $SearchString . '%';
        } else {
            $condition_sql = " WHERE 1=1";
        }

		
        $query = " ";
        $query = $query."    SELECT ";
        $query = $query."     warehouse_id, "; 
        $query = $query."    warehouse_code, "; 
        $query = $query."     warehouse_name,  ";
        $query = $query."      warehouse_rdate,  ";
        $query = $query."     IFNULL(( ";
        $query = $query."     SELECT SUM(IFNULL(( ";
        $query = $query."        SELECT SUM(quantity)  ";
        $query = $query."       FROM wms_stock  ";
        $query = $query."      WHERE warehouse_id = a.warehouse_id  ";
        $query = $query."      AND angle_id = a.angle_id ";
        $query = $query."      ), 0)) ";
        $query = $query."     FROM wms_angle a  ";
        $query = $query."      WHERE a.delYN = 'N'  ";
        $query = $query."       AND a.warehouse_id = w.warehouse_id ";
        $query = $query."     ), 0) AS sum_quantity_warehouse,  ";
        $query = $query."     (SELECT COUNT(angle_id)  ";
        $query = $query."     FROM wms_angle  ";
        $query = $query."      WHERE angle_id <> 0  ";
        $query = $query."     AND warehouse_id = w.warehouse_id  ";
        $query = $query."     AND delYN = 'N') AS angle_cnt  ";
        $query = $query."     FROM wms_warehouses w ";
        $query = $query.$condition_sql; 
        $query = $query."     AND w.warehouse_id <> 0  ";
        $query = $query."      AND w.delYN = 'N' ";
 

        try {
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
        
            // Fetching the results
            $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Success JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [ "data" => $warehouses ]
            ]);

        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ]);
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }
}



// 제품 목록 가져오기
function api6_getwms_items_list($start_record_number, $itemsPerPage, $search, $keyword, $searchStartDate, $searchEndDate) {
	
    $token = token_auth();
	//echo $token; exit();
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행	
	
    global $conn;

		// 기본 SQL 쿼리와 조건 초기화
			$base_sql = "  ";
			$base_sql = $base_sql."  SELECT   ";
			$base_sql = $base_sql."   c.cate_expose AS item_expose,   ";
			$base_sql = $base_sql."   c.cate_name AS item_cate,   ";
			$base_sql = $base_sql."    i.item_id AS item_id,   ";
			$base_sql = $base_sql."   i.item_code AS item_code,   ";
			$base_sql = $base_sql."    i.item_name AS item_name,   ";
			$base_sql = $base_sql."    LEFT(i.item_rdate, 10) AS item_rdate,   ";
			$base_sql = $base_sql."    i.item_cate AS item_cate_num,   ";
			$base_sql = $base_sql."    IFNULL((SELECT SUM(quantity) FROM wms_stock WHERE delYN = 'N' AND item_id = i.item_id), 0) AS sum_quantity_item  ";
			$base_sql = $base_sql."    FROM   ";
			$base_sql = $base_sql."    wms_items AS i  ";
			$base_sql = $base_sql."     LEFT JOIN   ";
			$base_sql = $base_sql."     wms_cate AS c ON i.item_cate = c.cate_id  ";
			$base_sql = $base_sql."      WHERE   ";
			$base_sql = $base_sql."       i.delYN = 'N'  ";

		$conditions = [];
		$params = [];

		// 검색 조건 추가
		if (!empty($keyword)) {
			if ($search == "ALL") {
				$conditions[] = "(i.item_name LIKE :keyword OR i.item_cate LIKE :keyword)";
				$params[':keyword'] = '%' . $keyword . '%';
			} else {
				$conditions[] = "i." . $search . " LIKE :keyword";
				$params[':keyword'] = '%' . $keyword . '%';
			}
		}

		if (!empty($searchStartDate) && !empty($searchEndDate)) {
			$conditions[] = "i.item_rdate BETWEEN :start_date AND :end_date";
			$params[':start_date'] = $searchStartDate;
			$params[':end_date'] = $searchEndDate;
		}

		// 조건을 SQL 쿼리에 추가
		if (count($conditions) > 0) {
			$base_sql .= ' AND ' . implode(' AND ', $conditions);
		}

		// 정렬 및 페이징 처리
		$base_sql .= " ORDER BY i.item_rdate DESC"; // LIMIT :start_record_number, :itemsPerPage";
 

        try {
            $stmt = $conn->prepare($base_sql);
            $stmt->execute($params);
        
            // Fetching the results
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

             // 숫자로 변환
			convertToNumbers($resources);
			
            // Success JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [ "data" => $resources ]
            ]);

        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ]);
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }

}


  
// 현재 재고 상태 가져오기 
function api7_getStock_all($start_record_number,$itemsPerPage,$search_add) {
	
   $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2

   if ($result_setting[0]['set_state']=="N") {
	   $add_sql = " and quantity > 0"; 
   }else{
	   $add_sql = " "; 
   }
	
	$token = token_auth();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상
		
    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행	
		global $conn;

	   $sql=" SELECT ";
	   $sql=$sql." s.*,  item_name, "; 
	   $sql=$sql." IFNULL(w.warehouse_id, '/') AS warehouse_id_null,  ";
	   $sql=$sql." IFNULL(w.warehouse_id, '0') AS warehouse_id,  ";
	   $sql=$sql." IFNULL(w.warehouse_name, '배정안됨') AS warehouse_name,  ";
	   $sql=$sql." a.angle_name AS angle_name   ";
	   $sql=$sql." FROM  ";
	   $sql=$sql."  `wms_stock` AS s  ";
	   $sql=$sql." LEFT JOIN  ";
	   $sql=$sql." `wms_items` AS i  ";
	   $sql=$sql." ON s.item_id = i.item_id  ";
	   $sql=$sql." LEFT JOIN  ";
	   $sql=$sql."  `wms_warehouses` AS w  ";
	   $sql=$sql."  ON s.warehouse_id = w.warehouse_id   ";
	   $sql=$sql." JOIN  ";
	   $sql=$sql."  `wms_angle` AS a  ";
	   $sql=$sql."  ON s.angle_id = a.angle_id  ";
	   $sql=$sql." WHERE  ";
	   $sql=$sql."  w.delYN = 'N'  ";
	   $sql=$sql."  AND a.delYN = 'N'  ";
	   //$sql=$sql."  AND w.warehouse_id <> 0     "; 
	   $sql=$sql.$add_sql;
	   $sql=$sql.$search_add;
	   $sql=$sql."  order by s.rdate desc, s.item_id  limit $start_record_number,$itemsPerPage ";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        
            // Fetching the results
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
             // 숫자로 변환
			convertToNumbers($resources);

            // Success JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [ "data" => $resources ]
            ]);

        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ]);
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }	 
		//$stmt = $conn->query($sql);
		//return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 

// 출고지시 목록 가져오기
function api9_getwms_outbounds($start_record_number,$itemsPerPage,$search,$api9_add_condition) {
	//$SearchString = " WHERE (( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%%' or item_name like '%%')";
	$token = token_auth();
	
	//echo $token;exit();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상
		
    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행		   
		global $conn;
	  
		$sql = "	SELECT ";
		$sql = $sql."	    i.outbound_id as outbound_id, p.item_name AS item_name, p.item_code AS item_code, ";
		$sql = $sql."	    w.warehouse_name AS warehouse_name, ";
		$sql = $sql."	    ( ";
		$sql = $sql."	       SELECT angle_name  ";
		$sql = $sql."	        FROM wms_angle  ";
		$sql = $sql."	        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
		$sql = $sql."	    ) AS angle_name, ";
			
		$sql = $sql."	    (  ";
		$sql = $sql."	       SELECT cate_name  ";
		$sql = $sql."	       from wms_company  ";
		$sql = $sql."	       where cate_id = i.company_id  ";
		$sql = $sql."	    ) as company_name,  ";
	   // $sql = $sql."	    i.company_id,  ";
		$sql = $sql."	    i.planned_quantity,  ";
		$sql = $sql."	   i.outbound_quantity,  ";
		$sql = $sql."	    i.plan_date,  ";
		$sql = $sql."	    i.rdate,  ";
		$sql = $sql."	    i.state, ";
		
		$sql = $sql."	     COALESCE(( ";
		$sql = $sql."	       SELECT quantity ";
		$sql = $sql."	       from wms_stock ";
		$sql = $sql."	       where item_id = i.product_id AND warehouse_id = i.warehouse_id AND angle_id = i.angle_id ";
		$sql = $sql."	     ), 0)  as stock_quantity ";
	 
		$sql = $sql."	FROM  ";
		$sql = $sql."	    wms_outbound i   ";
		$sql = $sql."	JOIN  ";
		$sql = $sql."	    wms_items p ON p.item_id = i.product_id ";
		$sql = $sql."	JOIN  ";
		$sql = $sql."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
		$sql = $sql.$api9_add_condition;	
		$sql = $sql." and i.delYN = 'N' order by i.plan_date desc, i.outbound_id desc limit $start_record_number,$itemsPerPage ";
	  
		$sqlcnt = "	SELECT count(*) as total_count FROM  ";
		$sqlcnt = $sqlcnt."	    wms_outbound i   ";
		$sqlcnt = $sqlcnt."	JOIN  ";
		$sqlcnt = $sqlcnt."	    wms_items p ON p.item_id = i.product_id ";
		$sqlcnt = $sqlcnt."	JOIN  ";
		$sqlcnt = $sqlcnt."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
		$sqlcnt = $sqlcnt.$api9_add_condition;	
		$sqlcnt = $sqlcnt." and i.delYN = 'N'"; // limit $start_record_number,$itemsPerPage ";


        try {
            $stmt_totcnt = $conn->prepare($sqlcnt);
            $stmt_totcnt->execute(); 		
            $rst_cnt = $stmt_totcnt->fetch(PDO::FETCH_ASSOC);
            $total_count = $rst_cnt['total_count'];

            $stmt = $conn->prepare($sql);
            $stmt->execute();
        
            // Fetching the results
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
            // 숫자로 변환
            convertToNumbers($resources);
			

            // Success JSON response
            header('Content-Type: application/json');
            if ($total_count == 0) {
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS_ZERO'
                    ],
                    'body' => [ 
                        'data' => $resources,
                        'total_count' => $total_count					
                    ]
                ]);				
            } else {
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS'
                    ],
                    'body' => [ 
                        'data' => $resources,
                        'total_count' => $total_count					
                    ]
                ]);					
            }
        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ]);
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }			
}



// 출고지시 목록 날짜 가져오기
function api15_getwms_outbounds($start_record_number,$itemsPerPage,$search,$api15_add_condition) {
	//$SearchString = " WHERE (( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%%' or item_name like '%%')";
	$token = token_auth();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상
		
    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행		   
		global $conn;
	  
		$sql = "	SELECT i.plan_date, COUNT(*) AS cnt"; 
		$sql = $sql."	FROM  ";
		$sql = $sql."	    wms_outbound i   ";
		$sql = $sql."	JOIN  ";
		$sql = $sql."	    wms_items p ON p.item_id = i.product_id ";
		$sql = $sql."	JOIN  ";
		$sql = $sql."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
		$sql = $sql." WHERE i.delYN = 'N' ";  
		$sql = $sql.$api15_add_condition;	
		$sql = $sql."GROUP BY i.plan_date ";
		$sql = $sql."ORDER BY i.plan_date asc, ";
		$sql = $sql."         i.outbound_id asc ";
		$sql = $sql." limit $start_record_number,$itemsPerPage ";
		
		//echo $sql; exit();
 
		$sqlcnt = "	SELECT count(*) as total_count FROM  ";
		$sqlcnt = $sqlcnt."	    wms_outbound i   ";
		$sqlcnt = $sqlcnt."	JOIN  ";
		$sqlcnt = $sqlcnt."	    wms_items p ON p.item_id = i.product_id ";
		$sqlcnt = $sqlcnt."	JOIN  ";
		$sqlcnt = $sqlcnt."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";
		$sqlcnt = $sqlcnt." and i.delYN = 'N' ";						
		$sqlcnt = $sqlcnt.$api15_add_condition;	

        try {
            $stmt_totcnt = $conn->prepare($sqlcnt);
			$stmt_totcnt->execute(); 		
			$rst_cnt = $stmt_totcnt->fetchAll(PDO::FETCH_ASSOC);
			
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        
            // Fetching the results
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 숫자로 변환
			convertToNumbers($resources);
			
            // Success JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [ "data" => $resources						
				]
            ]);

        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ]);
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }			
		//$stmt = $conn->query($sql);	
		//$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//return $result; 
}


// 입고지시 목록 날짜 가져오기
function api14_getwms_inbounds($start_record_number,$itemsPerPage,$search,$api14_add_condition) {
	//$SearchString = "WHERE (( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%%' or item_name like '%%')";
 
	$token = token_auth();	// 클라이언트의 토큰을 불러온다.
    //api_error_test($token); exit();
	
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상
	
	//$test = $tk_cnt[0]['cnt'];
	//api_error_test($test); exit();
 		
    if ($tk_cnt[0]['cnt'] > 0) {

        // 토큰이 유효함. 정상진행		   
		global $conn;
		 
		$sql = "	SELECT i.plan_date, COUNT(*) AS cnt"; 
		$sql = $sql."	FROM  ";
		$sql = $sql."	    wms_inbound i   ";
		$sql = $sql."	JOIN  ";
		$sql = $sql."	    wms_items p ON p.item_id = i.product_id ";
		$sql = $sql."	JOIN  ";
		$sql = $sql."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
		$sql = $sql."WHERE i.delYN = 'N' ";
		$sql = $sql.$api14_add_condition;	
		$sql = $sql."GROUP BY i.plan_date ";
		$sql = $sql."ORDER BY i.plan_date asc, ";
		$sql = $sql."         i.inbound_id asc ";
		$sql = $sql." limit $start_record_number,$itemsPerPage ";
 
  
		$sqlcnt = "	SELECT count(*) as total_count FROM  ";
		$sqlcnt = $sqlcnt."	    wms_inbound i   ";
		$sqlcnt = $sqlcnt."	JOIN  ";
		$sqlcnt = $sqlcnt."	    wms_items p ON p.item_id = i.product_id ";
		$sqlcnt = $sqlcnt."	JOIN  ";
		$sqlcnt = $sqlcnt."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
		$sqlcnt = $sqlcnt." and i.delYN = 'N' ";		
		$sqlcnt = $sqlcnt.$api14_add_condition;	
       // limit $start_record_number,$itemsPerPage ";		
 
        try {
			//echo $sqlcnt;
			//exit();
            $stmt_totcnt = $conn->prepare($sqlcnt);
			$stmt_totcnt->execute(); 		
			$rst_cnt = $stmt_totcnt->fetchAll(PDO::FETCH_ASSOC);
			
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        
            // Fetching the results
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
            // 숫자로 변환
			convertToNumbers($resources);
			
            // Success JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [ "data" => $resources				
				]
            ]);
 
        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ]);
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }			
		//$stmt = $conn->query($sql);	
		//$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//return $result; 
}
  



// 입고지시 목록 가져오기
function api10_getwms_inbounds($start_record_number,$itemsPerPage,$search,$api10_add_condition) {
	//$SearchString = "WHERE (( SELECT cate_name FROM wms_company WHERE cate_id = i.company_id ) like '%%' or item_name like '%%')";

	$token = token_auth();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

			
    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행		   
		global $conn;
		 
		$sql = "	SELECT ";
		$sql = $sql."	    i.inbound_id as inbound_id, p.item_name AS item_name, p.item_code AS item_code,  ";
		$sql = $sql."	    w.warehouse_name AS warehouse_name, ";
		$sql = $sql."	    ( ";
		$sql = $sql."	       SELECT angle_name  ";
		$sql = $sql."	        FROM wms_angle  ";
		$sql = $sql."	        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
		$sql = $sql."	    ) AS angle_name, ";
			
		$sql = $sql."	    (  ";
		$sql = $sql."	       SELECT cate_name  ";
		$sql = $sql."	       from wms_company  ";
		$sql = $sql."	       where cate_id = i.company_id  ";
		$sql = $sql."	    ) as company_name,  ";
	   // $sql = $sql."	    i.company_id,  ";
		$sql = $sql."	    i.planned_quantity,  ";
		$sql = $sql."	   i.inbound_quantity,  ";
		$sql = $sql."	    i.plan_date,  ";
		$sql = $sql."	    i.rdate,  ";
		$sql = $sql."	    i.state ";
		$sql = $sql."	FROM  ";
		$sql = $sql."	    wms_inbound i   ";
		$sql = $sql."	JOIN  ";
		$sql = $sql."	    wms_items p ON p.item_id = i.product_id ";
		$sql = $sql."	JOIN  ";
		$sql = $sql."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
	   // $sql = $sql." WHERE  (  SELECT cate_name FROM wms_company  WHERE cate_id = i.company_id  )  like '%B%'	";	
		$sql = $sql.$api10_add_condition;	
		$sql = $sql." and i.delYN = 'N' order by i.plan_date desc, i.inbound_id desc limit $start_record_number,$itemsPerPage ";

        //echo $sql; exit();

		$sqlcnt = "	SELECT count(*) as total_count FROM  ";
		$sqlcnt = $sqlcnt."	    wms_inbound i   ";
		$sqlcnt = $sqlcnt."	JOIN  ";
		$sqlcnt = $sqlcnt."	    wms_items p ON p.item_id = i.product_id ";
		$sqlcnt = $sqlcnt."	JOIN  ";
		$sqlcnt = $sqlcnt."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
	   // $sqlcnt = $sqlcnt." WHERE  (  SELECT cate_name FROM wms_company  WHERE cate_id = i.company_id  )  like '%B%'	";	
		$sqlcnt = $sqlcnt.$api10_add_condition;	
		$sqlcnt = $sqlcnt." and i.delYN = 'N' ";// limit $start_record_number,$itemsPerPage ";		
		

        try {
            $stmt_totcnt = $conn->prepare($sqlcnt);
            $stmt_totcnt->execute(); 		
            $rst_cnt = $stmt_totcnt->fetch(PDO::FETCH_ASSOC);
            $total_count = $rst_cnt['total_count'];

            $stmt = $conn->prepare($sql);
            $stmt->execute();
        
            // Fetching the results
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
            // 숫자로 변환
            convertToNumbers($resources);

            // Success JSON response
            header('Content-Type: application/json');
            if ($total_count == 0) {
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS_ZERO'
                    ],
                    'body' => [ 
                        'data' => $resources,
                        'total_count' => $total_count					
                    ]
                ]);				
            } else {
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS'
                    ],
                    'body' => [ 
                        'data' => $resources,
                        'total_count' => $total_count					
                    ]
                ]);					
            }
        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ]);
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }	
}
  



 // 인바운드 입고수량 업데이트  
function api11_update_inbound($inbound_id,$inbound_quantity) {
		
	$token = token_auth();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상
		
    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행		   
		global $conn;
		// 1. 입력받은 파라미터 $inbound_id의 유효성을 wms_inbound 테이블로부터 검사한다.
		$stmt = $conn->prepare("SELECT planned_quantity, inbound_quantity FROM wms_inbound WHERE inbound_id = :inbound_id");
		$stmt->bindParam(':inbound_id', $inbound_id);
		$stmt->execute();
		$inbound_data = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!$inbound_data) {
			//throw new Exception("Invalid inbound_id");
			api_error103();  // 유효하지 않은 ID
			
		}elseif (!is_numeric($inbound_quantity) ) { // 2. $inbound_quantity가 유효한 범위의 숫자인지 검사한다.
			api_error104();  // 유효하지 않은 수량
			
		}else{ 
			// 3. $inbound_quantity를 업데이트하였을때 planned_quantity 값보다 크지 않아야 유효한 범위로, 수량 업데이트 한다.
			$new_inbound_quantity = $inbound_data['inbound_quantity'] + $inbound_quantity;
            
            if ($new_inbound_quantity < 0) {
                api_error111();  // 입고 재고수량이 0보다 작아질 수 없음
				exit();
            } elseif ($new_inbound_quantity > $inbound_data['planned_quantity']) {
                api_error105();  // 예정수량을 초과
				exit();
            } else { // 정상 범위
				
				// 정상 입고진행 !!
				//4. 수량 업데이트시, planned_quantity와 inbound_quantity가 같게 되면, state를 1로 변경하여 입고완료처리 한다.
				$sql = "UPDATE wms_inbound SET inbound_quantity = :inbound_quantity, rdate = CASE WHEN :inbound_quantity = planned_quantity THEN CURDATE() ELSE rdate END, state = CASE WHEN :inbound_quantity = planned_quantity THEN 1 ELSE state END WHERE inbound_id = :inbound_id";
 
				try {					
					$stmt = $conn->prepare($sql);
					$stmt->bindParam(':inbound_id', $inbound_id);
					$stmt->bindParam(':inbound_quantity', $new_inbound_quantity);
					$stmt->execute();				
                 
				    api_ok200(); // 정상입력완료
					
					if ($new_inbound_quantity == $inbound_data['planned_quantity']) { // 위 update문에서 state 가 1로 바뀔때만, 이 if문 실행
						
						//2 stock 실제 입고처리 준비로, item 가져오기
						$result2 = get_wms_inbound_item($inbound_id);	
						
						// 2-1 해당 제품이 없으면, insert,  있으면 update
						$item_id		= $result2[0]['product_id'];
						$to_ware	    = $result2[0]['warehouse_id'];
						$to_angle		= $result2[0]['angle_id'];
						$qua			= $result2[0]['inbound_quantity'];
						$step			= 'N';
						
						// 실제 입고처리
						addStock($item_id,$to_ware,$to_angle,$qua,$step); // step = Y 면 앵글에 보관 / 아니면 N					
				  	}
				} catch (PDOException $e) {
					error_log('Query Error: ' . $e->getMessage());
					header('Content-Type: application/json');
					echo json_encode([
						'header' => [
							'resultCode' => 500,
							'codeName' => 'INTERNAL_SERVER_ERROR',
							'message' => $e->getMessage()
						]
					]);
				}
			}		
		}
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }				
 	
}				


function count_outbound_ids() {
    $outbound_ids = [];

    // Loop through all POST parameters
    foreach ($_POST as $key => $value) {
        // Check if the parameter starts with 'outbound_id_' and is numeric
        if (strpos($key, 'outbound_id_') === 0 && is_numeric($value)) {
            $outbound_ids[] = $value;
        }
    }

    // Return the count of outbound_ids found
    return count($outbound_ids);
}


// 출고지시 복수 수량 업데이트
function api16_update_outbound() {
    global $conn;
    $token = token_auth();  // 토큰 인증 함수
    $tk_cnt = ck_token_cnt($token);  // 토큰 유효성 검사 함수

    if ($tk_cnt == 0) {
        echo json_encode(['error' => 'Invalid token']);
        http_response_code(401);
        return;
    }

    // JSON 데이터로부터 출고 데이터 가져오기
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Invalid JSON input']);
        http_response_code(400);
        return;
    }

    if (!isset($data['outbound_data']) || !is_array($data['outbound_data'])) {
        echo json_encode(['error' => 'Invalid outbound data provided']);
        http_response_code(400);
        return;
    }

    $outbound_data = $data['outbound_data'];
    if (empty($outbound_data)) {
        echo json_encode(['error' => 'No outbound data provided']);
        http_response_code(400);
        return;
    }

    try {
        $conn->beginTransaction();  // 트랜잭션 시작

        foreach ($outbound_data as $item) {
            $outbound_id = $item['outbound_id'];
            $outbound_quantity = $item['outbound_quantity'];

            if ($outbound_id === null || $outbound_quantity === null) {
                $conn->rollBack();
                api_error106('Invalid outbound data for one entry.');
                return;
            }

            // outbound_id의 유효성 검사
            $stmt = $conn->prepare("SELECT planned_quantity, outbound_quantity FROM wms_outbound WHERE outbound_id = :outbound_id");
            $stmt->bindParam(':outbound_id', $outbound_id);
            $stmt->execute();
            $outbound_entry = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$outbound_entry) {
                $conn->rollBack();
                api_error106('유효하지 않은 ID', $outbound_id);  // 에러 함수 호출
                return;
            } elseif (!is_numeric($outbound_quantity)) {
                $conn->rollBack();
                api_error106('유효하지 않은 수량', $outbound_id);  // 에러 함수 호출
                return;
            }

            $new_outbound_quantity = $outbound_entry['outbound_quantity'] + $outbound_quantity;
            if ($new_outbound_quantity > $outbound_entry['planned_quantity']) {
                $conn->rollBack();
                api_error106('예정 수량 초과', $outbound_id);  // 에러 함수 호출
                return;
            }elseif($new_outbound_quantity < 0){
                api_error111();  // 고 재고수량이 0보다 작아질 수 없음           
                return;				
            }

            // 수량 및 상태 업데이트
            $sql = "UPDATE wms_outbound 
                    SET outbound_quantity = :outbound_quantity, 
                        rdate = CASE WHEN :outbound_quantity = planned_quantity THEN CURDATE() ELSE rdate END, 
                        state = CASE WHEN :outbound_quantity = planned_quantity THEN 1 ELSE state END 
                    WHERE outbound_id = :outbound_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':outbound_id', $outbound_id);
            $stmt->bindParam(':outbound_quantity', $new_outbound_quantity);
            $stmt->execute();

            if ($new_outbound_quantity == $outbound_entry['planned_quantity']) {
                // 제품 상세 정보 가져와서 재고 업데이트
                $result2 = get_wms_outbound_item($outbound_id);
                if ($result2) {
                    $item_id = $result2[0]['product_id'];
                    $from_ware = $result2[0]['warehouse_id'];
                    $from_angle = $result2[0]['angle_id'];
                    $qua = $result2[0]['outbound_quantity'];
                    $company_id = $result2[0]['company_id'];
                    $step = 'N';

                    add_outStock($item_id, $from_ware, $from_angle, $qua, $step, $company_id);  // 재고 업데이트 함수
                }
            }
        }

        $conn->commit();  // 트랜잭션 커밋

        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 200,
                'codeName' => 'SUCCESS',
                'message' => '정상 출고완료'
            ]
        ]);            
    } catch (PDOException $e) {
        $conn->rollBack();  // 트랜잭션 롤백
        error_log('Query Error: ' . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 500,
                'codeName' => 'INTERNAL_SERVER_ERROR',
                'message' => $e->getMessage()
            ]
        ]);
        return;
    }
}


// 아웃바운드 출고수량 업데이트  
function api12_update_outbound($outbound_id, $outbound_quantity) {
 	
    // 토큰 인증	
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행
 		global $conn;
        // 1. 입력받은 파라미터 $outbound_id의 유효성을 wms_outbound 테이블로부터 검사한다.
        $stmt = $conn->prepare("SELECT planned_quantity, outbound_quantity FROM wms_outbound WHERE outbound_id = :outbound_id");
        $stmt->bindParam(':outbound_id', $outbound_id);
        $stmt->execute();
        $outbound_data = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$outbound_data) {
            api_error103();  // 유효하지 않은 ID
        }elseif (!is_numeric($outbound_quantity)) {
            api_error104();  // 유효하지 않은 수량
        } else {
            // 수량 업데이트시, planned_quantity와 outbound_quantity가 같게 되면, state를 1로 변경하여 출고완료처리
            $new_outbound_quantity = $outbound_data['outbound_quantity'] + $outbound_quantity;
			
			// 출고 재고수량이 0보다 작아질 수 없음
            if ($new_outbound_quantity < 0) {
                api_error111();  // 출고 재고수량이 0보다 작아질 수 없음
				exit();
            } elseif ($new_outbound_quantity > $outbound_data['planned_quantity']) {
                api_error115();  // 예정수량을 초과
				exit();
            } else {  // 정상 범위
                // 정상 출고진행
                $sql = "UPDATE wms_outbound SET outbound_quantity = :outbound_quantity, rdate = CASE WHEN :outbound_quantity = planned_quantity THEN CURDATE() ELSE rdate END, state = CASE WHEN :outbound_quantity = planned_quantity THEN 1 ELSE state END WHERE outbound_id = :outbound_id";

                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':outbound_id', $outbound_id);
                    $stmt->bindParam(':outbound_quantity', $new_outbound_quantity);
                    $stmt->execute();

                    api_ok200(); // 정상출력완료

                    if ($new_outbound_quantity == $outbound_data['planned_quantity']) {
                        // 출고 완료 상태로 변경된 경우
                        // stock 실제 출고처리 준비로, item 가져오기
                        $result2 = get_wms_outbound_item($outbound_id);

                        // 해당 제품의 수량을 업데이트 (감소) 시킨다.
                        $item_id = $result2[0]['product_id'];
                        $from_ware = $result2[0]['warehouse_id'];
                        $from_angle = $result2[0]['angle_id'];
                        $qua = $result2[0]['outbound_quantity'];
                        $company_id = $result2[0]['company_id'];
                        $step = 'N';

                        // 실제 출고처리
                        add_outStock($item_id, $from_ware, $from_angle, $qua, $step, $company_id); // step = Y 면 앵글에서 제거 / 아니면 N
                    }
                } catch (PDOException $e) {
                    error_log('Query Error: ' . $e->getMessage());
                    header('Content-Type: application/json');
                    echo json_encode([
                        'header' => [
                            'resultCode' => 500,
                            'codeName' => 'INTERNAL_SERVER_ERROR',
                            'message' => $e->getMessage()
                        ]
                    ]);
                }
            }
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }
}




function count_inbound_ids() {
    $inbound_ids = [];

    // Loop through all POST parameters
    foreach ($_POST as $key => $value) {
        // Check if the parameter starts with 'inbound_id_' and is numeric
        if (strpos($key, 'inbound_id_') === 0 && is_numeric($value)) {
            $inbound_ids[] = $value;
        }
    }

    // Return the count of inbound_ids found
    return count($inbound_ids);
}


// 인바운드 입고복수 수량 업데이트  
function api13_update_inbound() {
    global $conn;
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);  // 토큰 유효성 검사
    
    $multi_ok = "false"; // 모두 true 이어야, commit 처리함.
    $msg_is = "비정상";
 
    // JSON 데이터 가져오기
    $input = json_decode(file_get_contents('php://input'), true);
    $inbound_data = $input['inbound_data'];

    if (empty($inbound_data)) {
        echo json_encode(['error' => 'Invalid inbound data provided']);
        http_response_code(400);
        return;
    }

    try {
        $conn->beginTransaction();

        foreach ($inbound_data as $item) {
            $multi_ok = "false";
            $inbound_id = $item['inbound_id'];
            $inbound_quantity = $item['inbound_quantity'];

            if ($inbound_id === null || $inbound_quantity === null) {
                echo 'Invalid inbound data for one entry.' . "\n";
                $msg_is = "Invalid inbound data for one entry.";
                continue;
            }

            // inbound_id의 유효성 검사
            $stmt = $conn->prepare("SELECT planned_quantity, inbound_quantity FROM wms_inbound WHERE inbound_id = :inbound_id");
            $stmt->bindParam(':inbound_id', $inbound_id);
            $stmt->execute();
            $inbound_entry = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$inbound_entry) {
                $msg_is = "유효하지 않은 ID";
                $conn->rollBack();
                api_error106($msg_is,$inbound_id);  // 에러                
                return;
            } elseif (!is_numeric($inbound_quantity)) {
                $msg_is = "유효하지 않은 수량";
                $conn->rollBack();
                api_error106($msg_is,$inbound_id);  // 에러                
                return;
            }

            $new_inbound_quantity = $inbound_entry['inbound_quantity'] + $inbound_quantity;
			
            if ($new_inbound_quantity > $inbound_entry['planned_quantity']) {
                $msg_is = "예정 수량 초과";
                $conn->rollBack();
                api_error106($msg_is,$inbound_id);  // 에러                
                return;
            }elseif($new_inbound_quantity < 0){
                api_error111();  // 입고 재고수량이 0보다 작아질 수 없음           
                return;				
            }

            // 수량 및 상태 업데이트
            $sql = "UPDATE wms_inbound 
                    SET inbound_quantity = :inbound_quantity, 
                        rdate = CASE WHEN :inbound_quantity = planned_quantity THEN CURDATE() ELSE rdate END, 
                        state = CASE WHEN :inbound_quantity = planned_quantity THEN 1 ELSE state END 
                    WHERE inbound_id = :inbound_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':inbound_id', $inbound_id);
            $stmt->bindParam(':inbound_quantity', $new_inbound_quantity);
            $stmt->execute();

            //api_ok200();  // 정상 입력 완료
            $multi_ok = "true";
            $msg_is = "정상 입고완료";

            if ($new_inbound_quantity == $inbound_entry['planned_quantity']) {
                // 제품 상세 정보 가져와서 재고 업데이트
                $result2 = get_wms_inbound_item($inbound_id);
                if ($result2) {
                    $item_id = $result2[0]['product_id'];
                    $to_ware = $result2[0]['warehouse_id'];
                    $to_angle = $result2[0]['angle_id'];
                    $qua = $result2[0]['inbound_quantity'];
                    $step = 'N';

                    addStock($item_id, $to_ware, $to_angle, $qua, $step);
                }
            }
        }

        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log('Query Error: ' . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 500,
                'codeName' => 'INTERNAL_SERVER_ERROR',
                'message' => $e->getMessage()
            ]
        ]);
    }
    
    if ($multi_ok == "true") {
        //api_ok200();  // 정상 입력 완료
        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 200,
                'codeName' => 'SUCCESS',
                'message' => $msg_is
            ]
        ]);         
    }else{
        //api_error106();  // 에러    
        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 106,
                'codeName' => 'ERROR',
                'message' => $msg_is
            ]
        ]);         
    }    
}      


// JSON 데이터를 받아서 재고 이동 처리
function handleJsonRequest() {
    global $conn;		
    $conn = getDbConnection();
    if ($conn === null) {
        return jsonResponse(500, 'Database connection error');
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['API_NUM']) || $data['API_NUM'] != 8) {
        return jsonResponse(400, 'Invalid API_NUM');
    }

    $requiredFields = [
        'stock_id',
        'to_ware_id',
        'to_angle_id',
        'to_cnt'
    ];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            return jsonResponse(400, "Missing field: $field");
        }
    }

    $stock_id = $data['stock_id'];
    $to_ware = $data['to_ware_id'];
    $to_angle = $data['to_angle_id'];
    $to_cnt = $data['to_cnt'];

    $result = api8_from_angle_moveto_angle_Stock($stock_id,$to_ware,$to_angle,$to_cnt);

    if ($result) {
        return jsonResponse(200, 'SUCCESS');
    } else {
        return jsonResponse(500, 'Internal Server Error');
    }
}

// JSON 응답을 생성하는 함수
function jsonResponse($statusCode, $message) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'header' => [
            'resultCode' => $statusCode,
            'codeName' => $message
        ],
        'body' => null
    ]);
    exit;
}

 

// 재고이동 (앵글로 이동)
function api8_from_angle_moveto_angle_Stock($stock_id,$to_ware,$to_angle,$to_cnt) {
	
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);  // 토큰 유효성 검사
 
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
            api_error107();  exit();
        }		
		
        if (($current_stock['quantity'] - $to_cnt) < 0) {
            api_error108(); exit();
        }

        if ($to_cnt < 0) {
            api_error110(); exit();
        }

        // 변수 대입, 업데이트 시킬 카운트 계산
        $from_warehouse_id = $current_stock['warehouse_id'];
        $from_angle_id     = $current_stock['angle_id'];
        $from_cnt          = $current_stock['quantity']-$to_cnt;  // 업데이트로 인한, 이전 창고 재고감소
        $item_id           = $current_stock['item_id'];

        if (($to_ware==$from_warehouse_id)&&($to_angle==$from_angle_id)) {  // 동일 창고, 앵글
            api_error109();  exit();
        }		

		
        // 원래창고 재고 감소
        $stmt_decrease = $conn->prepare("UPDATE wms_stock SET quantity = :quantity WHERE item_id = :item_id AND warehouse_id = :from_warehouse_id and angle_id = :from_angle_id ");
        $stmt_decrease->bindParam(':quantity', $from_cnt);
        $stmt_decrease->bindParam(':item_id', $item_id);
        $stmt_decrease->bindParam(':from_warehouse_id', $from_warehouse_id);
        $stmt_decrease->bindParam(':from_angle_id', $from_angle_id);
        $stmt_decrease->execute();

        // 대상창고에 재고를 증가 또는 업데이트
        $stmt_increase = $conn->prepare("INSERT INTO wms_stock (item_id, warehouse_id, angle_id, quantity , rdate) VALUES (:item_id, :to_warehouse_id, :to_angle_id, :quantity, :rdate) ON DUPLICATE KEY UPDATE quantity = quantity + :quantity "); // 
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
    }  

}
 
 
 
 
 
function api17_getwms_warehouses_angles() {
 
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행
        global $conn;

        // 검색조건
        $condition_sql = "";  $condition_sql = " WHERE 1=1";
        $params = [];
        //if (!empty($SearchString) && !empty($search)) {
        //    $condition_sql = " WHERE $search LIKE :searchString";
        //    $params[':searchString'] = '%' . $SearchString . '%';
        //} else {
        //    $condition_sql = " WHERE 1=1";
        //}

		
        $query = " ";
        $query = $query."    SELECT  ";
        $query = $query."        w.warehouse_id,  ";
        $query = $query."        w.warehouse_name,  ";

        $query = $query."        (SELECT COUNT(angle_id)  ";
        $query = $query."         FROM wms_angle  ";
        $query = $query."         WHERE angle_id <> 0  ";
        $query = $query."         AND warehouse_id = w.warehouse_id  ";
        $query = $query."         AND delYN = 'N' ";
        $query = $query."        ) AS angle_cnt, ";
        $query = $query."        a.angle_id, ";
        $query = $query."        a.angle_name ";
        $query = $query."    FROM  ";
        $query = $query."        wms_warehouses w ";
        $query = $query."    JOIN  ";
        $query = $query."        wms_angle a ON w.warehouse_id = a.warehouse_id ";
        $query = $query."    WHERE  ";
        $query = $query."        w.warehouse_id <> 0  ";
        $query = $query."        AND w.delYN = 'N'  ";
        $query = $query."        AND a.delYN = 'N' ";
        $query = $query."    ORDER BY  ";
        $query = $query."        w.warehouse_id, a.angle_id; ";
 

        try {
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
        
            // Fetching the results
            $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Success JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [ "data" => $warehouses ]
            ]);

        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ]);
        }
    } else {
        // 토큰값이 다름: 토큰에 해당하는 사용자가 없습니다.
        api_error102();
    }
}
 
 
/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 function api_error_test($token) {
	$response_arr = [
		"header" => [
			"resultCode" => 999,
			"codeName" => "error_test"
		],
		"body" => [
			"data" => $token
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);					 
 }
 

 function api_error101() { 
	$response_arr = [
		"header" => [
			"resultCode" => 101,
			"codeName" => "NOT_FOUND_TOKEN"
		],
		"body" => [
			"data" => null,  "msg" => "토큰값이 없습니다."
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 function api_error102() {
	$response_arr = [
		"header" => [
			"resultCode" => 102,
			"codeName" => "NOT_MATCH_TOKEN"
		],
		"body" => [
			"data" => null
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);					 
 }
 
 
 function api_error103() { 
	$response_arr = [
		"header" => [
			"resultCode" => 103,
			"codeName" => "NOT_FOUND_ID"
		],
		"body" => [
			"data" => null,  "msg" => "입력한 ID값이 없습니다."
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 function api_error104() { 
	$response_arr = [
		"header" => [
			"resultCode" => 104,
			"codeName" => "Invalid number"
		],
		"body" => [
			"data" => null,  "msg" => "올바르지 않는 숫자입니다."
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 function api_error105() { 
	$response_arr = [
		"header" => [
			"resultCode" => 105,
			"codeName" => "Invalid number"
		],
		"body" => [
			"data" => null,  "msg" => "입고수량이 예정수량을 초과합니다."
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 function api_error115() { 
	$response_arr = [
		"header" => [
			"resultCode" => 115,
			"codeName" => "Invalid number"
		],
		"body" => [
			"data" => null,  "msg" => "출고수량이 예정수량을 초과합니다."
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 function api_error106($msg,$inbound_id) { 
	$response_arr = [
		"header" => [
			"resultCode" => 106,
			"codeName" => "ERROR"
		],
		"body" => [
			"msg" => "ID:".$inbound_id." ERROR:".$msg
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }

 function api_error106_typejson($msg,$outbound_id = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'header' => [
            'resultCode' => 106,
            'codeName' => 'ERROR',
            'message' => $message,
            'outbound_id' => $outbound_id
        ]
    ]);
    http_response_code(400);
}

 
 function api_error107() { 
	$response_arr = [
		"header" => [
			"resultCode" => 107,
			"codeName" => "현재 재고수량 오류"
		],
		"body" => [
			"data" => null,  "msg" => "현재 재고수량 확인하시기 바랍니다."
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 function api_error108() { 
	$response_arr = [
		"header" => [
			"resultCode" => 108,
			"codeName" => "재고이동 수량초과"
		],
		"body" => [
			"data" => null,  "msg" => "이동하려는 수량이 재고수량을 초과하였습니다. 한도내에 하십시오"
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 function api_error109() { 
	$response_arr = [
		"header" => [
			"resultCode" => 109,
			"codeName" => "동일 창고,앵글"
		],
		"body" => [
			"data" => null,  "msg" => "이동 대상이 동일합니다. 창고 앵글을 다시 선택하세요"
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 
 function api_error110() { 
	$response_arr = [
		"header" => [
			"resultCode" => 110,
			"codeName" => "양수만 입력가능합니다."
		],
		"body" => [
			"data" => null,  "msg" => "음수를 입력하셨습니다. 다시 입력하세요"
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 
 function api_error111() { 
	$response_arr = [
		"header" => [
			"resultCode" => 111,
			"codeName" => "재고는 0이상만 가능합니다."
		],
		"body" => [
			"data" => null,  "msg" => "다시 입력하세요"
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 function api_error205() { 
	$response_arr = [
		"header" => [
			"resultCode" => 205,
			"codeName" => "Invalid number"
		],
		"body" => [
			"data" => null,  "msg" => "출고수량이 예정수량을 초과합니다."
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }

 function api_ok200() { 
	$response_arr = [
		"header" => [
			"resultCode" => 200,
			"codeName" => "SUCCESS"
		],
		"body" => [
			"data" => null,  "msg" => "정상 입력완료되었습니다."
		]
	];	
		header('Content-Type: application/json');echo json_encode($response_arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); 
 }
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 /////////////////////////////////////    이하 관리자모드 ///////////////////////////////////////////////////////

class adminManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 사용자 등록
    public function registerUser($admin_id, $admin_pw, $admin_role) {
        $admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO wms_admin (admin_id, admin_pw, admin_role) VALUES (:admin_id, :admin_pw, :admin_role)");
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':admin_pw', $admin_pw);
        $stmt->bindParam(':admin_role', $admin_role);
        $stmt->execute();
    }

    // 사용자 로그인
    public function loginUser($admin_id, $admin_pw) {
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
			
			add_history('A','로그인 성공',$_SESSION['admin_role'],'');        
			return true;    
			
        } else {
			$admin_name_search = admin_id_to_admin_name($admin_id);			
			add_history('A','로그인 실패',$admin_name_search[0]['admin_name'],$admin_id);			
            return false;
        }
    }

    // 사용자 로그아웃
    public function logoutUser() {				
        session_start();
		add_history('A','로그아웃',$_SESSION['admin_role'],'');					
		
        session_unset();
        session_destroy();
    }

    // 사용자 권한 확인
    public function checkUserRole() {
        session_start();
        if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
            return false; // 사용자가 로그인하지 않았음
        }

        $stmt = $this->conn->prepare("SELECT admin_role FROM wms_admin WHERE admin_id = :admin_id");
        $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        $stmt->execute();  //$userRole = $stmt->fetchColumn();
       
        $userRole = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        return $userRole; // === $requiredRole;
    }
	
}

$adminManager = new adminManager($conn);
 
 
 
 
 
 
function admin_id_to_admin_name($admin_id){
    global $conn;
    $stmt = $conn->query("SELECT admin_name FROM wms_admin where admin_id = '".$admin_id."'");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}
 

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * HOME * //
 
 //  history information
function get_history_personal($start_record_number,$itemsPerPage,$add_condition,$admin_id,$admin_role) {
    global $conn;
	
	// 검색조건
	$condition_sql = "";
	if ($SearchString!="") {
	//	$condition_sql = " and  ".$search." like '%".$SearchString."%' ";	
	}else{
	//	$condition_sql = "";	
	}	
	if ($admin_role<91) {
		$stmt = $conn->query("SELECT * FROM wms_history where 1=1  ".$add_condition." and h_id = '".$admin_id."' order by h_date desc limit $start_record_number,$itemsPerPage");
	}else{
		$stmt = $conn->query("SELECT * FROM wms_history where 1=1  ".$add_condition."  order by h_date desc limit $start_record_number,$itemsPerPage");
	}
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  

 //  history information
function get_history_item_list_cate_personal($item,$h_loc_code,$admin_id) {
    global $conn;
    $stmt = $conn->query("SELECT DISTINCT $item, h_loc_code, h_location FROM wms_history where h_id = '".$admin_id."' and h_loc_code ='".$h_loc_code."' order by $item asc");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 창고관리 * //
// 창고 추가
function addWarehouse($code, $name) {
    global $conn;
	$date = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO wms_warehouses (warehouse_code, warehouse_name, warehouse_rdate) VALUES (:code, :name, :rdate)");
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':rdate',$date);
    $stmt->execute();
	
	
	// 히스토리추가	
	add_history('A','창고를 등록',$name,'');			
	
    //$stmt_history = $conn->prepare("INSERT INTO wms_history (h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$name."','','창고를 등록','m02')");
   // $stmt_history->execute();
}

// 특정창고 가져오기
function getwms_warehouse_name($warehouse_id) {
    global $conn;
 
    $stmt = $conn->query("SELECT IFNULL(max(warehouse_id), '0') as warehouse_id FROM wms_warehouses where delYN = 'N'  and  warehouse_id = ".$warehouse_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 특정창고 1가지 조회 
function select_warehouse_one($warehouse_id) {
    global $conn;
    $stmt = $conn->query("SELECT warehouse_name FROM wms_warehouses where  delYN = 'N'  and warehouse_id = $warehouse_id limit 0,1");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

 // 앵글테이블안에 창고ID가 있는지 확인, 삭제가능한 창고인지 조회 0 이면, 창고삭제가능
function stock_warehouse_count($warehouse_id) {
    global $conn;
    $stmt = $conn->query("SELECT count(*) as count FROM wms_angle where angle_use = 'Y' and delYN = 'N' and warehouse_id = '".$warehouse_id."'");
    $result= $stmt->fetch(PDO::FETCH_ASSOC);
	return $result['count'];
}   

 // 앵글삽입시 앵글명 중복검사  
function ck_angle_cnt($warehouse_id,$angle_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(angle_id),'0') as angle_cnt FROM `wms_angle` WHERE  delYN = 'N'  and warehouse_id = '".$warehouse_id."'  and angle_name = '".$angle_name."'");
    $stmt->execute(); 
    $angle_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $angle_cnt;
}

 // 창고 삭제 
function del_warehouse($warehouse_id,$angle_name) {
    global $conn;
    $stmt0 = $conn->prepare("update wms_angle set delYN = 'Y', angle_use = 'N' where warehouse_id = $warehouse_id and  delYN = 'N'");
    $stmt0->bindParam(':warehouse_id', $warehouse_id);
    $stmt0->execute();	
	
    $stmt = $conn->prepare("update wms_warehouses set  delYN = 'Y' where warehouse_id = $warehouse_id");
   // $stmt = $conn->prepare("delete from wms_warehouses where warehouse_id = $warehouse_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->execute();	
	
	$warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
	add_history('A','창고를 삭제',$warehouse_name[0]['warehouse_name'],$warehouse_name);	
}

// 창고앵글 삽입시, ID값 +1 하기전 최대값추출
function getwms_max_angle($warehouse_id) {
    global $conn;
 
    $stmt = $conn->query("SELECT IFNULL(max(angle_id), '0') as angle_id  FROM wms_angle");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 정렬순서 변경up
function order_up_angle($warehouse_id,$angle_id,$angle_order){
    global $conn;
    $stmt = $conn->prepare("update wms_angle as a1 join ( select angle_id, min(angle_order) + 1 as change_order from wms_angle where delYN = 'N' and    warehouse_id = :warehouse_id and angle_order > :angle_order ) as a2 on a1.angle_id = :angle_id set a1.angle_order = a2.change_order");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id',$angle_id);
    $stmt->bindParam(':angle_order',$angle_order);
    $stmt->execute();		
}

// 정렬순서 변경down
function order_down_angle($warehouse_id,$angle_id,$angle_order){
    global $conn;
    $stmt = $conn->prepare("update wms_angle as a1 join ( select angle_id, max(angle_order) - 1 as change_order from wms_angle where delYN = 'N' and  warehouse_id = :warehouse_id and angle_order < :angle_order ) as a2 on a1.angle_id = :angle_id set a1.angle_order = a2.change_order");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id',$angle_id);
    $stmt->bindParam(':angle_order',$angle_order);
    $stmt->execute();		
}

// 최근생성창고ID 가져오기
function getwms_warehouse_last1() {
    global $conn;
    $stmt = $conn->query("SELECT IFNULL(max(warehouse_id),1) as warehouse_id  FROM wms_warehouses");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 최근생성제품ID 가져오기, 코드명 부여
function getwms_item_last1() {
    global $conn;
    $stmt = $conn->query("SELECT IFNULL(item_id, '0') item_id FROM wms_items order by item_rdate desc LIMIT 1");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 앵글등록 
// Add angle
function add_angle($warehouse_id, $angle_name) {
    global $conn;
	$date = date("Y-m-d H:i:s");	
    $stmt = $conn->prepare("INSERT INTO wms_angle (angle_name, warehouse_id,angle_rdate) VALUES (:angle_name, :warehouse_id, :angle_rdate)");
    $stmt->bindParam(':angle_name', $angle_name);
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_rdate',$date);
    $stmt->execute();	
	
	
	$warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
	
	add_history('A','앵글을 삽입',$warehouse_name[0]['warehouse_name'],$angle_name);
}

 // 앵글명 업데이트  
function update_angle($warehouse_id,$angle_id,$angle_name) {
    global $conn;
	
	$angle_name_before = angle_id_to_angle_name($angle_id);
	$warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
	
    $stmt = $conn->prepare("update wms_angle set angle_name = :angle_name, angle_rdate = :angle_rdate where warehouse_id = :warehouse_id and angle_id = :angle_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->bindParam(':angle_name', $angle_name);
    $stmt->bindParam(':angle_rdate',date("Y-m-d H:i:s"));
    $stmt->execute();

	add_history('B','앵글명을 변경',$warehouse_name[0]['warehouse_name']."창고내 앵글을 ".$angle_name_before[0]['angle_name'],$angle_name);			
	
}

 // 앵글 삭제 
function del_angle($warehouse_id,$angle_id,$angle_name) {
    global $conn;
   // $stmt = $conn->prepare("delete from wms_angle where warehouse_id = $warehouse_id and angle_id = $angle_id");
    $stmt = $conn->prepare("update wms_angle set delYN = 'Y', angle_use = 'N'  where warehouse_id = $warehouse_id and angle_id = $angle_id");
    $stmt->bindParam(':warehouse_id', $warehouse_id);
    $stmt->bindParam(':angle_id', $angle_id);
    $stmt->execute();	
	
	$warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
	
	add_history('A','앵글을 삭제',$warehouse_name[0]['warehouse_name'],$angle_name);
	
}

function warehouse_id_to_warehouse_name($warehouse_id){
    global $conn;
    $stmt = $conn->query("SELECT warehouse_name FROM wms_warehouses where warehouse_id = $warehouse_id");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}

function angle_id_to_angle_name($angle_id){
    global $conn;
    $stmt = $conn->query("SELECT angle_name FROM wms_angle where angle_id = $angle_id");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}


// 특정앵글 1가지 조회 
function select_angle_one($warehouse_id,$angle_id) {
    global $conn;
    $stmt = $conn->query("SELECT angle_name FROM wms_angle where delYN = 'N' and warehouse_id = $warehouse_id and angle_id = $angle_id limit 0,1");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

 // 앵글안에 제품이 있는지 확인, 삭제가능한 앵글인지 조회 0 이면, 앵글삭제가능
function stock_count($warehouse_id,$angle_id) {
    global $conn;
    $stmt = $conn->query("SELECT count(*) as count FROM wms_stock where warehouse_id = $warehouse_id and angle_id = $angle_id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
	//return $result['count'];
}

 // 특정앵글안에 제품들 합산 sum
function stock_sum($warehouse_id,$angle_id) {
    global $conn;
    $stmt = $conn->query("SELECT IFNULL(sum(quantity),0) as sum_quantity FROM wms_stock where warehouse_id = $warehouse_id and angle_id = $angle_id");
    $result= $stmt->fetch(PDO::FETCH_ASSOC);
	return $result['sum_quantity'];
}

 // 특정앵글안에 제품들 합산 sum
function stock_warehouse_name($warehouse_id) {
    global $conn;
    $stmt = $conn->query("SELECT warehouse_name FROM wms_warehouses where warehouse_id = $warehouse_id");
    $result= $stmt->fetch(PDO::FETCH_ASSOC);
	return $result['warehouse_name'];
}
  
 // 특정앵글안에 제품 리스트 (JSON수정)
function stock_list($warehouse_id,$angle_id) {
	
     $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2
	  
	   if ($result_setting[0]['set_state']=="N") {
		   $add_sql = "  and s.quantity > 0"; 
	   }else{
		   $add_sql = " "; 
	   }	
	   
    global $conn;
    $stmt = $conn->query("select i.item_id, i.item_name, (select cate_name from wms_cate where i.item_cate = cate_id ) as cate_name, (SELECT sum(quantity) as sum_quantity FROM wms_stock where warehouse_id = $warehouse_id and angle_id = $angle_id and item_id = i.item_id ) as item_cnt from wms_stock s left join wms_items i on  s.item_id = i.item_id  where s.warehouse_id = $warehouse_id and s.angle_id = $angle_id".$add_sql);
	
    $result->fetchAll(PDO::FETCH_ASSOC);
    //return $stmt->fetchAll(PDO::FETCH_ASSOC);
	  //json 타입의 결과값을 return합니다.
    header('Content-Type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}

// 재고관리 > 재고목록(창고밖) 
function getStock_00($warehouse_id,$angle_id,$start_record_number,$itemsPerPage,$search_add,$searchType,$keyword) {
	
     $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2
	  
	   if ($result_setting[0]['set_state']=="N") {
		   $add_sql = "  and s.quantity > 0"; 
	   }else{
		   $add_sql = " "; 
	   }	
	   
    global $conn;
	if ($searchType=="item_name") {
	$sql = "select i.item_id, i.item_name, (select cate_name from wms_cate where i.item_cate = cate_id ) as cate_name, (SELECT sum(quantity) as sum_quantity FROM wms_stock where warehouse_id = $warehouse_id and angle_id = $angle_id and item_id = i.item_id ) as item_cnt, s.rdate from wms_stock s left join wms_items i on  s.item_id = i.item_id  where s.warehouse_id = $warehouse_id and s.angle_id = $angle_id ".$add_sql.$search_add."  order by s.rdate desc, s.item_id limit  $start_record_number,$itemsPerPage ";
	}else{
	$sql = "select i.item_id, i.item_name, (select cate_name from wms_cate where i.item_cate = cate_id ) as cate_name, (SELECT sum(quantity) as sum_quantity FROM wms_stock where warehouse_id = $warehouse_id and angle_id = $angle_id and item_id = i.item_id ) as item_cnt, s.rdate from wms_stock s left join wms_items i on  s.item_id = i.item_id  where  (SELECT cate_name FROM wms_cate WHERE i.item_cate = cate_id ) like '%".$keyword."%' and s.warehouse_id = $warehouse_id and s.angle_id = $angle_id ".$add_sql.$search_add."  order by s.rdate desc, s.item_id limit  $start_record_number,$itemsPerPage ";
	}

    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


 // 제품카테고리 업데이트
function update_angle_order($angle_id,$order) {
    global $conn;
    $stmt = $conn->prepare("update wms_angle set angel_order = :angel_order where delYN = 'N' and angle_id = :angle_id");
    $stmt->bindParam(':angel_order', $angel_order);
    $stmt->bindParam(':angle_id',$angle_id);
    $stmt->execute();
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 제품관리 * //

// 제품 목록 가져오기
function getwms_items($start_record_number,$itemsPerPage,$search,$SearchString) {
    global $conn;
	
	// 검색조건
	$condition_sql = "";
	if ($SearchString!="") {
		$condition_sql = " and i.".$search." like '%".$SearchString."%' ";	
	}else{
		$condition_sql = "";	
	}
		
    $stmt = $conn->query("SELECT c.cate_expose item_expose, c.cate_name item_cate, i.item_id item_id, i.item_code item_code, i.item_name item_name, left(i.item_rdate,10) item_rdate, i.item_cate as item_cate_num, IFNULL((SELECT sum(quantity) as count FROM wms_stock where delYN = 'N' and item_id = i.item_id),0) as sum_quantity_item  FROM wms_items as i, wms_cate as c where  i.item_cate = c.cate_id and i.delYN = 'N'  ".$condition_sql."  order by item_rdate desc limit $start_record_number,$itemsPerPage");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $result; 
}


// 제품 목록 가져오기
function getwms_items_list($start_record_number,$itemsPerPage,$search,$keyword,$searchStartDate,$searchEndDate) {
    global $conn;
	
	// 검색조건
	$condition_sql = "";
	if ($keyword!="") {
		if ($search=="ALL") {
			$condition_sql =" and (i.item_name like '%".$keyword."%'  or i.item_cate like '%".$keyword."%' ) ";
		}else{
			$condition_sql = " and i.".$search." like '%".$keyword."%' ";	
		}
	}
	
	if($searchStartDate!=""){
		    $condition_sql = $condition_sql." and item_rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	 
	}
		
    $stmt = $conn->query("SELECT c.cate_expose item_expose, c.cate_name item_cate, i.item_id item_id, i.item_code item_code, i.item_name item_name, left(i.item_rdate,10) item_rdate, i.item_cate as item_cate_num, IFNULL((SELECT sum(quantity) as count FROM wms_stock where delYN = 'N' and item_id = i.item_id),0) as sum_quantity_item  FROM wms_items as i, wms_cate as c where  i.item_cate = c.cate_id and i.delYN = 'N'  ".$condition_sql."  order by item_rdate desc limit $start_record_number,$itemsPerPage");
  
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $result; 
}

// 제품관리 > 제품목록 분류변경
function m03_cate_change($cate_change,$item_cate,$item_id) {
    global $conn;
	
	$cate_name_before = cate_id_to_cate_name_from_item_id($item_id);	
	
    $stmt = $conn->prepare("update wms_items  set item_cate = '$item_cate' where item_id = '$item_id'");
    $stmt->execute();	
	
	
	$cate_name_after = cate_id_to_cate_name_from_item_id($item_id);	
	add_history('B','제품의 분류를 변경',$cate_name_before[0]['cate_name'],$cate_name_after[0]['cate_name']);			

}

function cate_id_to_cate_name_from_item_id($item_id){
    global $conn;
    $stmt = $conn->query("SELECT c.cate_name as cate_name FROM wms_cate c join wms_items i on c.cate_id = i.item_cate where i.item_id = '$item_id'");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}


// 바코드값 중복 검사 하여, 중복 카운트 안나오면 코드값 전달.
function duplicate_bar($code){
    global $conn;	
    $stmt = $conn->query("select count(*) as count from wms_items where item_code=$code");
    $result= $stmt->fetch(PDO::FETCH_ASSOC);
	
	if ($result['count']!=0) { 
		$code = $code + 1;
	    $code = duplicate_bar($code);
	}
	return $code;	
}

// 제품 추가
function addItem($code, $name, $item_cate) {
	
	$code = duplicate_bar($code);
		
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_items (item_code, item_name,item_rdate,item_cate) VALUES (:code, :name, :rdate, :item_cate)");
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':rdate',date("Y-m-d H:i:s"));
    $stmt->bindParam(':item_cate', $item_cate);
    $stmt->execute();
   
    $item_cate_name = cate_id_to_cate_name($item_cate);
   
	// 히스토리추가	
	add_history('C','제품을 등록',$item_cate_name[0]['cate_name']."분류로",$name);			
   
}

 // 제품 분류명등록 분류숫자 중복검사  
function ck_item_cate_cnt($cate_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(cate_id),'0') as cate_cnt FROM `wms_cate` WHERE  delYN = 'N' and cate_name = '".$cate_name."'");
    $stmt->execute(); 
    $cate_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $cate_cnt;
}

// 특정제품명 추출하기, 제품명 변경과정
function getwms_item_name($item_id) {
    global $conn;
	
    $stmt = $conn->query("SELECT *  FROM wms_items where item_id =".$item_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 특정제품 1가지 조회 
function select_item_one($item_id) {
    global $conn;
    $stmt = $conn->query("SELECT item_name FROM wms_items where  delYN = 'N'  and item_id = $item_id limit 0,1");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}
   
 // 재고목록에 제품이 있는지 확인, 삭제가능한 제품인지 조회 0 이면, 제품삭제가능
function stock_item_count($item_id) {
    global $conn;
    $stmt = $conn->query("SELECT sum(quantity) as count FROM wms_stock where delYN = 'N' and item_id = '".$item_id."'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

 // 제품 삭제 
function del_item($item_id,$item_name) {
    global $conn;
    $stmt0 = $conn->prepare("update wms_items set delYN = 'Y' where item_id = $item_id and  delYN = 'N'");
    $stmt0->bindParam(':item_id', $item_id);
    $stmt0->execute();	
	add_history('A','제품을 삭제',$item_name,'');			
}
 

 // 제품명 업데이트 중복검사  
function update_item_cnt($item_id,$item_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(item_id),'0') as item_cnt FROM `wms_items` WHERE  delYN = 'N'  and item_id <> ".$item_id." and item_name = '".$item_name."'");
    $stmt->execute(); 
    $item_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
	return $item_cnt;
}
  
 // 제품명 업데이트  
function update_item($item_id,$item_name) {
    global $conn;
	
		$item_name_before = item_id_to_item_name($item_id);
	
		$stmt = $conn->prepare("update wms_items set item_name = :item_name, item_rdate = :item_rdate where  delYN = 'N'  and item_id = :item_id");
		$stmt->bindParam(':item_id', $item_id);
		$stmt->bindParam(':item_name', $item_name);
		$stmt->bindParam(':item_rdate',date("Y-m-d H:i:s"));
		$stmt->execute();	
		
		add_history('B','제품명을 변경',$item_name_before[0]['item_name'],$item_name);			
		
}
 
 // 제품카테고리 삭제 
function del_cate($cate_id,$cate_name) {
    global $conn;
    $stmt0 = $conn->prepare("update wms_cate set delYN = 'Y', cate_use = 'N' where cate_id = $cate_id and  delYN = 'N'");
    $stmt0->bindParam(':cate_id', $cate_id);
    $stmt0->execute();	
	 
	add_history('A','제품분류명을 삭제',$cate_name,'');			
	
}  

// 특정제품 1가지 조회 
function select_cate_one($cate_id) {
    global $conn;
    $stmt = $conn->query("SELECT cate_name FROM wms_cate where  delYN = 'N'  and cate_id = $cate_id limit 0,1");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

// 제품 삭제가능한 카테고리인지 확인
function stock_cate_count($cate_id) {
    global $conn;
    $stmt = $conn->query("SELECT (select count(item_id) from wms_items where item_cate = wms_cate.cate_id) as count FROM wms_cate where cate_id = $cate_id and cate_id > 0");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 제품카테고리 조회
function getwms_cate($start_record_number,$itemsPerPage) {
    global $conn;
    $stmt = $conn->query("SELECT cate_id, cate_name, cate_use, cate_expose, cate_rdate, delYN, (select count(item_id)  from wms_items where item_cate = wms_cate.cate_id) as cnt_item FROM wms_cate where delYN='N' and cate_id > 0 limit $start_record_number,$itemsPerPage");
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
	
	add_history('A','제품분류를 등록',$cate_name,'');	
}
 
 // 제품카테고리 업데이트
function updateCate($cate_name,$cate_id) {
    global $conn;
	
	$cate_name_before = cate_id_to_cate_name($cate_id);
	
    $stmt = $conn->prepare("update wms_cate set cate_name = :cate_name where cate_id = :cate_id");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_id',$cate_id);
    $stmt->execute();

	add_history('B','제품분류명을 변경',$cate_name_before[0]['cate_name'],$cate_name);	

} 

// 앵글로 제품 넣기전 검사과정 (item_id, warehouse_id, angle_id 조건 검사하여, insert 값 있는지 확인)
function is_first_ck($item_id, $warehouse_id,$angle_id, $quantity) {
    global $conn;		
    $stmt = $conn->query("select count(*) as count from wms_stock where item_id=$item_id and warehouse_id=$warehouse_id and angle_id = $angle_id");
    $result= $stmt->fetch(PDO::FETCH_ASSOC);
	return $result['count'];
}


// 제품목록 > 입고등록 : 앵글로 제품 넣는 과정 (앵글로 수정)
function addStock($item_id, $warehouse_id,$angle_id, $quantity,$step_1) {
    global $conn;	
	
    $rst = is_first_ck($item_id, $warehouse_id,$angle_id, $quantity);  	
	
	if (($rst==0)||($rst=="0")) {
		
		$stmt = $conn->prepare("INSERT INTO wms_stock (item_id, warehouse_id, angle_id, quantity,rdate) VALUES ($item_id, $warehouse_id, $angle_id, $quantity,:rdate)");	
		//$stmt->bindParam(':item_id', $item_id);
		//$stmt->bindParam(':warehouse_id', $warehouse_id);
		//$stmt->bindParam(':angle_id', $angle_id);
		//$stmt->bindParam(':quantity', $quantity);
		$stmt->bindParam(':rdate', date("Y-m-d H:i:s"));	
		$stmt->execute();
	}else{
		$stmt = $conn->prepare("update wms_stock set quantity = quantity + $quantity  where item_id = $item_id and warehouse_id = $warehouse_id and angle_id = $angle_id");
		//$stmt->bindParam(':item_id', $item_id);
		//$stmt->bindParam(':warehouse_id', $warehouse_id);
		//$stmt->bindParam(':angle_id', $angle_id);
		//$stmt->bindParam(':quantity', $quantity);
		//$stmt->bindParam(':rdate', date("Y-m-d H:i:s"));	
		$stmt->execute();
			
	}
	
		$item_name = item_id_to_item_name($item_id);
		
    if ($warehouse_id==0) {
		add_history('C','창고 입고등록',$item_name[0]['item_name']." 제품 ".$quantity."개를 ",'미지정');					
    }else{
		$warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
		$angle_name     = angle_id_to_angle_name($angle_id);
		add_history('C','창고 입고등록',$item_name[0]['item_name']."제품 ".$quantity."개를 ",$warehouse_name[0]['warehouse_name']."창고의 ".$angle_name[0]['angle_name']."앵글로 ");					
    }

	
	//		$stmt = $conn->prepare("INSERT INTO wms_stock (item_id, warehouse_id, angle_id, quantity,rdate) VALUES (5, 3, 25, 6, date("Y-m-d H:i:s")) ON DUPLICATE KEY UPDATE quantity = quantity + :quantity");
 
	
	// 이력 추가
	$stmt_history1 = $conn->prepare("INSERT INTO wms_in_stock_history (item_id, to_warehouse_id, angle_id, quantity, rdate, in_stock_who, in_stock_ip) VALUES (:item_id, :to_warehouse_id, :angle_id, :quantity, :rdate, :in_stock_who, :in_stock_ip)"); // ON DUPLICATE KEY UPDATE quantity = quantity + :quantity
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
function movetoangle_inc_company_Stock($item_id, $warehouse_id,$angle_id, $quantity,$step_1) {
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
        $stmt_increase = $conn->prepare("INSERT INTO wms_stock (item_id,  warehouse_id, angle_id, quantity , rdate) VALUES (:item_id, :to_warehouse_id, :to_angle_id, :quantity, :rdate) ON DUPLICATE KEY UPDATE quantity = quantity + :quantity "); // 
        $stmt_increase->bindParam(':item_id', $item_id);
        $stmt_increase->bindParam(':to_warehouse_id', $warehouse_id);
        $stmt_increase->bindParam(':to_angle_id', $angle_id);
        $stmt_increase->bindParam(':quantity', $quantity);
        $stmt_increase->bindParam(':rdate', date("Y-m-d H:i:s"));
        $stmt_increase->execute();
//$item_id, $warehouse_id,$angle_id, $quantity


		$item_name			 = item_id_to_item_name($item_id);	    
		$warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
		$angle_name     = angle_id_to_angle_name($angle_id);   
 		
		add_history('C','앵글로 이동',$item_name[0]['item_name']."제품 ".$quantity."개를 ","미지정 창고에서 ".$warehouse_name[0]['warehouse_name']."창고 ".$angle_name[0]['angle_name']." ");	

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
function from_angle_moveto_angle_Stock($stock_id,$to_ware,$to_angle,$to_cnt) {
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
            error107($stock_id);  exit();
        }		
		
        if (($current_stock['quantity'] - $to_cnt) < 0) {
            error108($stock_id); exit();
        }

        // 변수 대입, 업데이트 시킬 카운트 계산
        $from_warehouse_id = $current_stock['warehouse_id'];
        $from_angle_id     = $current_stock['angle_id'];
        $from_cnt          = $current_stock['quantity']-$to_cnt;  // 업데이트로 인한, 이전 창고 재고감소
        $item_id           = $current_stock['item_id'];

        if (($to_ware==$from_warehouse_id)&&($to_angle==$from_angle_id)) {  // 동일 창고, 앵글
            error109($stock_id);  exit();
        }

        // 추가적으로 필요한 로직이 여기에 들어갑니다.
        // 예를 들어, 재고 이동 작업을 수행하고 데이터베이스 업데이트를 할 수 있습니다.
		
        // 재고 감소
        $stmt_decrease = $conn->prepare("UPDATE wms_stock SET quantity = :quantity  WHERE stock_id = :stock_id");
        $stmt_decrease->bindParam(':quantity', $from_cnt);
        $stmt_decrease->bindParam(':stock_id', $stock_id);
        $stmt_decrease->execute();

        // 대상 창고에 재고를 증가 또는 업데이트
        $stmt_increase = $conn->prepare("INSERT INTO wms_stock (item_id, warehouse_id, angle_id, quantity , rdate) VALUES (:item_id, :to_warehouse_id, :to_angle_id, :quantity, :rdate) ON DUPLICATE KEY UPDATE quantity = quantity + :quantity "); // 
        $stmt_increase->bindParam(':item_id', $item_id);
        $stmt_increase->bindParam(':to_warehouse_id', $to_ware);
        $stmt_increase->bindParam(':to_angle_id', $to_angle);
        $stmt_increase->bindParam(':quantity', $to_cnt);
        $stmt_increase->bindParam(':rdate', date("Y-m-d H:i:s"));
        $stmt_increase->execute();
 
        // 이력추가 
		$item_name			 = item_id_to_item_name($item_id);	    
		$from_warehouse_name = warehouse_id_to_warehouse_name($from_warehouse_id);
		$from_angle_name     = angle_id_to_angle_name($from_angle_id);   
		
		$to_ware_name		 = warehouse_id_to_warehouse_name($to_ware);
		$to_angle_name       = angle_id_to_angle_name($to_angle);  
		
		add_history('C','재고이동',$item_name[0]['item_name']."제품 ".$to_cnt."개를 ".$from_warehouse_name[0]['warehouse_name']."창고 ".$from_angle_name[0]['angle_name']."앵글에서 ",$to_ware_name[0]['warehouse_name']."창고 ".$to_angle_name[0]['angle_name']."앵글로 ");	
 
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
  
// 현재 재고 상태 가져오기 
function getStock($start_record_number,$itemsPerPage,$search_add) {
	
   $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2

   if ($result_setting[0]['set_state']=="N") {
	   $add_sql = " and quantity > 0"; 
   }else{
	   $add_sql = " "; 
   }
	
    global $conn;

   $sql=" SELECT ";
   $sql=$sql." s.*,  item_name, "; 
   $sql=$sql." IFNULL(w.warehouse_id, '/') AS warehouse_id_null,  ";
   $sql=$sql." IFNULL(w.warehouse_id, '0') AS warehouse_id,  ";
   $sql=$sql." IFNULL(w.warehouse_name, '배정안됨') AS warehouse_name,  ";
   $sql=$sql." a.angle_name AS angle_name   ";
   $sql=$sql." FROM  ";
   $sql=$sql."  `wms_stock` AS s  ";
   $sql=$sql." LEFT JOIN  ";
   $sql=$sql." `wms_items` AS i  ";
   $sql=$sql." ON s.item_id = i.item_id  ";
   $sql=$sql." LEFT JOIN  ";
   $sql=$sql."  `wms_warehouses` AS w  ";
   $sql=$sql."  ON s.warehouse_id = w.warehouse_id   ";
   $sql=$sql." JOIN  ";
   $sql=$sql."  `wms_angle` AS a  ";
   $sql=$sql."  ON s.angle_id = a.angle_id  ";
   $sql=$sql." WHERE  ";
   $sql=$sql."  w.delYN = 'N'  ";
   $sql=$sql."  AND a.delYN = 'N'  ";
   $sql=$sql."  AND w.warehouse_id <> 0     "; 
   $sql=$sql.$add_sql;
   $sql=$sql.$search_add;
   $sql=$sql."  order by s.rdate desc, s.item_id limit $start_record_number,$itemsPerPage ";
 
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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



// 입고지시관리 신규등록 start //	
 if (($_POST['page_name']!="")&&($_POST['page_name']=="inbound_write")) {  // inbound_write (입고지시 등록으로부터 받은 값)
	 
	// POST로 전달된 데이터 수신
	$product_ids = $_POST['product_id'];
	$company_ids = $_POST['company_id'];
	$warehouse_id = $_POST['warehouse_id'];
	$angle_id = $_POST['angle_id'];
	$planned_quantities = $_POST['planned_quantity'];
	$inbound_quantities = $_POST['inbound_quantity'];
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
 
    $sql = "	SELECT ";
    $sql = $sql."	    i.inbound_id as inbound_id, p.item_name AS item_name,  ";
    $sql = $sql."	    w.warehouse_name AS warehouse_name, ";
    $sql = $sql."	    ( ";
    $sql = $sql."	       SELECT angle_name  ";
    $sql = $sql."	        FROM wms_angle  ";
    $sql = $sql."	        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
    $sql = $sql."	    ) AS angle_name, ";
    $sql = $sql."	    (  ";
    $sql = $sql."	       SELECT cate_name  ";
    $sql = $sql."	       from wms_company  ";
    $sql = $sql."	       where cate_id = i.company_id  ";
    $sql = $sql."	    ) as company_name,  ";
   // $sql = $sql."	    i.company_id,  ";
    $sql = $sql."	    i.planned_quantity,  ";
    $sql = $sql."	   i.inbound_quantity,  ";
    $sql = $sql."	    i.plan_date,  ";
    $sql = $sql."	    i.rdate,  ";
    $sql = $sql."	    i.state ";
    $sql = $sql."	FROM  ";
    $sql = $sql."	    wms_inbound i   ";
    $sql = $sql."	JOIN  ";
    $sql = $sql."	    wms_items p ON p.item_id = i.product_id ";
    $sql = $sql."	JOIN  ";
    $sql = $sql."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id and  i.inbound_id = ".$inbound_id;
	$stmt = $conn->query($sql);
    return $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 예정수량과 , 입고 수량이 같은지 비교, 같으면 입고처리 진행을 위한 조회.
function ck_state($inbound_id){
    global $conn;
    $sql = "select count(state) as cnt from wms_inbound where planned_quantity = inbound_quantity and inbound_id =  ".$inbound_id;
	$stmt = $conn->query($sql);
    return $result = $stmt->fetchAll(PDO::FETCH_ASSOC);	
}


 // 인바운드 입고수량 업데이트  
function update_inbound($inbound_id,$inbound_cnt,$plan_date) {
    global $conn;
		
		$stmt = $conn->prepare("update wms_inbound set inbound_quantity = inbound_quantity + :inbound_quantity where inbound_id = :inbound_id");
		$stmt->bindParam(':inbound_id', $inbound_id);
		$stmt->bindParam(':inbound_quantity', $inbound_cnt);
		$stmt->execute();	
		//echo "<script>alert('1');</script>";

		$result = ck_state($inbound_id); // 예정수량과 , 입고 수량이 같은지 비교, 같으면 입고처리 진행한다.
		//echo "<script>alert('2');</script>";

		if ($result[0]['cnt'] == 1) { // 예정수량과 , 입고 수량 동일시, 실제 입고완료처리
 		//echo "<script>alert('3');</script>";

			//1 인바운드 완료처리
			$stmt = $conn->prepare("update wms_inbound set state = 1, rdate = CURDATE()  where inbound_id = :inbound_id");
			$stmt->bindParam(':inbound_id', $inbound_id);
			$stmt->execute();	
			
 		//echo "<script>alert('4');</script>";
			
			//2 stock 실제 입고처리 준비로, item 가져오기
			
			$result2 = get_wms_inbound_item($inbound_id);	
			
			// 2-1 해당 제품이 없으면, insert,  있으면 update
			$item_id		= $result2[0]['product_id'];
			$to_ware	    = $result2[0]['warehouse_id'];
			$to_angle		= $result2[0]['angle_id'];
			$qua			= $result2[0]['inbound_quantity'];
			$step			= 'N';
 
			// 실제 입고처리
			addStock($item_id,$to_ware,$to_angle,$qua,$step); // step = Y 면 앵글에 보관 / 아니면 N
			
		}else{		
		//echo "<script>alert('확인바람');</script>";
		//exit();
		}
		
		
	  // add_history('B','창고명을 변경',$warehouse_name_before[0]['warehouse_name'],$warehouse_name);			
}			
 
 
 // 인바운드  삭제 
function del_inbound($inbound_id) {
    global $conn;
		
		$stmt = $conn->prepare("delete from wms_inbound where inbound_id = :inbound_id");
		$stmt->bindParam(':inbound_id', $inbound_id);
		$stmt->execute();	
	  // add_history('B','창고명을 변경',$warehouse_name_before[0]['warehouse_name'],$warehouse_name);			
}
 


// 입고지시 목록 가져오기
function getwms_inbounds($start_record_number,$itemsPerPage,$search,$SearchString) {
    global $conn;

    //$stmt = $conn->query("SELECT p.item_name as item_name, (SELECT w.warehouse_name  from  wms_inbound i  JOIN wms_warehouses w  on w.warehouse_id = i.warehouse_id where  p.item_id = i.product_id) as warehouse_name, (select angle_name from wms_angle where angle_id = i.angle_id and warehouse_id = i.warehouse_id) as angle_name, company_name, planned_quantity, inbound_quantity, plan_date, rdate, state  from  wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id  where 1=1 ".$condition_sql."  order by i.plan_date desc limit $start_record_number,$itemsPerPage");
	 
    $sql = "	SELECT ";
    $sql = $sql."	    i.inbound_id as inbound_id, p.item_name AS item_name,  ";
    $sql = $sql."	    w.warehouse_name AS warehouse_name, ";
    $sql = $sql."	    ( ";
    $sql = $sql."	       SELECT angle_name  ";
    $sql = $sql."	        FROM wms_angle  ";
    $sql = $sql."	        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
    $sql = $sql."	    ) AS angle_name, ";
	    
    $sql = $sql."	    (  ";
    $sql = $sql."	       SELECT cate_name  ";
    $sql = $sql."	       from wms_company  ";
    $sql = $sql."	       where cate_id = i.company_id  ";
    $sql = $sql."	    ) as company_name,  ";
   // $sql = $sql."	    i.company_id,  ";
    $sql = $sql."	    i.planned_quantity,  ";
    $sql = $sql."	   i.inbound_quantity,  ";
    $sql = $sql."	    i.plan_date,  ";
    $sql = $sql."	    i.rdate,  ";
    $sql = $sql."	    i.state ";
    $sql = $sql."	FROM  ";
    $sql = $sql."	    wms_inbound i   ";
    $sql = $sql."	JOIN  ";
    $sql = $sql."	    wms_items p ON p.item_id = i.product_id ";
    $sql = $sql."	JOIN  ";
    $sql = $sql."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
   // $sql = $sql." WHERE  (  SELECT cate_name FROM wms_company  WHERE cate_id = i.company_id  )  like '%B%'	";	
    $sql = $sql.$SearchString;	
    $sql = $sql." and i.delYN = 'N' order by i.plan_date desc, i.inbound_id desc limit $start_record_number,$itemsPerPage ";
	
    $stmt = $conn->query($sql);	
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $result; 
}
  
  
// 입고지시서 팝업 (입고등록) 처리를 위한, item 가져오기
function get_wms_inbound_item($inbound_id) {
    global $conn;
    $stmt = $conn->query("SELECT product_id,warehouse_id,angle_id,inbound_quantity FROM wms_inbound where  inbound_id = ".$inbound_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

  
// 출고지시서 팝업 (출고등록) 처리를 위한, item 가져오기
function get_wms_outbound_item($outbound_id) {
    global $conn;
    $stmt = $conn->query("SELECT product_id,warehouse_id,angle_id,outbound_quantity,company_id FROM wms_outbound where  outbound_id = ".$outbound_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 출고지시관리 * //

 
// 출고지시관리 신규등록 start //	
 if (($_POST['page_name']!="")&&($_POST['page_name']=="outbound_write")) {  // outbound_write (출고지시 등록으로부터 받은 값)
	 
	// POST로 전달된 데이터 수신
	$product_ids = $_POST['product_id'];
	$company_ids = $_POST['company_id'];
	$warehouse_id = $_POST['warehouse_id'];
	$angle_id = $_POST['angle_id'];
	$planned_quantities = $_POST['planned_quantity'];
	$outbound_quantities = $_POST['outbound_quantity'];
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
function is_able_ck($item_id, $warehouse_id,$angle_id, $quantity,$company_id) {
    global $conn;		
    //$stmt = $conn->query("select count(*) as count from wms_stock where item_id=$item_id and warehouse_id=$warehouse_id and company_name='".$company_name."' and angle_id = $angle_id");
	
    $sql = "select count(stock_id) as count2 from wms_stock where  item_id=$item_id and warehouse_id=$warehouse_id and angle_id=$angle_id  and delYN ='N' and quantity >= $quantity";
 
	$stmt = $conn->query($sql);
    return $result_ck = $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

  
  
//  입고지시목록 완료처리 : wms_inbound 테이블 state = 1 처리,  입고지시 등록시, 예상수량 및 입고수량을 동일 등록시, 완료처리하기
function update_wms_inbound_same_quantity_state_1($item_id, $warehouse_id,$angle_id, $company_id,$plan_date) {
    global $conn;	

		$stmt = $conn->prepare("update wms_inbound set state = 1 ,  rdate = CURDATE() where product_id = $item_id and warehouse_id = $warehouse_id and angle_id = $angle_id  and plan_date = '".$plan_date."' and delYN ='N'");
		$stmt->execute();	
}  
  
//  출고지시목록 완료처리 : wms_outbound 테이블 state = 1 처리,  출고지시 등록시, 예상수량 및 출고수량을 동일 등록시, 완료처리하기
function update_wms_outbound_same_quantity_state_1($item_id, $warehouse_id,$angle_id, $company_id,$plan_date) {
    global $conn;	

		$stmt = $conn->prepare("update wms_outbound set state = 1 ,  rdate = CURDATE() where product_id = $item_id and warehouse_id = $warehouse_id and angle_id = $angle_id and company_id= $company_id and plan_date = '".$plan_date."' and delYN ='N'");
		$stmt->execute();	
}  
  
//  출고등록 : 앵글의 제품수 차감 (출고)
function add_outStock($item_id, $warehouse_id,$angle_id, $quantity,$step,$company_id) {
    global $conn;	
 
        $company_name   = company_id_to_company_name($company_id);
 		$item_name      = item_id_to_item_name($item_id);
		$warehouse_name = warehouse_id_to_warehouse_name($warehouse_id);
		$angle_name     = angle_id_to_angle_name($angle_id);

		$stmt = $conn->prepare("update wms_stock set quantity = quantity - $quantity  where item_id = $item_id and warehouse_id = $warehouse_id and angle_id = $angle_id  and delYN ='N'");
		$stmt->execute();
 
		add_history('C','출고완료',$item_name[0]['item_name']."제품 ".$quantity."개를 ".$company_name[0]['cate_name']."업체",$warehouse_name[0]['warehouse_name']."창고의 ".$angle_name[0]['angle_name']." 앵글로부터 ");					

	// 이력 추가
	$stmt_history1 = $conn->prepare("INSERT INTO wms_out_stock_history (item_id, to_warehouse_id, angle_id, quantity, rdate, out_stock_who, out_stock_ip) VALUES (:item_id, :to_warehouse_id, :angle_id, :quantity, :rdate, :out_stock_who, :out_stock_ip)"); // ON DUPLICATE KEY UPDATE quantity = quantity + :quantity
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
    $sql = "select count(state) as cnt, product_id, warehouse_id, angle_id, outbound_quantity, CASE WHEN warehouse_id = 0 THEN 'N' ELSE 'Y' END AS step, ";
    $sql = $sql."	    (  ";
    $sql = $sql."	       SELECT cate_name  ";
    $sql = $sql."	       from wms_company  ";
    $sql = $sql."	       where cate_id = wms_outbound.company_id  ";
    $sql = $sql."	    ) as company_name,  ";	
    $sql = $sql."	 wms_outbound.company_id as company_id ";	
    $sql = $sql." from wms_outbound where planned_quantity = outbound_quantity and outbound_id =  ".$outbound_id;
	$stmt = $conn->query($sql);
    return $result = $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

// 아웃바운드 출고예정수량 및 출고된 수량 가져오기
function getwms_wms_outbound_cnt($outbound_id) {
    global $conn;
 
    $stmt = $conn->query("SELECT planned_quantity, outbound_quantity, (planned_quantity - outbound_quantity) as able_outbound_quantity, plan_date  FROM wms_outbound where  outbound_id = ".$outbound_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


 // 아웃바운드 출고수량 업데이트  
function update_outbound($outbound_id,$outbound_cnt,$plan_date) {
    global $conn;
	
		// 1. wms_outbound에, 출고수량만 우선 업데이트
		$stmt = $conn->prepare("update wms_outbound set outbound_quantity = outbound_quantity + :outbound_quantity where outbound_id = :outbound_id");
		$stmt->bindParam(':outbound_id', $outbound_id);
		$stmt->bindParam(':outbound_quantity', $outbound_cnt);
		$stmt->execute();	
		
        // 2. 예정수량과 , 출고 수량비교, 같으면 출고가능 !
		$result = out_ck_state($outbound_id); 
		
		if ($result[0]['cnt'] == 1) { //  출고 완료표시로 바꾸기 가능 ( 하지만, 실제 출고되는지는 더 세부확인필요) !!
			$item_id		= $result[0]['product_id'];
			$warehouse_id   = $result[0]['warehouse_id'];
			$angle_id		= $result[0]['angle_id'];
			$qua			= $result[0]['outbound_quantity'];
			$step			= $result[0]['step'];
			$company_name   = $result[0]['company_name'];		
			$company_id     = $result[0]['company_id'];		
 
			/////////////////////////////////////////////////////////////////
			//0 출고지시 출고수량이, 실 재고 수량과 부합하는지 사전 검사,  정상 : 출고수량 <= 실재 재고수량
  			$count2 = 0;
			$result_ck = is_able_ck($item_id, $warehouse_id,$angle_id, $qua,$company_id);  	
			$count2 = $result_ck[0]['count2'];
			
			if ($count2 == 1) {	// 존재하면(실제 수량대비 그 이하이면) 진짜 출고하기 !!

 					//1 아웃바운드 완료처리
					$stmt = $conn->prepare("update wms_outbound set state = 1, rdate = CURDATE()  where planned_quantity = outbound_quantity and  outbound_id = :outbound_id");
					$stmt->bindParam(':outbound_id', $outbound_id);
					$stmt->execute();	
					
					//2 stock 실제 출고처리 진행			
					add_outStock($item_id,$warehouse_id,$angle_id,$qua,$step,$company_id); // step = Y 면 앵글에 보관 / 아니면 N						
			}else{ // 불가
							
				echo "<script>alert('실 재고수량과 불일치.삭제후 다시 하세요.');</script>";
			}					
		}else{
		}		
}


// 아웃바운드 출고삭제용 정보 가져오기
function getwms_wms_outbound_info($outbound_id) {
    global $conn;
 
    $sql = "	SELECT ";
    $sql = $sql."	    i.outbound_id as outbound_id, p.item_name AS item_name,  ";
    $sql = $sql."	    w.warehouse_name AS warehouse_name, ";
    $sql = $sql."	    ( ";
    $sql = $sql."	       SELECT angle_name  ";
    $sql = $sql."	        FROM wms_angle  ";
    $sql = $sql."	        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
    $sql = $sql."	    ) AS angle_name, ";
    $sql = $sql."	    (  ";
    $sql = $sql."	       SELECT cate_name  ";
    $sql = $sql."	       from wms_company  ";
    $sql = $sql."	       where cate_id = i.company_id  ";
    $sql = $sql."	    ) as company_name,  ";
   // $sql = $sql."	    i.company_id,  ";
    $sql = $sql."	    i.planned_quantity,  ";
    $sql = $sql."	   i.outbound_quantity,  ";
    $sql = $sql."	    i.plan_date,  ";
    $sql = $sql."	    i.rdate,  ";
    $sql = $sql."	    i.state ";
    $sql = $sql."	FROM  ";
    $sql = $sql."	    wms_outbound i   ";
    $sql = $sql."	JOIN  ";
    $sql = $sql."	    wms_items p ON p.item_id = i.product_id ";
    $sql = $sql."	JOIN  ";
    $sql = $sql."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id and  i.outbound_id = ".$outbound_id;
	$stmt = $conn->query($sql);
    return $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 아웃바운드 삭제 
function del_outbound($outbound_id) {
    global $conn;
		
		$stmt = $conn->prepare("update wms_outbound set delYN = 'Y' where outbound_id = :outbound_id");
		$stmt->bindParam(':outbound_id', $outbound_id);
		$stmt->execute();	
	  // add_history('B','창고명을 변경',$warehouse_name_before[0]['warehouse_name'],$warehouse_name);			
}


// 출고지시 목록 가져오기
function getwms_outbounds($start_record_number,$itemsPerPage,$search,$SearchString) {
    global $conn;

    //$stmt = $conn->query("SELECT p.item_name as item_name, (SELECT w.warehouse_name  from  wms_outbound i  JOIN wms_warehouses w  on w.warehouse_id = i.warehouse_id where  p.item_id = i.product_id) as warehouse_name, (select angle_name from wms_angle where angle_id = i.angle_id and warehouse_id = i.warehouse_id) as angle_name, company_name, planned_quantity, outbound_quantity, plan_date, rdate, state  from  wms_outbound i  JOIN wms_items p  on p.item_id = i.product_id  where 1=1 ".$condition_sql."  order by i.plan_date desc limit $start_record_number,$itemsPerPage");
	 
    $sql = "	SELECT ";
    $sql = $sql."	    i.outbound_id as outbound_id, p.item_name AS item_name,  ";
    $sql = $sql."	    w.warehouse_name AS warehouse_name, ";
    $sql = $sql."	    ( ";
    $sql = $sql."	       SELECT angle_name  ";
    $sql = $sql."	        FROM wms_angle  ";
    $sql = $sql."	        WHERE angle_id = i.angle_id AND warehouse_id = i.warehouse_id ";
    $sql = $sql."	    ) AS angle_name, ";
	    
    $sql = $sql."	    (  ";
    $sql = $sql."	       SELECT cate_name  ";
    $sql = $sql."	       from wms_company  ";
    $sql = $sql."	       where cate_id = i.company_id  ";
    $sql = $sql."	    ) as company_name,  ";
   // $sql = $sql."	    i.company_id,  ";
    $sql = $sql."	    i.planned_quantity,  ";
    $sql = $sql."	   i.outbound_quantity,  ";
    $sql = $sql."	    i.plan_date,  ";
    $sql = $sql."	    i.rdate,  ";
    $sql = $sql."	    i.state, ";
	
    $sql = $sql."	     COALESCE(( ";
    $sql = $sql."	       SELECT quantity ";
    $sql = $sql."	       from wms_stock ";
    $sql = $sql."	       where item_id = i.product_id AND warehouse_id = i.warehouse_id AND angle_id = i.angle_id ";
    $sql = $sql."	     ), 0)  as stock_quantity ";
 
    $sql = $sql."	FROM  ";
    $sql = $sql."	    wms_outbound i   ";
    $sql = $sql."	JOIN  ";
    $sql = $sql."	    wms_items p ON p.item_id = i.product_id ";
    $sql = $sql."	JOIN  ";
    $sql = $sql."	    wms_warehouses w ON w.warehouse_id = i.warehouse_id ";  
   // $sql = $sql." WHERE  (  SELECT cate_name FROM wms_company  WHERE cate_id = i.company_id  )  like '%B%'	";	
    $sql = $sql.$SearchString;	
    $sql = $sql." and i.delYN = 'N' order by i.plan_date desc, i.outbound_id desc limit $start_record_number,$itemsPerPage ";
	
    $stmt = $conn->query($sql);	
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $result; 
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * HISTORY관리 * //

// Get current 입고 stock_history information
function get_in_Stock_history_detail($start_record_number,$itemsPerPage) {
    global $conn;
    $stmt = $conn->query("SELECT s.item_id as item_id, s.to_warehouse_id as to_warehouse_id,  s.wms_stock_id as wms_stock_id, s.angle_id as angle_id, s.quantity as quantity, s.rdate as rdate, s.in_stock_who as in_stock_who, s.in_stock_ip as in_stock_ip, (select item_name from wms_items where item_id = s.item_id) as item_name ,  v.angle_name as angle_name, v.warehouse_name as warehouse_name  FROM wms_in_stock_history s left join view_warehouse_angle v on s.angle_id = v.angle_id and s.to_warehouse_id = v.warehouse_id  order by s.rdate desc limit $start_record_number,$itemsPerPage");
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
    $stmt = $conn->query("SELECT DATE_FORMAT(date_range.date, '%Y-%m-%d') AS input_day, COALESCE(SUM(wms_in_stock_history.quantity), 0) AS total_sum FROM (    SELECT DATE_SUB(CURDATE(), INTERVAL n DAY) AS date    FROM (        SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3        UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6        UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9        UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12        UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15        UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18        UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21        UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24        UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27        UNION ALL SELECT 28 UNION ALL SELECT 29    ) AS date_range ) AS date_range LEFT JOIN wms_in_stock_history ON DATE_FORMAT(date_range.date, '%Y-%m-%d') = DATE_FORMAT(wms_in_stock_history.rdate, '%Y-%m-%d') WHERE date_range.date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) GROUP BY DATE_FORMAT(date_range.date, '%Y-%m-%d') ORDER BY DATE_FORMAT(date_range.date, '%Y-%m-%d')");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
 
 
 
 
 
 
 
// Get current 출고 stock_history information
function get_out_Stock_history_detail($start_record_number,$itemsPerPage) {
    global $conn;
    $stmt = $conn->query("SELECT s.item_id as item_id, s.to_warehouse_id as to_warehouse_id,  s.wms_stock_id as wms_stock_id, s.angle_id as angle_id, s.quantity as quantity, s.rdate as rdate, s.out_stock_who as out_stock_who, s.out_stock_ip as out_stock_ip, (select item_name from wms_items where item_id = s.item_id) as item_name ,  v.angle_name as angle_name, v.warehouse_name as warehouse_name  FROM wms_out_stock_history s left join view_warehouse_angle v on s.angle_id = v.angle_id and s.to_warehouse_id = v.warehouse_id  order by s.rdate desc limit $start_record_number,$itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get current 출고 stock_history information
function get_out_Stock_history_day_cnt7() {
    global $conn;
    $stmt = $conn->query("SELECT DATE_SUB(CURDATE(), INTERVAL n DAY) AS input_day, COALESCE(SUM(quantity), 0) AS total_sum FROM (     SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3     UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 ) AS dates LEFT JOIN wms_out_stock_history ON DATE(rdate) = DATE_SUB(CURDATE(), INTERVAL n DAY) WHERE DATE_SUB(CURDATE(), INTERVAL n DAY) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE_SUB(CURDATE(), INTERVAL n DAY) ORDER BY DATE_SUB(CURDATE(), INTERVAL n DAY)");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
// Get current 출고 stock_history information
function get_out_Stock_history_day_cnt30() {
    global $conn;
    $stmt = $conn->query("SELECT DATE_FORMAT(date_range.date, '%Y-%m-%d') AS input_day, COALESCE(SUM(wms_out_stock_history.quantity), 0) AS total_sum FROM (    SELECT DATE_SUB(CURDATE(), INTERVAL n DAY) AS date    FROM (        SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3        UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6        UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9        UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12        UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15        UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18        UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21        UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24        UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27        UNION ALL SELECT 28 UNION ALL SELECT 29    ) AS date_range ) AS date_range LEFT JOIN wms_out_stock_history ON DATE_FORMAT(date_range.date, '%Y-%m-%d') = DATE_FORMAT(wms_out_stock_history.rdate, '%Y-%m-%d') WHERE date_range.date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) GROUP BY DATE_FORMAT(date_range.date, '%Y-%m-%d') ORDER BY DATE_FORMAT(date_range.date, '%Y-%m-%d')");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
  

 //  history information
function get_history($start_record_number,$itemsPerPage,$add_condition) {
    global $conn;
	
	// 검색조건
	$condition_sql = "";
	if ($SearchString!="") {
	//	$condition_sql = " and  ".$search." like '%".$SearchString."%' ";	
	}else{
	//	$condition_sql = "";	
	}	
 	$stmt = $conn->query("SELECT * FROM wms_history where 1=1  ".$add_condition."  order by h_date desc limit $start_record_number,$itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


 //  history information , 홈 슬라이드 메뉴
function get_history_item_list($item) {
    global $conn;
    $stmt = $conn->query("SELECT DISTINCT $item, h_loc_code, h_location FROM wms_history order by $item asc");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 

 //  history information  , 홈 
function get_history_item_list_cate($item,$h_loc_code) {
    global $conn;
    $stmt = $conn->query("SELECT DISTINCT $item, h_loc_code, h_location FROM wms_history where h_loc_code ='".$h_loc_code."' order by $item asc");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 운영자관리 * //
// 조회
function get_admin_cate_add_cate_use($start_record_number,$itemsPerPage,$sql_cate_use) {
    global $conn;
    $stmt = $conn->query("SELECT cate_id,cate_name,cate_use,cate_expose,cate_rdate,cate_comment,cate_admin_role, (select count(*) from wms_admin where admin_use='Y' and wms_admin_cate.cate_admin_role = wms_admin.admin_role) as use_admin_role_cnt, (select count(*) from wms_admin where admin_use='N' and wms_admin_cate.cate_admin_role = wms_admin.admin_role) as notuse_admin_role_cnt  FROM wms_admin_cate where cate_admin_role <> 100 and cate_use ".$sql_cate_use." and cate_admin_role <= ".$_SESSION['admin_role']." order by cate_admin_role desc limit $start_record_number,$itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 등록
function get_admin_cate_reg($start_record_number,$itemsPerPage) {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_admin_cate where cate_admin_role <= ".$_SESSION['admin_role']." and cate_use = 'Y' and cate_expose = 'Y' order by cate_admin_role desc limit $start_record_number,$itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

 // 운영자분류명관리 카테고리 추가
function add_admin_Cate($cate_admin_role,$cate_name,$cate_comment) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_admin_cate (cate_name,cate_rdate,cate_comment,cate_admin_role) VALUES (:cate_name,:cate_rdate,:cate_comment,:cate_admin_role)");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_rdate',date("Y-m-d H:i:s"));
    $stmt->bindParam(':cate_comment', $cate_comment);
    $stmt->bindParam(':cate_admin_role', $cate_admin_role);
    $stmt->execute();
}

// 분류명수정, 운영자카테고리 1가지 조회  
function get_admin_cate_search1($cate_id) {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_admin_cate where cate_id = $cate_id limit 0,1");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


 // 분류명수정, 운영자카테고리 업데이트
function update_admin_Cate($cate_admin_role,$cate_name,$cate_id,$cate_comment,$before_cate_admin_role) {
    global $conn;
    $stmt = $conn->prepare("update wms_admin_cate set cate_admin_role = :cate_admin_role, cate_name = :cate_name, cate_comment = :cate_comment where cate_id = :cate_id");
    $stmt->bindParam(':cate_admin_role', $cate_admin_role);
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_id',$cate_id);
    $stmt->bindParam(':cate_comment',$cate_comment);
    $stmt->execute();
	
    $stmt2 = $conn->prepare("update wms_admin set admin_role = ".$cate_admin_role." where admin_role = ".$before_cate_admin_role);
    $stmt2->execute();
}


// 운영자 목록 가져오기
function getwms_users($start_record_number,$itemsPerPage,$search,$SearchString) {
    global $conn;
	
	// 검색조건
	$condition_sql = "";
	if ($SearchString!="") {
		$condition_sql = " and a.".$search." like '%".$SearchString."%' ";	
	}else{
		$condition_sql = "";	
	}
		//  where 1 = 1 ".$condition_sql."  
	if ($_SESSION['admin_role']=="100") {
		$stmt = $conn->query("SELECT * FROM wms_admin a join wms_admin_cate c on a.admin_role = c.cate_admin_role ".$condition_sql." order by c.cate_admin_role desc  limit $start_record_number,$itemsPerPage ");
	}else{
		$stmt = $conn->query("SELECT * FROM  wms_admin a join wms_admin_cate c on a.admin_role = c.cate_admin_role and admin_role < 100  ".$condition_sql."  order by c.cate_admin_role desc  limit $start_record_number,$itemsPerPage ");
	}	
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $result; 
}
  

// 운영자 추가
function addAdmin($admin_id, $admin_name, $cate_admin_role) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_admin (admin_id, admin_name,admin_pw,admin_role,admin_rdate) VALUES (:admin_id, :admin_name,:admin_pw,:admin_role,:admin_rdate)");
    $admin_pw = password_hash('1234', PASSWORD_DEFAULT);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':admin_name', $admin_name);
    $stmt->bindParam(':admin_pw', $admin_pw);
    $stmt->bindParam(':admin_role', $cate_admin_role);
    $stmt->bindParam(':admin_rdate',date("Y-m-d H:i:s"));
    $stmt->execute();
}

 // 운영자 분류명등록 분류숫자 중복검사  
function ck_cate_cnt($role_num) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(cate_id),'0') as role_cnt FROM `wms_admin_cate` WHERE  delYN = 'N'  and cate_admin_role = '".$role_num."'");
    $stmt->execute(); 
    $role_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $role_cnt;
}

 // 운영자등록 아이디 중복검사  
function ck_user_cnt($admin_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(admin_id),'0') as user_cnt FROM `wms_admin` WHERE  delYN = 'N'  and admin_id = '".$admin_id."'");
    $stmt->execute(); 
    $user_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $user_cnt;
}


 // 거래처 중복검사  
function ck_company_cnt($cate_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(cate_id),'0') as cate_cnt FROM `wms_company` WHERE  delYN = 'N' and cate_name = '".$cate_name."'");
    $stmt->execute(); 
    $cate_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $cate_cnt;
}

// 거래처 조회
function getwms_company($start_record_number,$itemsPerPage) {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_company where delYN='N' and cate_name <>'미지정' limit $start_record_number,$itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}  


// 거래처명 1가지 조회 
function getwms_company_search1($cate_id) {
    global $conn;
    $stmt = $conn->query("SELECT cate_name FROM wms_company where cate_id = $cate_id limit 0,1");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//  거래처 추가
function addcompany($cate_name) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_company (cate_name,cate_rdate) VALUES (:cate_name,:cate_rdate)");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_rdate',date("Y-m-d H:i:s"));
    $stmt->execute();
	
	add_history('A','거래처를 등록',$cate_name,'');	
}

// 거래처 업데이트
function updatecompany($cate_name,$cate_id) {
    global $conn;
	
	$cate_name_before = company_id_to_company_name($cate_id);
	
    $stmt = $conn->prepare("update wms_company set cate_name = :cate_name where cate_id = :cate_id");
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_id',$cate_id);
    $stmt->execute();

	add_history('B','거래처명을 변경',$cate_name_before[0]['cate_name'],$cate_name);	

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
function add_access($access_id,$access_name) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_access_crud (access_id,access_name,access_type,access_value,access_rdate,access_order) VALUES (:access_id,:access_name,'R','99',:access_rdate,:access_order)");
    $stmt->bindParam(':access_id', $access_id);
    $stmt->bindParam(':access_name', $access_name);
    $stmt->bindParam(':access_rdate',date("Y-m-d H:i:s"));
    $stmt->bindParam(':access_order', $access_id);
   $stmt->execute();
	
    $stmt = $conn->prepare("INSERT INTO wms_access_crud (access_id,access_name,access_type,access_value,access_rdate,access_order) VALUES (:access_id,:access_name,'W','99',:access_rdate,:access_order)");
    $stmt->bindParam(':access_id', $access_id);
    $stmt->bindParam(':access_name', $access_name);
    $stmt->bindParam(':access_rdate',date("Y-m-d H:i:s"));
    $stmt->bindParam(':access_order', $access_id);
    $stmt->execute();
	
    $stmt = $conn->prepare("INSERT INTO wms_access_crud (access_id,access_name,access_type,access_value,access_rdate,access_order) VALUES  (:access_id,:access_name,'U','99',:access_rdate,:access_order)");
    $stmt->bindParam(':access_id', $access_id);
    $stmt->bindParam(':access_name', $access_name);
    $stmt->bindParam(':access_rdate',date("Y-m-d H:i:s"));
    $stmt->bindParam(':access_order', $access_id);
    $stmt->execute();
	
    $stmt = $conn->prepare("INSERT INTO wms_access_crud (access_id,access_name,access_type,access_value,access_rdate,access_order) VALUES  (:access_id,:access_name,'D','99',:access_rdate,:access_order)");
    $stmt->bindParam(':access_id', $access_id);
    $stmt->bindParam(':access_name', $access_name);
    $stmt->bindParam(':access_rdate',date("Y-m-d H:i:s"));
    $stmt->bindParam(':access_order', $access_id);
    $stmt->execute();
}

// 접근관리 조회
function wms_access() {
    global $conn;
    $stmt = $conn->query("SELECT cate_admin_role, cate_name FROM wms_admin_cate where cate_admin_role > 1 and cate_admin_role < 100 and  cate_use ='Y' order by cate_admin_role desc");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 접근관리 조회 출력
function wms_access_crud($access_id,$access_type) {
    global $conn;
    $stmt = $conn->query("select a.access_id as access_id, c.cate_name as cate_name, c.cate_admin_role as cate_admin_role, a.access_type as access_type from wms_admin_cate c inner join wms_access_crud a on a.access_value = c.cate_admin_role where c.cate_use = 'Y' and a.access_type = '".$access_type."' and a.access_id = '".$access_id."' order by c.cate_admin_role desc");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
 

// 분류 추가하기 (일반)
function wms_access_add($access_id,$access_type) {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_admin_cate WHERE cate_admin_role <> 100 and cate_admin_role <> 0 and cate_admin_role <> 1 and cate_use = 'Y' and cate_admin_role not IN (SELECT access_value FROM wms_access_crud WHERE access_id = '".$access_id."' and access_type = '".$access_type."') order by cate_admin_role desc");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  
// 분류 추가하기 (특정, SYS) 
function wms_access_add_onlysystme($access_id,$access_type) {
    global $conn;
    $stmt = $conn->query("SELECT * FROM wms_admin_cate WHERE cate_admin_role <> 100 and cate_admin_role <> 0 and cate_admin_role <> 1 and cate_use = 'Y' and cate_admin_role not IN (SELECT access_value FROM wms_access_crud WHERE access_id = '".$access_id."' and access_type = '".$access_type."')  and cate_admin_role > 90  order by cate_admin_role desc");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 접근권한관리 목록 가져오기
function getwms_access_crud($start_record_number,$itemsPerPage,$search,$SearchString) {
    global $conn;
	
   // only_full_group_by 비활성화
  //  $conn->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
	
	// 검색조건
	$condition_sql = "";
	if ($SearchString!="") {
		$condition_sql = " and a.".$search." like '%".$SearchString."%' ";	
	}else{
		$condition_sql = "";	
	}
		//  where 1 = 1 ".$condition_sql."  
	$stmt = $conn->query("SELECT * FROM wms_access_crud group by access_id order by access_order,access_id");
	
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
    //$stmt->bindParam(':rdate',date("Y-m-d H:i:s"));
    $stmt->execute();
}
 
// 접근권한관리 일괄삽입 (이미 삽입건 제외하고 넣기)
function m04_role_add_all($m04_access_id, $m04_access_name, $m04_access_type, $m04_access_value) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_access_crud (access_id, access_name, access_type, access_value) SELECT '".$m04_access_id."', '".$m04_access_name."', '".$m04_access_type."', cate_admin_role FROM `wms_admin_cate` where cate_admin_role not in (0,1,100) and cate_use = 'Y' and  cate_admin_role not in (select access_value from wms_access_crud where access_id = '".$m04_access_id."' and access_type = '".$m04_access_type."')");

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
    $stmt = $conn->prepare("delete from wms_access_crud where access_id = '$access_id' and access_type = '$access_type' and access_value = '$role'");
    $stmt->execute();	
}


//접근권한관리 > 관리목록 대상 오름정렬시키기
function list_up_sorting($num){
    global $conn;
	$num_under = $num - 1;
    $stmt = $conn->prepare("update wms_access_crud set access_order = 0 where access_order = $num_under");
    $stmt->execute();	
	
    $stmt = $conn->prepare("update wms_access_crud set access_order = $num_under  where access_order = $num");
    $stmt->execute();		
	
    $stmt = $conn->prepare("update wms_access_crud set access_order = $num  where access_order = 0");
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
	
    $stmt = $conn->query("SELECT *  FROM wms_setting where set_id =".$set_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  
 //  업데이트 중복검사  
function update_set_cnt($set_id,$set_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(set_id),'0') as item_cnt FROM wms_setting WHERE   set_id <> ".$set_id." and set_name = '".$set_name."'");
    $stmt->execute(); 
    $item_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
	return $item_cnt;
} 

 
 // 시스템항목명 업데이트  
function update_set($set_id,$set_var,$col) {
    global $conn;
	    if ($col == "set_name") {
			$stmt = $conn->prepare("update wms_setting set set_name = :set_name, set_rdate = :set_rdate where set_id = :set_id");
			$stmt->bindParam(':set_id', $set_id);
			$stmt->bindParam(':set_name', $set_var);
			$stmt->bindParam(':set_rdate',date("Y-m-d H:i:s"));
			$stmt->execute();						
	    }
	    if ($col == "set_comment") {
			$stmt = $conn->prepare("update wms_setting set set_comment = :set_comment, set_rdate = :set_rdate where set_id = :set_id");
			$stmt->bindParam(':set_id', $set_id);
			$stmt->bindParam(':set_comment', $set_var);
			$stmt->bindParam(':set_rdate',date("Y-m-d H:i:s"));
			$stmt->execute();						
	    }
}
 
// Add 시스템설정 항목추가
function add_setsys_col($set_name, $set_comment) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO wms_setting (set_id, set_name, set_comment, set_rdate) VALUES ('0',:set_name, :set_comment, :set_rdate)");
    $stmt->bindParam(':set_name', $set_name);
    $stmt->bindParam(':set_comment', $set_comment);
    $stmt->bindParam(':set_rdate',date("Y-m-d H:i:s"));
    $stmt->execute();	
	
    $stmt = $conn->prepare(" UPDATE wms_setting JOIN ( SELECT MAX(set_id) + 1 AS next_set_id FROM wms_setting ) AS max_set_id SET wms_setting.set_id = max_set_id.next_set_id WHERE wms_setting.set_id = 0");
    $stmt->execute();			
}
 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 슬라이드메뉴 * //

// 관리자모드 현접속자 권한명 출력
function cate_name(){
    global $conn;	
	$stmt = $conn->query("SELECT c.cate_name as cate_name  FROM wms_admin_cate c join wms_admin a on c.cate_admin_role = a.admin_role WHERE  a.admin_id = '".$_SESSION['admin_id']."'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 공통다중사용 * //

// 창고앵글 일괄삭제 setting 값 가져오기
function getwms_setting_state($set_id) {
    global $conn;
    $stmt = $conn->query("SELECT set_state FROM wms_setting where set_id = ".$set_id);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  
 // 운영자 비밀번호 초기화
function reset_pw($admin_id) {
    global $conn;
    $admin_pw = password_hash('1234', PASSWORD_DEFAULT);	
    $stmt = $conn->prepare("update wms_admin set admin_pw = '".$admin_pw."' where admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
}
 
 // 운영자 비밀번호 변경
function change_pw($admin_id,$admin_pw) {
    global $conn;
    $admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);	
    $stmt = $conn->prepare("update wms_admin set admin_pw = '".$admin_pw."' where admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
}


function permission_ck($where,$type,$who){
    global $conn;
    $stmt = $conn->query("select CASE  WHEN COUNT(*) = 0 THEN 'F'  ELSE  'T' END AS pm_rst from  `wms_access_crud` where (access_name ='$where' and access_type = '$type' and access_value = $who) or ('100' = '$who')");
    $result= $stmt->fetchColumn();
	return $result['pm_rst'];

}

// 제품분류명 가져오기
function cate_id_to_cate_name($item_cate){
    global $conn;
    $stmt = $conn->query("SELECT cate_name FROM wms_cate where cate_id = '".$item_cate."'");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
} 
 
// 입출고 거래처명 가져오기
function company_id_to_company_name($item_cate){
    global $conn;
    $stmt = $conn->query("SELECT cate_name FROM wms_company where cate_id = '".$item_cate."'");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}
 
// 입출고 거래처 ID 가져오기
function company_name_to_company_id($item_cate){
    global $conn;
    $stmt = $conn->query("SELECT cate_id FROM wms_company where cate_name = '".$item_cate."'");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}

// 창고 목록 가져오기
function getwms_warehouses($start_record_number,$itemsPerPage,$search,$SearchString) {
    global $conn;
	
	// 검색조건
	$condition_sql = "";
	if ($SearchString!="") {
		$condition_sql = " where ".$search." like '%".$SearchString."%' ";	
	}else{
		$condition_sql = " where 1=1 ";	
	}
	
   // $stmt = $conn->query("SELECT * FROM wms_warehouses  ".$condition_sql." and delYN = 'N' limit $start_record_number,$itemsPerPage");
    $stmt = $conn->query("SELECT warehouse_id, warehouse_code, warehouse_name, warehouse_rdate, IFNULL(( SELECT sum( IFNULL( (SELECT sum(quantity) as sum_quantity FROM wms_stock where warehouse_id = a.warehouse_id and angle_id = a.angle_id ), 0)  )  as sum_quantity  FROM wms_angle a 	where a.delYN = 'N' and a.warehouse_id = w.warehouse_id  ) , 0) as sum_quantity_warehouse, ( select count(angle_id) from wms_angle where angle_id <> 0 and warehouse_id = w.warehouse_id and delYN='N')  as angle_cnt FROM wms_warehouses w ".$condition_sql." and warehouse_id <> 0 and delYN = 'N' limit $start_record_number,$itemsPerPage");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
 // 창고명 업데이트 중복검사  
function update_warehouse_cnt($warehouse_id,$warehouse_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(warehouse_id),'0') as warehouse_cnt FROM `wms_warehouses` WHERE  delYN = 'N'  and warehouse_id <> ".$warehouse_id." and warehouse_name = '".$warehouse_name."'");
    $stmt->execute(); 
    $warehouse_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $warehouse_cnt;
}  

// 창고명 업데이트  
function update_warehouse($warehouse_id,$warehouse_name) {
    global $conn;
	
		$warehouse_name_before = warehouse_id_to_warehouse_name($warehouse_id);
	
		$stmt = $conn->prepare("update wms_warehouses set warehouse_name = :warehouse_name, warehouse_rdate = :warehouse_rdate where  delYN = 'N'  and warehouse_id = :warehouse_id");
		$stmt->bindParam(':warehouse_id', $warehouse_id);
		$stmt->bindParam(':warehouse_name', $warehouse_name);
		$stmt->bindParam(':warehouse_rdate',date("Y-m-d H:i:s"));
		$stmt->execute();	
		
	   add_history('B','창고명을 변경',$warehouse_name_before[0]['warehouse_name'],$warehouse_name);			
}
  

function getwms_angle_namelist($warehouse_id) {
    global $conn;
 
    //$stmt = $conn->query("SELECT  *  FROM wms_angle where warehouse_id = ".$warehouse_id." order by angle_order desc");
    $stmt = $conn->query("SELECT a.angle_id as angle_id, a.angle_name as angle_name, a.warehouse_id as warehouse, a.angle_use as angle_use, a.angle_rdate as rdate, a.angle_order as angle_order,  IFNULL((SELECT sum(quantity) as sum_quantity FROM wms_stock where warehouse_id = ".$warehouse_id." and angle_id = a.angle_id) , 0) as sum_quantity FROM wms_angle a where delYN = 'N' and warehouse_id = ".$warehouse_id." order by angle_order desc");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);	
} 


// 앵글로 재고이동 또는 제품입고등록시 거래처리스트 불러오기
function getwms_company_namelist() {
    global $conn;
 
    //$stmt = $conn->query("SELECT  *  FROM wms_angle where warehouse_id = ".$warehouse_id." order by angle_order desc");
    $stmt = $conn->query("SELECT cate_id, cate_name FROM wms_company where delYN = 'N' order by cate_name asc");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}

 // 제품명 이름추출
function item_id_to_item_name($item_id){
    global $conn;
    $stmt = $conn->query("SELECT item_name FROM wms_items where item_id = $item_id");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
} 
 
 
// JSON 형식의 데이터를 받는 함수
function user_info() {
    // 요청이 POST일 경우에만 처리
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // JSON 데이터 읽기
        $json_data = file_get_contents('php://input');
        // JSON 데이터를 배열로 변환
        $data = json_decode($json_data, true);

        // 배열에서 필요한 데이터 추출
        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;

        // 필요한 정보를 배열로 리턴
        return [
            'username' => $username,
            'email' => $email
        ];
    } else {
        // POST 요청이 아닌 경우 null 리턴
        return null;
    }
}
 
 
 // 히스토리 추가 
 function add_history($h_type,$h_action,$h_col1,$h_col2){
	 
    session_start(); 
	
    global $conn;
	
	$token = token_auth();
	
	if ($token!="") {
		$tk_userinfo = ck_token_user($token);	
		
		$_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
		$_SESSION['user_name'] = $tk_userinfo[0]['user_name'];	
	}
 
	$date = date("Y-m-d H:i:s");	
	// 히스토리추가
	if ($h_action=="앵글내 제품목록 조회") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고의 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="앵글을 삽입") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고에 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="앵글을 삭제") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고의 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="창고를 등록") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','','".$h_action."','m02')");		
	}else if ($h_action=="창고를 삭제") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','','".$h_action."','m02')");		
	}else if ($h_action=="창고명을 변경") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','B', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','".$h_col2."','".$h_action."','m02')");		
	}else if ($h_action=="앵글명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','B', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','".$h_col2."','".$h_action."','m02')");		
	}else if ($h_action=="로그인 성공") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$h_col2."','".$h_col1."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		
	}else if ($h_action=="로그인 실패") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$h_col1."','".$h_col2."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		
	}else if ($h_action=="로그아웃") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		
	}else if ($h_action=="제품을 등록") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','C', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품의 분류를 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','B', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','B', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품을 삭제") {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','C', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if (($h_action=="창고 입고등록") || ($h_action=="창고 입고등록")) {	
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','C', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
	}else if ($h_action=="제품분류를 등록") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1." 이름으로 ".$h_col2."','','".$h_action."','m03')");		
	}else if ($h_action=="제품분류명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','B', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품분류명을 삭제") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="재고이동") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','C', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','재고관리','".$h_col1."','".$h_col2."','".$h_action."','m04')");		
	}else if ($h_action=="앵글로 이동") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','C', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','재고관리','".$h_col1."','".$h_col2."','".$h_action."','m04')");		
	}else if ($h_action=="거래처를 등록") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플', 'A', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1." 이름으로 ".$h_col2."','','".$h_action."','m06')");		
	}else if ($h_action=="거래처명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','B', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
	}else if ($h_action=="출고완료") {
		$stmt_history = $conn->prepare("INSERT INTO wms_history (h_gubun, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('어플','C', '".$_SESSION['user_name']."','".$_SESSION['user_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
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

		$pagination .= '<a href="' . $url . '?page=1&itemsPerPage='.$itemsPerPage.'" ><span class="glyphicon glyphicon glyphicon-backward" aria-hidden="true"></span></a> ';

    // 이전 페이지 링크
    if ($prevPage !== null) {
        $pagination .= '<a href="' . $url . '?page=' . $prevPage . '&itemsPerPage='.$itemsPerPage.'" ><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>';
    }else{
        $pagination .= '<a href="' . $url . '?page=1&itemsPerPage='.$itemsPerPage.'" ><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>';
    }

    // 페이지 번호 링크
    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = ($i == $currentPage) ? ' current' : '';
        $pagination .= '<a href="' . $url . '?page=' . $i . '&itemsPerPage='.$itemsPerPage.'" class="num' . $activeClass . '" style="font-size:18px;color:#666;text-decoration: none"><span> ' . $i . ' </span></a>';
    }

    // 다음 페이지 링크
    if ($nextPage !== null) {
        $pagination .= '<a href="' . $url . '?page=' . $nextPage . '&itemsPerPage='.$itemsPerPage.'" ><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a> ';
    }else{
		if ($nextPage=="") {$nextPage=$totalPages;}
        $pagination .= '<a href="' . $url . '?page=' . $nextPage . '&itemsPerPage='.$itemsPerPage.'" ><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a> ';
    }

		$pagination .= '<a href="' . $url . '?page='.$totalPages.'&itemsPerPage='.$itemsPerPage.'"  ><span class="glyphicon glyphicon glyphicon-forward" aria-hidden="true"></span></a> ';

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