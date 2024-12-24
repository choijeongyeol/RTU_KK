<?php

//String _login = '1';
// _importInvenDateAll = '14'; 완료


require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

class UserManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 사용자 등록
    public function registerUser($user_id, $user_pw, $user_role) {
        $user_pw = password_hash($user_pw, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO RTU_user (user_id, user_pw, user_role) VALUES (:user_id, :user_pw, :user_role)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_pw', $user_pw);
        $stmt->bindParam(':user_role', $user_role);
        $stmt->execute();
    }





    // 사용자 로그인(안씀. 다른용도 스웨거로그인)
    public function loginUser($partner_id, $user_id, $user_pw) {
	
 
		
        $stmt = $this->conn->prepare("SELECT * FROM RTU_user WHERE user_id = :user_id");
       // $stmt->bindParam(':partner_id', $partner_id);
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

			echo "<script>location.href='/swagger-ui-rtu/index.html';</script>";
        } else {

			// exit();	 
            return false;
        }
    }
 
 
 

    // 사용자 로그인
   // public function loginUser2($partner_id, $user_id, $user_pw) {
    public function loginUser2($user_id, $user_pw,$user_fcm) {
		
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
 
		try {
			// 사용자 조회 쿼리
			$stmt = $this->conn->prepare("SELECT * FROM RTU_user WHERE user_id = :user_id");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($user) {
				// 사용자 존재 시 업데이트 쿼리 실행
				$updateSql = "UPDATE RTU_user SET user_fcm = :user_fcm WHERE user_id = :user_id";
				$updateStmt = $this->conn->prepare($updateSql);
				$updateStmt->bindParam(':user_id', $user_id);
				$updateStmt->bindParam(':user_fcm', $user_fcm);
				$updateStmt->execute();
			} else {
				// 사용자 존재하지 않을 경우
				http_response_code(404);
				header('Content-Type: application/json');
				echo json_encode([
					'header' => [
						'resultCode' => 404,
						'codeName' => 'NOT_FOUND_USER',
						'message' => 'User not found.'
					],
					'body' => [
						'data' => null
					]
				]);
				exit;
			}
		} catch (PDOException $e) {
			// 예외 처리
			error_log('Database Error: ' . $e->getMessage());
			http_response_code(500);
			header('Content-Type: application/json');
			echo json_encode([
				'header' => [
					'resultCode' => 500,
					'codeName' => 'INTERNAL_SERVER_ERROR',
					'message' => 'An error occurred while processing the request.'
				]
			]);
			exit;
		}

 


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
			//add_history('A','로그인 성공',$user['user_id'],$user['user_name']);   


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
							"partner_id" => $user['partner_id'],
							"user_id" => $user['user_id'],
							"user_name" => $user['user_name'],
							"user_pw" => $user['user_pw'],
							"user_tel" => $user['user_tel'],
							"user_addr" => $user['user_addr'],
							"user_email" => $user['user_email'],
							"user_role" => $user['user_role'],
							"sms_receive" => $user['sms_receive'],
							"email_receive" => $user['email_receive'],
							"user_rdate" => $user['user_rdate'],
							"user_token" => $user['user_token'],
							"user_fcm" => $user_fcm,
							"user_use" => $user['user_use'],
							"delYN" => $user['delYN'],
							"created_at" => $user['created_at'],
							"updated_at" => $user['updated_at']
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
			//add_history('A','로그인 실패','사용자',$user_id);	
			
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

        $stmt = $this->conn->prepare("SELECT user_role FROM RTU_user WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();  //$userRole = $stmt->fetchColumn();
       
        $userRole = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        return $userRole; // === $requiredRole;
    }
	


	 
	
    // RTU_6431 테이블의 데이터를 배열로 반환하는 함수
    public function listRTU6431() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM RTU_6431");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 데이터를 배열로 반환
            return $rows;

        } catch (PDOException $e) {
            return "데이터를 가져오는 데 실패했습니다.";
        }
    }	
	
    // RTU_SolarInputData 단상
    public function list_SolarInputData_single() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM RTU_SolarInputData where energy_type = '0101' order by id desc");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 데이터를 배열로 반환
            return $rows;

        } catch (PDOException $e) {
            return "데이터를 가져오는 데 실패했습니다.";
        }
    }	
	
    // RTU_SolarInputData 삼상
    public function list_SolarInputData_multi() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM RTU_SolarInputData where energy_type = '0102' and subscription_key ='".$_SESSION['subscription_key']."' order by id desc");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 데이터를 배열로 반환
            return $rows;

        } catch (PDOException $e) {
            return "데이터를 가져오는 데 실패했습니다.";
        }
    }	
	
	
}
 
$userManager = new UserManager($conn);
 
// API_NUM 선언 // 클라이언트에서 POST로 전송된 데이터 받기
$API_NUM = 0;
$token = 0; 

if (isset($_GET['API_NUM'])) {	$API_NUM = $_GET['API_NUM']; }
if ($API_NUM == 0) { $API_NUM = $_POST['API_NUM'];  }
if (isset($_POST['token'])) { $token = $_POST['token'];  } 
 
if ($API_NUM==1) {	session_start(); }
    
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
			//$partner_id = $_POST['partner_id'];
			
			//exit();
			$user_id = $_POST['user_id'];
			$user_pw = $_POST['user_pw']; 
			
			if (isset($_GET['user_fcm'])) {
				$user_fcm = $_GET['user_fcm'];
			} elseif (isset($_POST['user_fcm'])) {
				$user_fcm = $_POST['user_fcm'];
			} else {
				$user_fcm = null;
			}
			
			//$userManager->loginUser2($partner_id, $user_id, $user_pw);
			$userManager->loginUser2($user_id, $user_pw,$user_fcm);
			break;
			
		case 1001 : 

			if (isset($_GET['user_id'])) {
				$user_id = $_GET['user_id'];
			} elseif (isset($_POST['user_id'])) {
				$user_id = $_POST['user_id'];
			} else {
				$user_id = null;
			}
			if (isset($_GET['user_fcm'])) {
				$user_fcm = $_GET['user_fcm'];
			} elseif (isset($_POST['user_fcm'])) {
				$user_fcm = $_POST['user_fcm'];
			} else {
				$user_fcm = null;
			}
			api1001_fcm_update($user_id, $user_fcm);
			break;
			
		case 1002 : 
			// 아이디 찾기 API 처리	
			//$partner_id = $_POST['partner_id'];
			$user_name = $_GET['user_name'];
			$user_tel = $_GET['user_tel']; 
			login_id_search($user_name, $user_tel);
			break;
			
		case 1003 : 
			// 아이디 찾기 API 처리	
			//$partner_id = $_POST['partner_id'];
			$user_name = $_GET['user_name'];
			$user_id = $_GET['user_id']; 
			$user_tel = $_GET['user_tel']; 
			login_pw_reset1($user_name, $user_id, $user_tel);
			break;
			
		case 1004 : 
			// 아이디 찾기 API 처리	
			//$partner_id = $_POST['partner_id'];
			$user_name = $_GET['user_name'];
			$user_id = $_GET['user_id']; 
			$user_tel = $_GET['user_tel']; 
			$user_pw = $_GET['user_pw']; 
			login_pw_reset2($user_name, $user_id, $user_tel, $user_pw);
			break;

		case 1005 : 
			// 유저 FCM	
			//$partner_id = $_POST['partner_id'];
			$user_name = $_GET['user_name'];
			$user_id = $_GET['user_id']; 
			$user_tel = $_GET['user_tel']; 
			$user_fcm = $_GET['user_fcm']; 
			login_fcm($user_name, $user_id, $user_tel, $user_fcm);
			break;
			
	
		case 9001 : // 파트너 생성 함수		
				    create_partner($partner_name, $admin_id, $partner_address, $partner_contact);
			break;

	
		case 2 : // 통계분석, 하루 시간대별 표시	
			if(isset($_GET['page'])){$start_record_number=$_GET['page'];}else{$start_record_number=1;}
			if(isset($_GET['itemsPerPage'])){$itemsPerPage=$_GET['itemsPerPage'];}else{$itemsPerPage=10;}
			if(isset($_GET['search_date'])){$search_date=$_GET['search_date'];}else{$search_date="";}

			api2_stat_realtime($start_record_number,$itemsPerPage,$search_date);
			break;
		case 3 : 
			if(isset($_GET['page'])){$start_record_number=$_GET['page'];}else{$start_record_number=1;}
			if(isset($_GET['itemsPerPage'])){$itemsPerPage=$_GET['itemsPerPage'];}else{$itemsPerPage=10;}
			if(isset($_GET['startDate'])){$searchStartDate=$_GET['startDate'];}else{$searchStartDate="";}
			if(isset($_GET['endDate'])){$searchEndDate=$_GET['endDate'];}else{$searchEndDate="";}

			api3_stat_daily($start_record_number, $itemsPerPage, $searchStartDate, $searchEndDate);
			break;
		
		case 6001 : 
			// RTU 모니터링 홈 API 오늘발전량
			if(isset($_GET['page'])){$start_record_number=$_GET['page'];}else{$start_record_number=0;}
			if(isset($_GET['itemsPerPage'])){$itemsPerPage=$_GET['itemsPerPage'];}else{$itemsPerPage=10;}
			if(isset($_GET['startDate'])){$searchStartDate=$_GET['startDate'];}else{$searchStartDate="";}
			if(isset($_GET['endDate'])){$searchEndDate=$_GET['endDate'];}else{$searchEndDate="";}
			if(isset($_GET['search'])){$search=$_GET['search'];}else{$search="";}
			if(isset($_GET['keyword'])){$search=$_GET['keyword'];}else{$keyword="";}
			
			api6001_today_bjr_tot($start_record_number,$itemsPerPage,$search,$keyword,$searchStartDate,$searchEndDate);
			break;			
		case 6002 : 
			// RTU 모니터링 홈 API 어제발전량
			if(isset($_GET['page'])){$start_record_number=$_GET['page'];}else{$start_record_number=0;}
			if(isset($_GET['itemsPerPage'])){$itemsPerPage=$_GET['itemsPerPage'];}else{$itemsPerPage=10;}
			if(isset($_GET['startDate'])){$searchStartDate=$_GET['startDate'];}else{$searchStartDate="";}
			if(isset($_GET['endDate'])){$searchEndDate=$_GET['endDate'];}else{$searchEndDate="";}
			if(isset($_GET['search'])){$search=$_GET['search'];}else{$search="";}
			if(isset($_GET['keyword'])){$search=$_GET['keyword'];}else{$keyword="";}
			
			api6002_yesterday_bjr_tot($start_record_number,$itemsPerPage,$search,$keyword,$searchStartDate,$searchEndDate);
			break;	
			
		case 6003 : 
			// RTU 모니터링 홈 API 오늘발전량 예상
			if(isset($_GET['page'])){$start_record_number=$_GET['page'];}else{$start_record_number=0;}
			if(isset($_GET['itemsPerPage'])){$itemsPerPage=$_GET['itemsPerPage'];}else{$itemsPerPage=10;}
			if(isset($_GET['startDate'])){$searchStartDate=$_GET['startDate'];}else{$searchStartDate="";}
			if(isset($_GET['endDate'])){$searchEndDate=$_GET['endDate'];}else{$searchEndDate="";}
			if(isset($_GET['search'])){$search=$_GET['search'];}else{$search="";}
			if(isset($_GET['keyword'])){$search=$_GET['keyword'];}else{$keyword="";}
			
			api6003_today_bjr_will($start_record_number,$itemsPerPage,$search,$keyword,$searchStartDate,$searchEndDate);
			break;	
			
		case 6004 : 
			// RTU 모니터링 홈 API 오늘발전량 예상
			if(isset($_GET['page'])){$start_record_number=$_GET['page'];}else{$start_record_number=0;}
			if(isset($_GET['itemsPerPage'])){$itemsPerPage=$_GET['itemsPerPage'];}else{$itemsPerPage=10;}
			if(isset($_GET['startDate'])){$searchStartDate=$_GET['startDate'];}else{$searchStartDate="";}
			if(isset($_GET['endDate'])){$searchEndDate=$_GET['endDate'];}else{$searchEndDate="";}
			if(isset($_GET['search'])){$search=$_GET['search'];}else{$search="";}
			if(isset($_GET['keyword'])){$search=$_GET['keyword'];}else{$keyword="";}
			
			api6004_today_bjtime_will($start_record_number,$itemsPerPage,$search,$keyword,$searchStartDate,$searchEndDate);
			break;	
			
		case 6000 : 
			// RTU 모니터링 홈 API SET
			if(isset($_GET['page'])){$start_record_number=$_GET['page'];}else{$start_record_number=0;}
			if(isset($_GET['itemsPerPage'])){$itemsPerPage=$_GET['itemsPerPage'];}else{$itemsPerPage=10;}
			if(isset($_GET['startDate'])){$searchStartDate=$_GET['startDate'];}else{$searchStartDate="";}
			if(isset($_GET['endDate'])){$searchEndDate=$_GET['endDate'];}else{$searchEndDate="";}
			if(isset($_GET['search'])){$search=$_GET['search'];}else{$search="";}
			if(isset($_GET['keyword'])){$search=$_GET['keyword'];}else{$keyword="";}
			
			api6000_set($start_record_number,$itemsPerPage,$search,$keyword,$searchStartDate,$searchEndDate);
			break;				
		case 7000 : 

			 $page = 1;
			 if (isset($_GET['page'])) { $page = $_GET['page'];}	 
						
			// RTU 장애이력 홈 API SET
			if (isset($_GET['start_record_number'])) {
				$start_record_number = $_GET['start_record_number'];
			} else {
				$start_record_number = 0;
			}
			
			if (isset($_GET['itemsPerPage'])) {
				$itemsPerPage = $_GET['itemsPerPage'];
			} else {
				$itemsPerPage = 10;
			}
			
			$start_record_number = ($page-1)*$itemsPerPage;
			
			
			
			if (isset($_GET['startDate'])) {
				$startDate = $_GET['startDate'];
			} else {
				$startDate = "";
			}
			
			if (isset($_GET['endDate'])) {
				$endDate = $_GET['endDate'];
			} else {
				$endDate = "";
			}
			
			if (isset($_GET['search'])) {
				$search = $_GET['search'];
			} else {
				$search = "";
			}
			
			if (isset($_GET['keyword'])) {
				$keyword = $_GET['keyword'];
			} else {
				$keyword = "";
			}

			if (isset($_GET['status'])) {
				$status = $_GET['status'];
			} else {
				$status = "전체";
			}
			api7000_set($start_record_number, $itemsPerPage, $search, $keyword, $startDate, $endDate, $status);
			break;				
			
		case 7001 : 	
			if(isset($_GET['issue_id'])){$issue_idx=$_GET['issue_id'];}else{$issue_idx="";echo "필수항목 누락";exit();}
			api7001_set($issue_idx); 
			break;				
			
		case 7002 : 	
			if (isset($_GET['issue_id'])) {
				$issue_id = $_GET['issue_id'];
			} elseif (isset($_POST['issue_id'])) {
				$issue_id = $_POST['issue_id'];
			} else {
				$issue_id = "";
				echo "issue_id 필수항목 누락";
				exit();
			}

			if (isset($_GET['notes'])) {
				$notes = $_GET['notes'];
			} elseif (isset($_POST['notes'])) {
				$notes = $_POST['notes'];
			} else {
				$notes = "";
				echo "notes 필수항목 누락";
				exit();
			}

			if (isset($_GET['technician_id'])) {
				$technician_id = $_GET['technician_id'];
			} elseif (isset($_POST['technician_id'])) {
				$technician_id = $_POST['technician_id'];
			} else {
				$technician_id = null;
			}

			if (isset($_GET['status'])) {
				$status = $_GET['status'];
			} elseif (isset($_POST['status'])) {
				$status = $_POST['status'];
			} else {
				$status = 2;
			}

			// 첨부파일 처리
			$saved_file_name = null;
			$original_file_name = null;

			if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
				// 원래 파일 이름
				$original_file_name = $_FILES['attachment']['name'];

				// 저장될 파일 이름 (중복 방지를 위해 타임스탬프 + 유니크 아이디)
				$saved_file_name = time() . '_' . uniqid() . '.' . pathinfo($original_file_name, PATHINFO_EXTENSION);

				// 파일 저장 디렉토리
				$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
				if (!is_dir($upload_dir)) {
					mkdir($upload_dir, 0777, true);
				}

				// 파일 저장
				$file_path = $upload_dir . $saved_file_name;
				if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
					$saved_file_name = null; // 저장 실패 시 초기화
					$original_file_name = null;
				}
			}

			// 함수 호출
			api7002_set($issue_id, $notes, $technician_id, $status, $saved_file_name, $original_file_name);
			break;	
			
		case 7003 : 
			if(isset($_GET['as_id'])){
				$as_id=$_GET['as_id'];
			}elseif(isset($_POST['as_id'])){
				$as_id=$_POST['as_id'];
			}else{
				$as_id="";echo "as_id 필수항목 누락";exit();
			}			
			api7003_set($as_id); 
			break;	
			
			
			
		case 5000 : 	
			
			api5000_set(); 
			break;				
 
		case 5001 : 	
			if(isset($_GET['category_id'])){
				$category_id=$_GET['category_id'];
			}elseif(isset($_POST['category_id'])){
				$category_id=$_POST['category_id'];
			}else{
				$category_id=null;
			}		
			if($category_id==null){
				api5001_faq($category_id = null); 
			}else{
				api5001_faq($category_id); 
			}
			break;				
 
		case 5002 : 	
			if(isset($_GET['inquiry_id'])){
				$inquiry_id=$_GET['inquiry_id'];
			}elseif(isset($_POST['inquiry_id'])){
				$inquiry_id=$_POST['inquiry_id'];
			}else{
				$inquiry_id=null;
			}		
			if($inquiry_id==null){
				api5002_qna($inquiry_id = null); 
			}else{
				api5002_qna($inquiry_id); 
			}
			break;				
 
		case 5003 : 	
			// 요청값 처리
			if (isset($_POST['title'])) {
				$title = trim($_POST['title']);
			} else {
				$title = null;
			}

			if (isset($_POST['content'])) {
				$content = trim($_POST['content']);
			} else {
				$content = null;
			}

			// 첨부파일 처리
			$saved_file_name = null;
			$original_file_name = null;

			if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
				// 원래 파일 이름
				$original_file_name = $_FILES['attachment']['name'];

				// 저장될 파일 이름 (중복 방지를 위해 타임스탬프 + 유니크 아이디)
				$saved_file_name = time() . '_' . uniqid() . '.' . pathinfo($original_file_name, PATHINFO_EXTENSION);

				// 파일 저장 디렉토리
				$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
				if (!is_dir($upload_dir)) {
					mkdir($upload_dir, 0777, true);
				}

				// 파일 저장
				$file_path = $upload_dir . $saved_file_name;
				if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
					// 저장 실패 시 초기화
					$saved_file_name = null;
					$original_file_name = null;
				}
			}

			// 함수 호출
			api_5003_qna_input($title, $content, $saved_file_name, $original_file_name);

			break;				
 
		case 8000 : 	
			//$user_id = $_REQUEST['user_id'] ?? null;
			$cid = $_REQUEST['cid'] ?? null;
			$view_type = $_REQUEST['view_type'] ?? "t_day";
			$date = $_REQUEST['date'] ?? '';
			$date_offset = isset($_GET['date_offset']) && is_numeric($_GET['date_offset']) ? intval($_GET['date_offset']) : 0;

			api8000_set($cid, $view_type, $date, $date_offset);
			break;					
 
 
			
		case 9999 : 
			api9999_set(); 
 			break;	
 
		case 7 : 
			if(isset($_GET['lora_id'])){$lora_id=$_GET['lora_id'];}else{$lora_id="";echo "필수항목 누락";exit();}
			
			if(isset($_GET['page'])){$start_record_number=$_GET['page'];}else{$start_record_number=0;}
			if(isset($_GET['itemsPerPage'])){$itemsPerPage=$_GET['itemsPerPage'];}else{$itemsPerPage=10;}
			if(isset($_GET['startDate'])){$searchStartDate=$_GET['startDate'];}else{$searchStartDate="";}
			if(isset($_GET['endDate'])){$searchEndDate=$_GET['endDate'];}else{$searchEndDate="";}
			
			api7_solar_stat($lora_id, $start_record_number, $itemsPerPage, $searchStartDate, $searchEndDate);
			break;
			
		default :
		   // 처리할 수 없는 API_NUM이 전달된 경우		
			break;
	} 
}
 
function user_id_to_user_name($user_id){
    global $conn;
    $stmt = $conn->query("SELECT user_name FROM RTU_user where user_id = '".$user_id."'");
	return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}

  

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 아이디 찾기 * //    
function login_id_search($user_name, $user_tel) {
    global $conn;
    
    // 토큰 인증 및 검증
   // $token = token_auth();
  //  $tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

   // if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상 진행    

        // 하이픈 없이 전화번호를 저장한 DB의 전화번호와 비교
        $sql = "SELECT * FROM RTU_user WHERE user_name = :user_name AND REPLACE(user_tel, '-', '') = :user_tel";

        try {
            $stmt = $conn->prepare($sql);

            // 바인딩 파라미터
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':user_tel', $user_tel);

            // 쿼리 실행
            $stmt->execute();

            // 결과 모두 가져오기
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 결과가 없는 경우 처리
            if (empty($resources)) {
                // No matching record found
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 666,
                        'codeName' => 'NOT_FOUND'
                    ],
					"body" => [
						"data" => null
					]
                ]);
                return;
            }

            // 성공적인 JSON 응답 (여러 결과 반환)
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => ["data" => $resources]
            ]);

        } catch (PDOException $e) {
            // 쿼리 오류 발생 시 처리
            error_log('Query Error: ' . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => 'Server Error: Could not process the query'
                ]
            ]);
        }
   // } else {
        // 유효하지 않은 토큰 처리
   //     api_error102();
   // }
}

  

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 비밀번호 재설정 1/2 * //    
function login_pw_reset1($user_name, $user_id, $user_tel) {
	
    // URL 인코딩된 값을 디코딩
    //$user_name = urldecode($user_name);
   // $user_tel = urldecode($user_tel);
	
    // 토큰 인증 및 검증
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

    //if ($tk_cnt[0]['cnt'] > 0) {
    if (1==1) {
        // 토큰이 유효함. 정상 진행    
        global $conn;
        $sql = "SELECT * FROM RTU_user WHERE user_name = :user_name AND user_id = :user_id AND REPLACE(user_tel, '-', '') = :user_tel";

        try {
            $stmt = $conn->prepare($sql);

            // 바인딩 파라미터
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':user_tel', $user_tel);

            // 쿼리 실행
            $stmt->execute();

            // 결과 가져오기
            $resources = $stmt->fetch(PDO::FETCH_ASSOC);

            // 결과가 없는 경우 처리
            if ($resources === false) {
                // No matching record found
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 666,
                        'codeName' => 'NOT_FOUND',
                    ],
					"body" => [
						"data" => null
					]
                ]);
                return;
            }

            // 성공적인 JSON 응답
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => ["data" => $resources]
            ]);

        } catch (PDOException $e) {
            // 쿼리 오류 발생 시 처리
            error_log('Query Error: ' . $e->getMessage());
            http_response_code(500);
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
        // 유효하지 않은 토큰 처리
        api_error102();
    }
}
  
    
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 비밀번호 재설정 2/2 * //    
function login_pw_reset2($user_name, $user_id, $user_tel, $user_pw) {
 
    // 토큰 인증 및 검증
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

    //if ($tk_cnt[0]['cnt'] > 0) {
    if (1==1) {
        // 토큰이 유효함. 정상 진행    
        global $conn;

        // 비밀번호 암호화 (예시: password_hash 사용)
        $hashed_pw = password_hash($user_pw, PASSWORD_DEFAULT);

        // SQL 쿼리 (암호화된 비밀번호 저장)
        $sql = "UPDATE RTU_user SET user_pw = :user_pw WHERE user_name = :user_name AND user_id = :user_id AND REPLACE(user_tel, '-', '') = :user_tel";

        try {
            $stmt = $conn->prepare($sql);

            // 바인딩 파라미터
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':user_tel', $user_tel);
            $stmt->bindParam(':user_pw', $hashed_pw);

            // 쿼리 실행
            $stmt->execute();

            // 변경된 행 수 확인
            if ($stmt->rowCount() > 0) {
                // 성공적인 JSON 응답
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS',
                        'message' => 'Password reset successfully'
                    ],
					"body" => [
						"data" => null
					]
                ]);
            } else {
                // No matching record found
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 666,
                        'codeName' => 'NOT_FOUND'
                    ],
					"body" => [
						"data" => null
					]
                ]);
                return;
            }

        } catch (PDOException $e) {
            // 쿼리 오류 발생 시 처리
            error_log('Query Error: ' . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                    ],
					"body" => [
						"data" => null
					]

            ]);
        }
    } else {
        // 유효하지 않은 토큰 처리
        api_error102();
    }
}
      
 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 유저 fcm 업데이트 * //    
function api1001_fcm_update($user_id, $user_fcm) {
 
    // 토큰 인증 및 검증
    //$token = token_auth();
    //$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

    if (1==1){ //($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상 진행    
        global $conn;
 	 
		// 사용자 조회 쿼리
		$checkSql = "SELECT COUNT(*) FROM RTU_user WHERE user_id = :user_id";

		try {			
			$checkStmt = $conn->prepare($checkSql);
			$checkStmt->bindParam(':user_id', $user_id);
			$checkStmt->execute();
			$userExists = $checkStmt->fetchColumn();

			if ($userExists > 0) {
				// 사용자 존재 시 업데이트 쿼리 실행
				$sql = "UPDATE RTU_user SET user_fcm = :user_fcm WHERE user_id = :user_id";

				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':user_fcm', $user_fcm);
				$stmt->execute();
					
				// 사용자 조회 쿼리
				$stmt_u = $conn->prepare("SELECT * FROM RTU_user WHERE user_id = :user_id");
				$stmt_u->bindParam(':user_id', $user_id);
				$stmt_u->execute();
				$user = $stmt_u->fetch(PDO::FETCH_ASSOC);			
 
				// 변경된 행 수 확인
				if ($stmt->rowCount() > 0) {
					// 성공적인 JSON 응답
					http_response_code(200);
					header('Content-Type: application/json');
					echo json_encode([
						'header' => [
							'resultCode' => 200,
							'codeName' => 'SUCCESS',
							'message' => 'User FCM updated successfully.'
						],
						'body' => [
							"user_idx" => $user['user_idx'],
							"partner_id" => $user['partner_id'],
							"user_id" => $user['user_id'],
							"user_name" => $user['user_name'],
							"user_pw" => $user['user_pw'],
							"user_tel" => $user['user_tel'],
							"user_addr" => $user['user_addr'],
							"user_email" => $user['user_email'],
							"user_role" => $user['user_role'],
							"sms_receive" => $user['sms_receive'],
							"email_receive" => $user['email_receive'],
							"user_rdate" => $user['user_rdate'],
							"user_token" => $user['user_token'],
							"user_fcm" => $user_fcm,
							"user_use" => $user['user_use'],
							"delYN" => $user['delYN'],
							"created_at" => $user['created_at'],
							"updated_at" => $user['updated_at']
						]
					]);
				} else {
					// 데이터는 존재하지만 업데이트된 내용이 없는 경우
					http_response_code(200);
					header('Content-Type: application/json');
					echo json_encode([
						'header' => [
							'resultCode' => 200,
							'codeName' => 'SUCCESS',
							'message' => 'User FCM updated successfully.'
						],
						'body' => [
							"user_idx" => $user['user_idx'],
							"partner_id" => $user['partner_id'],
							"user_id" => $user['user_id'],
							"user_name" => $user['user_name'],
							"user_pw" => $user['user_pw'],
							"user_tel" => $user['user_tel'],
							"user_addr" => $user['user_addr'],
							"user_email" => $user['user_email'],
							"user_role" => $user['user_role'],
							"sms_receive" => $user['sms_receive'],
							"email_receive" => $user['email_receive'],
							"user_rdate" => $user['user_rdate'],
							"user_token" => $user['user_token'],
							"user_fcm" => $user_fcm,
							"user_use" => $user['user_use'],
							"delYN" => $user['delYN'],
							"created_at" => $user['created_at'],
							"updated_at" => $user['updated_at']
						]
					]);
				}
			} else {
				// 사용자 존재하지 않음
				http_response_code(404);
				header('Content-Type: application/json');
				echo json_encode([
					'header' => [
						'resultCode' => 404,
						'codeName' => 'NOT_FOUND',
						'message' => 'User not found.'
					],
					'body' => [
						'data' => 0
					]
				]);
			}
		} catch (PDOException $e) {
			// 쿼리 오류 발생 시 처리
			error_log('Query Error: ' . $e->getMessage());
			http_response_code(500);
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
        // 유효하지 않은 토큰 처리
        api_error102();
    }
}
       
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 유저 fcm 업데이트 * //    
function login_fcm($user_name, $user_id, $user_tel, $user_fcm) {
 
    // 토큰 인증 및 검증
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상 진행    
        global $conn;
 
		 // SQL 쿼리 (암호화된 비밀번호 저장)
        $sql = "UPDATE RTU_user SET user_fcm = :user_fcm WHERE user_name = :user_name AND user_id = :user_id AND user_tel = :user_tel";

        try {
            $stmt = $conn->prepare($sql);

            // 바인딩 파라미터
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':user_tel', $user_tel);
            $stmt->bindParam(':user_fcm', $user_fcm);

            // 쿼리 실행
            $stmt->execute();

            // 변경된 행 수 확인
            if ($stmt->rowCount() > 0) {
                // 성공적인 JSON 응답
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS',
                        'message' => 'User fcm update successfully'
                    ],
					"body" => [
						"data" => null
					]
                ]);
            } else {
                // No matching record found
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 666,
                        'codeName' => 'NOT_FOUND'
                    ],
					"body" => [
						"data" => null
					]
                ]);
                return;
            }

        } catch (PDOException $e) {
            // 쿼리 오류 발생 시 처리
            error_log('Query Error: ' . $e->getMessage());
            http_response_code(500);
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
        // 유효하지 않은 토큰 처리
        api_error102();
    }
}
       
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 파트너 생성 함수
function create_partner($partner_name, $admin_id, $partner_address, $partner_contact) {
    global $conn;	
    $sql = "INSERT INTO RTU_Partner (name, admin_id, address, contact) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('siss', $name, $admin_id, $address, $contact);

    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => '파트너가 성공적으로 생성되었습니다.'];
    } else {
        return ['status' => 'error', 'message' => '파트너 생성에 실패했습니다.'];
    }
}

// 유저 등록 함수
function create_user($conn, $partner_id, $user_id, $user_name, $user_pw, $user_tel = null, $user_role = 1, $user_token = null, $user_fcm = null) {
    $hashed_pw = password_hash($user_pw, PASSWORD_DEFAULT);
    $sql = "INSERT INTO RTU_User (partner_id, user_id, user_name, user_pw, user_tel, user_role, user_token, user_fcm) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('issssiss', $partner_id, $user_id, $user_name, $hashed_pw, $user_tel, $user_role, $user_token, $user_fcm);

    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => '유저가 성공적으로 등록되었습니다.'];
    } else {
        return ['status' => 'error', 'message' => '유저 등록에 실패했습니다.'];
    }
}

// 발전소 등록 함수
function create_powerplant($conn, $partner_id, $name, $location, $capacity, $user_id = null) {
    $sql = "INSERT INTO RTU_PowerPlant (name, location, capacity, partner_id, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('ssdii', $name, $location, $capacity, $partner_id, $user_id);

    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => '발전소가 성공적으로 등록되었습니다.'];
    } else {
        return ['status' => 'error', 'message' => '발전소 등록에 실패했습니다.'];
    }
}

// RTU 등록 함수
function create_rtu($conn, $power_plant_id, $name, $model, $install_date, $status = 'active') {
    $sql = "INSERT INTO RTU_RTU (name, model, install_date, power_plant_id, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('sssii', $name, $model, $install_date, $power_plant_id, $status);

    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => 'RTU가 성공적으로 등록되었습니다.'];
    } else {
        return ['status' => 'error', 'message' => 'RTU 등록에 실패했습니다.'];
    }
}

// 인버터(설비) 등록 함수
function create_inverter($conn, $rtu_id, $model, $capacity, $efficiency) {
    $sql = "INSERT INTO RTU_Inverter (model, capacity, efficiency, rtu_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam('siii', $model, $capacity, $efficiency, $rtu_id);

    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => '설비가 성공적으로 등록되었습니다.'];
    } else {
        return ['status' => 'error', 'message' => '설비 등록에 실패했습니다.'];
    }
}		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 통계분석 * //

// 일일, 시간대별 분석
function api2_stat_realtime($start_record_number,$itemsPerPage,$search_date) {
		
	$token = token_auth();
	$tk_cnt = ck_token_cnt($token);  // 없으면 0, 있으면 1 이상

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행	
		global $conn;

	   $sql=" SELECT ";
	   $sql=$sql." energy_source, ";
	   $sql=$sql." COUNT(*) AS total_entries, ";
	   $sql=$sql." MAX(cs) AS max_cs, ";
	   $sql=$sql." MIN(cs) AS min_cs, ";
	   $sql=$sql." AVG(cs) AS avg_cs, ";
	   $sql=$sql." SUM(production_time) AS total_production_time,  ";//   -- 총 발전 시간
	   $sql=$sql." SUM(production_amount) AS total_production_amount,  ";//  -- 총 발전량
	   $sql=$sql." SUM(IF(fault_status = 1, 1, 0)) AS fault_count,   ";//    -- 고장 횟수
	   $sql=$sql."  MAX(timestamp) AS latest_timestamp    ";//               -- 가장 최근 타임스탬프
	   $sql=$sql." FROM RTU_real_time_stats  ";
	   
	   if ($search_date!="") {
	   $sql=$sql." WHERE timestamp >= '".$search_date."' and  timestamp < '".$search_date."' + INTERVAL 1 DAY  ";//-- 특정 날짜의 끝 (23:59:59);
	   }else{
	   $sql=$sql." WHERE timestamp >= CURDATE() - INTERVAL - 0 DAY AND timestamp < CURDATE() + INTERVAL 1 DAY ";
	   }
	   
	   $sql=$sql." GROUP BY energy_source ";
	   $sql=$sql."  order by timestamp limit $start_record_number,$itemsPerPage ";
 

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
}
 


// 일별 통계 분석 API (날짜 범위로 조회)
function api3_stat_daily($start_record_number, $itemsPerPage, $searchStartDate, $searchEndDate) {

    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함
        global $conn;

        $sql = "SELECT ";
        $sql .= " energy_source, ";
        $sql .= " total_production_time, ";  // 총 발전 시간
        $sql .= " total_production_amount, ";  // 총 발전량
        $sql .= " fault_count, ";  // 고장 횟수
        $sql .= " MAX(date) AS latest_date ";  // 최근 날짜
        $sql .= " FROM RTU_daily_statistics ";

        // 날짜 범위 필터링
        if ($searchStartDate != "" && $searchEndDate != "") {
            $sql .= " WHERE date BETWEEN :searchStartDate AND :searchEndDate ";
        } else {
            $sql .= " WHERE date = CURDATE() ";  // 오늘 통계
        }

        $sql .= " GROUP BY energy_source ";
        $sql .= " ORDER BY date DESC LIMIT :start_record_number, :itemsPerPage ";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':searchStartDate', $searchStartDate);
            $stmt->bindParam(':searchEndDate', $searchEndDate);
            $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
            $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
            $stmt->execute();

            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

            convertToNumbers($resources);  // 숫자 변환

            // 성공 응답
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
        // 유효하지 않은 토큰
        api_error102();
    }
}












// 10/21 ///////////////////////////////////////////////////////////////////////////
// 태양광 통계 분석 함수 (발전소별, 날짜별)
function api7_solar_stat($lora_id, $start_record_number, $itemsPerPage, $searchStartDate, $searchEndDate) {
    global $conn;

    $sql = "SELECT ";
    $sql .= " RIGHT(ltid, 16) AS lora_id, ";  // ltid의 마지막 16자를 lora_id로 추출
    $sql .= " COUNT(*) AS total_entries, ";
    $sql .= " MAX(pv_voltage) AS max_voltage, ";
    $sql .= " MIN(pv_voltage) AS min_voltage, ";
    $sql .= " AVG(pv_voltage) AS avg_voltage, ";
    $sql .= " MAX(pv_output) AS max_output, ";
    $sql .= " MIN(pv_output) AS min_output, ";
    $sql .= " AVG(pv_output) AS avg_output, ";
    $sql .= " SUM(cumulative_energy) AS total_energy, ";
    $sql .= " SUM(IF(fault_status = 1, 1, 0)) AS fault_count, ";
    $sql .= " MAX(rdate) AS latest_rdate ";
    $sql .= " FROM RTU_SolarInputData ";

    // 날짜 범위 필터링
    if ($searchStartDate != "" && $searchEndDate != "") {
        $sql .= " WHERE rdate BETWEEN :searchStartDate AND DATE_ADD(:searchEndDate, INTERVAL 1 DAY)";
    } else {
        $sql .= " WHERE rdate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)";  // 오늘 통계
    }

    // ltid 조건 추가 (lora_id를 기반으로 필터링)
    if (!empty($lora_id)) {
        $sql .= " AND RIGHT(ltid, 16) = :lora_id ";
    }

    $sql .= " GROUP BY RIGHT(ltid, 16) ";
    $sql .= " ORDER BY MAX(rdate) LIMIT :start_record_number, :itemsPerPage ";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':lora_id', $lora_id);
        $stmt->bindParam(':searchStartDate', $searchStartDate);
        $stmt->bindParam(':searchEndDate', $searchEndDate);
        $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
        $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

        convertToNumbers($resources);  // 숫자 변환
        
        // SQL과 파라미터를 JSON에 포함하여 응답
        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 200,
                'codeName' => 'SUCCESS',
                'sql' => $sql,
                'params' => [$lora_id, $searchStartDate, $searchEndDate, $start_record_number, $itemsPerPage]
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
}

 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 오늘 발전시간 총합계 가져오기 <수정 : 누적으로 변경>
function api6001_today_bjr_tot($start_record_number, $itemsPerPage, $search, $keyword, $searchStartDate, $searchEndDate) {
    session_start(); 
	
    global $conn;
	
	$token = token_auth();
	
	if ($token!="") {
		$tk_userinfo = ck_token_user($token);	
		
		$_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
		$_SESSION['user_name'] = $tk_userinfo[0]['user_name'];	
	}
	
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행	
 
		// 기본 SQL 쿼리와 조건 초기화
			$sql = "  ";
			$sql .= "  SELECT   ";//
			$sql .= "      DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS today,    ";//-- 날짜를 'YYYY-MM-DD' 형식으로 변환
			$sql .= "      SUM(s.pv_output) / 1000 AS today_total_energy_kW,   ";// -- 전체 pv_output 값을 킬로와트로 변환하여 합산
			$sql .= "      TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes    ";//-- 시작과 종료 시간의 차이를 분 단위로 계산
			$sql .= "  FROM RTU_SolarInputData s   ";//
			$sql .= "  JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id   ";// -- RTU_SolarInputData와 RTU_facility 조인
			$sql .= "  JOIN RTU_user u ON f.user_id = u.user_id    ";//-- RTU_facility와 RTU_user 조인
			$sql .= "   WHERE u.user_id = '".$_SESSION['user_id']."'     ";//-- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
			$sql .= "  AND s.energy_type IN ('0101', '0102')   ";// -- 태양광 (단상, 삼상) 필터링
			$sql .= "  AND s.fault_status = 0  ";//-- 발전 정상
			$sql .= "  AND s.pv_output > 0    ";//-- 발전량이 0보다 큰 경우만
			$sql .= "  AND DATE(s.rdate) = CURDATE();   ";// -- 오늘 날짜 필터링			
			
		// <수정> pv_output 에서 >> 누적발전량 차이로 계산 cumulative_energy	
			$sql = " 
					 SELECT         
						DATE_FORMAT(CURDATE(), '%Y-%m-%d') AS today,
						SUM(cid_energy.today_total_energy_kW) AS today_total_energy_kW,
						SUM(cid_energy.generation_minutes) / COUNT(cid_energy.cid) AS generation_minutes

					FROM (
						SELECT 
							f.cid,
							(MAX(s.cumulative_energy) - MIN(s.cumulative_energy)) / 1000 AS today_total_energy_kW,
							TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes
						FROM RTU_SolarInputData s     
						JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id     
						JOIN RTU_user u ON f.user_id = u.user_id      
						WHERE u.user_id = '".$_SESSION['user_id']."'      
						  AND s.energy_type IN ('0101', '0102')    
						  AND s.fault_status = 0    
						  AND s.cumulative_energy > 0      
						  AND DATE(s.rdate) = CURDATE()
						GROUP BY f.cid
					) AS cid_energy;
                   ";
 
 
 
			
		/*$conditions = [];
		//$params = [];

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
        */
 

		try {
			// SQL 실행
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			
			// Fetching the results
			$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			// 만약 데이터가 있다면
			if (!empty($resources)) {
				foreach ($resources as &$resource) {
					// 총 분 계산 (예: total_minutes 필드가 있다고 가정)
					$total_minutes = $resource['generation_minutes'];  // SQL에서 계산된 분 단위 발전 시간
					$hours = floor($total_minutes / 60);  // 시간 계산
					$minutes = $total_minutes % 60;  // 나머지 분 계산
					
					// 시간과 분을 "시간:분" 형식으로 변환하여 추가
					$resource['generation_time'] = $hours . "시간 " . $minutes . "분";
					
					// 필요에 따라 다른 데이터 처리도 가능
				}
			}

			// Fetch된 데이터 확인
			//var_dump($resources); // or print_r($resources);
			//exit();  // 데이터를 확인한 후 종료

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
			// Query Error Handling
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



// 어제 발전시간 총합계 가져오기 <수정> pv_output 에서 >> 누적발전량 차이로 계산 cumulative_energy
function api6002_yesterday_bjr_tot($start_record_number, $itemsPerPage, $search, $keyword, $searchStartDate, $searchEndDate) {
    session_start(); 
	
    global $conn;
	
	$token = token_auth();
	
	if ($token!="") {
		$tk_userinfo = ck_token_user($token);	
		
		$_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
		$_SESSION['user_name'] = $tk_userinfo[0]['user_name'];	
	}
	
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행	
 
		// 기본 SQL 쿼리와 조건 초기화
			$sql = "  ";
			$sql .= "  SELECT   ";//
			$sql .= "      DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS day,    ";//-- 날짜를 'YYYY-MM-DD' 형식으로 변환
			$sql .= "      SUM(s.pv_output) / 1000 AS day_total_energy_kW,   ";// -- 전체 pv_output 값을 킬로와트로 변환하여 합산
			$sql .= "      TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes    ";//-- 시작과 종료 시간의 차이를 분 단위로 계산
			$sql .= "  FROM RTU_SolarInputData s   ";//
			$sql .= "  JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id   ";// -- RTU_SolarInputData와 RTU_facility 조인
			$sql .= "  JOIN RTU_user u ON f.user_id = u.user_id    ";//-- RTU_facility와 RTU_user 조인
			$sql .= "   WHERE u.user_id = '".$_SESSION['user_id']."'     ";//-- 특정 user_id 필터링 (BN241017-0001 사용자의 모든 LoRa ID)
			$sql .= "  AND s.energy_type IN ('0101', '0102')   ";// -- 태양광 (단상, 삼상) 필터링
			$sql .= "  AND s.fault_status = 0  ";//-- 발전 정상
			$sql .= "  AND s.pv_output > 0    ";//-- 발전량이 0보다 큰 경우만
			$sql .= "  AND DATE(s.rdate) =  DATE_SUB(CURDATE(), INTERVAL 1 DAY);";//  -- 어제 날짜로 필터링	
			
 		// <수정> pv_output 에서 >> 누적발전량 차이로 계산 cumulative_energy	
			$sql = " 
					SELECT         
						DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d') AS day,
						SUM(cid_energy.day_total_energy_kW) AS day_total_energy_kW,
						SUM(cid_energy.generation_minutes) / COUNT(cid_energy.cid) AS generation_minutes

					FROM (
						SELECT 
							f.cid,
							(MAX(s.cumulative_energy) - MIN(s.cumulative_energy)) / 1000 AS day_total_energy_kW,
							TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes
						FROM RTU_SolarInputData s     
						JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id     
						JOIN RTU_user u ON f.user_id = u.user_id      
						WHERE u.user_id = '".$_SESSION['user_id']."'      
						  AND s.energy_type IN ('0101', '0102')    
						  AND s.fault_status = 0    
						  AND s.cumulative_energy > 0      
						  AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
						GROUP BY f.cid
					) AS cid_energy;
                   ";
 

		try {
			// SQL 실행
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			
			// Fetching the results
			$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			// 만약 데이터가 있다면
			if (!empty($resources)) {
				foreach ($resources as &$resource) {
					// 총 분 계산 (예: total_minutes 필드가 있다고 가정)
					$total_minutes = $resource['generation_minutes'];  // SQL에서 계산된 분 단위 발전 시간
					$hours = floor($total_minutes / 60);  // 시간 계산
					$minutes = $total_minutes % 60;  // 나머지 분 계산
					
					// 시간과 분을 "시간:분" 형식으로 변환하여 추가
					$resource['generation_time'] = $hours . "시간 " . $minutes . "분";
					
					// 필요에 따라 다른 데이터 처리도 가능
				}
			}

			// Fetch된 데이터 확인
			//var_dump($resources); // or print_r($resources);
			//exit();  // 데이터를 확인한 후 종료

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
			// Query Error Handling
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





// 오늘 예상 발전량  <수정> pv_output 에서 >> 누적발전량 차이로 계산 cumulative_energy (미검증)
function api6003_today_bjr_will($start_record_number, $itemsPerPage, $search, $keyword, $searchStartDate, $searchEndDate) {
    session_start(); 
	
    global $conn;
	
	$token = token_auth();
	
	if ($token!="") {
		$tk_userinfo = ck_token_user($token);	
		
		$_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
		$_SESSION['user_name'] = $tk_userinfo[0]['user_name'];	
	}
	
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행	
 
		// 기본 SQL 쿼리와 조건 초기화
			$sql = "  "; 
			$sql .= "  SELECT    ";//
			$sql .= "      CURDATE() AS today,  ";// -- 오늘 날짜 출력 
			$sql .= "      (yesterday_total_energy_kW +   ";//
			$sql .= "      ((today_sunlight_minutes - yesterday_sunlight_minutes) / yesterday_sunlight_minutes) * yesterday_total_energy_kW) AS estimated_today_energy_kW    ";//
			$sql .= "  FROM (  ";//
            //  -- 어제 발전량 계산
			$sql .= "      SELECT    ";//
			$sql .= "          DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS yesterday,    ";// 
			$sql .= "          SUM(s.pv_output) / 1000 AS yesterday_total_energy_kW,   ";//  
			$sql .= "          TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes     ";//
			$sql .= "      FROM RTU_SolarInputData s    ";//
			$sql .= "      JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id     ";//
			$sql .= "      JOIN RTU_user u ON f.user_id = u.user_id     ";//
			$sql .= "      WHERE u.user_id = '".$_SESSION['user_id']."'     ";//
			$sql .= "      AND s.energy_type IN ('0101', '0102')    ";//
			$sql .= "      AND s.fault_status = 0     ";//
			$sql .= "      AND s.pv_output > 0      ";//
 			$sql .= "     AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)  ";//
			$sql .= "  ) AS t1,  ";//
			$sql .= "  (  ";//
			//     -- 오늘과 어제 해 떠있는 시간 차이 계산   
			$sql .= "      SELECT  ";//
			$sql .= "          TIMESTAMPDIFF(MINUTE, st_today.sunrise, st_today.sunset) AS today_sunlight_minutes,  ";//
			$sql .= "          TIMESTAMPDIFF(MINUTE, st_yesterday.sunrise, st_yesterday.sunset) AS yesterday_sunlight_minutes  ";//
			$sql .= "      FROM RTU_sun_times_365 st_today  ";//
			$sql .= "      JOIN RTU_sun_times_365 st_yesterday  ";//
			$sql .= "          ON DATE(st_yesterday.rdate) = DATE_SUB(st_today.rdate, INTERVAL 1 DAY)  ";//
			$sql .= "      WHERE DATE(st_today.rdate) = CURDATE()  ";//
			$sql .= "  ) AS t2;  ";//
			
			$sql = "
					SELECT         
						CURDATE() AS today,        
						(SUM(t1.yesterday_total_energy_kW) + 
						((t2.today_sunlight_minutes - t2.yesterday_sunlight_minutes) / t2.yesterday_sunlight_minutes) * SUM(t1.yesterday_total_energy_kW)) AS estimated_today_energy_kW

					FROM (
						SELECT 
							f.cid,
							(MAX(s.cumulative_energy) - MIN(s.cumulative_energy)) / 1000 AS yesterday_total_energy_kW
						FROM RTU_SolarInputData s     
						JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id     
						JOIN RTU_user u ON f.user_id = u.user_id      
						WHERE u.user_id = '".$_SESSION['user_id']."'       
						  AND s.energy_type IN ('0101', '0102')    
						  AND s.fault_status = 0    
						  AND s.cumulative_energy > 0      
						  AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
						GROUP BY f.cid
					) AS t1,
					(
						SELECT
							TIMESTAMPDIFF(MINUTE, st_today.sunrise, st_today.sunset) AS today_sunlight_minutes,
							TIMESTAMPDIFF(MINUTE, st_yesterday.sunrise, st_yesterday.sunset) AS yesterday_sunlight_minutes
						FROM RTU_sun_times_365 st_today
						JOIN RTU_sun_times_365 st_yesterday
							ON DATE(st_yesterday.rdate) = DATE_SUB(st_today.rdate, INTERVAL 1 DAY)
						WHERE DATE(st_today.rdate) = CURDATE()
					) AS t2;

			";
 
		try {
			// SQL 실행
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			
			// Fetching the results
			$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
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
			// Query Error Handling
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




// 오늘 예상 발전량 
function api6004_today_bjtime_will($start_record_number, $itemsPerPage, $search, $keyword, $searchStartDate, $searchEndDate) {
    session_start(); 
	
    global $conn;
	
	$token = token_auth();
	
	if ($token!="") {
		$tk_userinfo = ck_token_user($token);	
		
		$_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
		$_SESSION['user_name'] = $tk_userinfo[0]['user_name'];	
	}
	
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 토큰이 유효함. 정상진행	
 
		// 기본 SQL 쿼리와 조건 초기화
			$sql = "  "; 
			$sql .= " SELECT  ";
			$sql .= "      CURDATE() AS today,  ";// -- 오늘 날짜 출력 
			$sql .= "     (yesterday_generation_minutes +  ";
			$sql .= "     ((today_sunlight_minutes - yesterday_sunlight_minutes) / yesterday_sunlight_minutes) * yesterday_generation_minutes) AS estimated_today_generation_minutes  ";
			$sql .= " FROM ( ";
 			   // -- 어제 발전 시간 계산
 			$sql .= "    SELECT   ";
 			$sql .= "        TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS yesterday_generation_minutes ";
 			$sql .= "    FROM RTU_SolarInputData s   ";
			$sql .= "     JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id    ";
			$sql .= "     JOIN RTU_user u ON f.user_id = u.user_id    ";
			$sql .= "     WHERE u.user_id = '".$_SESSION['user_id']."'     ";
			$sql .= "     AND s.energy_type IN ('0101', '0102')   ";
			$sql .= "     AND s.fault_status = 0    ";
			$sql .= "     AND s.pv_output > 0     ";
			$sql .= "     AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) ";
			$sql .= " ) AS t1, ";
			$sql .= " ( ";
			//    -- 오늘과 어제 해 떠있는 시간 차이 계산
			$sql .= "     SELECT ";
			$sql .= "         TIMESTAMPDIFF(MINUTE, st_today.sunrise, st_today.sunset) AS today_sunlight_minutes, ";
			$sql .= "         TIMESTAMPDIFF(MINUTE, st_yesterday.sunrise, st_yesterday.sunset) AS yesterday_sunlight_minutes ";
			$sql .= "     FROM RTU_sun_times_365 st_today ";
			$sql .= "     JOIN RTU_sun_times_365 st_yesterday ";
			$sql .= "         ON DATE(st_yesterday.rdate) = DATE_SUB(st_today.rdate, INTERVAL 1 DAY) ";
			$sql .= "     WHERE DATE(st_today.rdate) = CURDATE() ";
			$sql .= " ) AS t2; ";

 
		try {
			// SQL 실행
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			
			// Fetching the results
			$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
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
			// Query Error Handling
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
 

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// api6000_set 함수 -모니터링 홈 함수를 한 번에 호출하여 결과를 합침
function api6000_set($start_record_number, $itemsPerPage, $search, $keyword, $searchStartDate, $searchEndDate) {
    session_start();

    global $conn;

    $token = token_auth();

    if ($token != "") {
        $tk_userinfo = ck_token_user($token);
        $_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
        $_SESSION['user_name'] = $tk_userinfo[0]['user_name'];
    }

    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        // 결과 데이터 배열 초기화               
        //$response_data = ['bal' => []];
        $response_data = [];

        try {
            // today_bjr_tot 결과 가져오기 
            $sql1 = "
                SELECT
                    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS day,
                    SUM(s.pv_output) / 1000 AS day_total_energy_kW,
                    TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes,
                    CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
                FROM RTU_SolarInputData s
                JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
                JOIN RTU_user u ON f.user_id = u.user_id
                JOIN RTU_lora l ON f.lora_id = l.lora_id
                WHERE u.user_id = '" . $_SESSION['user_id'] . "'
                AND s.energy_type IN ('0101', '0102')
                AND s.fault_status = 0
                AND s.pv_output > 0
                AND DATE(s.rdate) = CURDATE()
                GROUP BY short_powerstation, l.id
                ORDER BY l.id
                LIMIT 0, 25;";
				// <수정> pv_output 에서 >> 누적발전량 차이로 계산 cumulative_energy
			$sql1 = "
				SELECT
					DATE_FORMAT(CURDATE(), '%Y-%m-%d') AS day,
					lora_id,
					SUM(t1.day_total_energy_kW) AS day_total_energy_kW,
					SUM(t1.generation_minutes) / COUNT(DISTINCT t1.cid) AS generation_minutes,
					t1.short_powerstation
				FROM (
					SELECT 
						f.cid, l.lora_id as lora_id,
						(MAX(s.cumulative_energy) - MIN(s.cumulative_energy)) / 1000 AS day_total_energy_kW,
						TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes,
						CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
					FROM RTU_SolarInputData s
					JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
					JOIN RTU_user u ON f.user_id = u.user_id
					JOIN RTU_lora l ON f.lora_id = l.lora_id
					WHERE u.user_id = '" . $_SESSION['user_id'] . "'
					  AND s.energy_type IN ('0101', '0102')
					  AND s.fault_status = 0
					  AND s.cumulative_energy > 0
					  AND DATE(s.rdate) = CURDATE()
					GROUP BY f.cid
				) AS t1
				GROUP BY t1.short_powerstation
				ORDER BY t1.short_powerstation;
			";

					
            $stmt1 = $conn->prepare($sql1);
            $stmt1->execute();
            $today_result = $stmt1->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($today_result)) {
                foreach ($today_result as $resource) {
                    $total_minutes = $resource['generation_minutes'];
                    $hours = floor($total_minutes / 60);
                    $minutes = $total_minutes % 60;
                    $resource['generation_time'] = $hours . "시간 " . $minutes . "분";

                   // $response_data['bal'][] = [
                    $response_data[] = [
                        'short_powerstation' => $resource['short_powerstation'],
                        'lora_id' => $resource['lora_id'],
                        'today_bjr' => [
                            'day' => $resource['day'],
                            'day_total_energy_kW' => $resource['day_total_energy_kW'],
                            'generation_minutes' => $resource['generation_minutes'],
                            'generation_time' => $resource['generation_time']
                        ]
                    ];
                }
            }

            // api6002_yesterday_bjr_tot 결과 가져오기
            $sql2 = "
                SELECT
                    DATE_FORMAT(MAX(s.rdate), '%Y-%m-%d') AS day,
                    SUM(s.pv_output) / 1000 AS day_total_energy_kW,
                    TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes,
                    CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
                FROM RTU_SolarInputData s
                JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
                JOIN RTU_user u ON f.user_id = u.user_id
                JOIN RTU_lora l ON f.lora_id = l.lora_id
                WHERE u.user_id = '" . $_SESSION['user_id'] . "'
                AND s.energy_type IN ('0101', '0102')
                AND s.fault_status = 0
                AND s.pv_output > 0
                AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                GROUP BY short_powerstation, l.id
                ORDER BY l.id
                LIMIT 0, 25;";
				// <수정> pv_output 에서 >> 누적발전량 차이로 계산 cumulative_energy
			$sql2 = "
				SELECT
					DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), '%Y-%m-%d') AS day,
					SUM(t1.day_total_energy_kW) AS day_total_energy_kW,
					SUM(t1.generation_minutes) / COUNT(DISTINCT t1.cid) AS generation_minutes,
					t1.short_powerstation
				FROM (
					SELECT 
						f.cid,
						(MAX(s.cumulative_energy) - MIN(s.cumulative_energy)) / 1000 AS day_total_energy_kW,
						TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS generation_minutes,
						CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
					FROM RTU_SolarInputData s
					JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
					JOIN RTU_user u ON f.user_id = u.user_id
					JOIN RTU_lora l ON f.lora_id = l.lora_id
					WHERE u.user_id = '" . $_SESSION['user_id'] . "'
					  AND s.energy_type IN ('0101', '0102')
					  AND s.fault_status = 0
					  AND s.cumulative_energy > 0
					  AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
					GROUP BY f.cid
				) AS t1
				GROUP BY t1.short_powerstation
				ORDER BY t1.short_powerstation;
			";

            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute();
            $yesterday_result = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($yesterday_result)) {
                foreach ($yesterday_result as $resource) {
                    $total_minutes = $resource['generation_minutes'];
                    $hours = floor($total_minutes / 60);
                    $minutes = $total_minutes % 60;
                    $resource['generation_time'] = $hours . "시간 " . $minutes . "분";

                    foreach ($response_data as &$entry) {
                        if ($entry['short_powerstation'] === $resource['short_powerstation']) {
                            $entry['yesterday_bjr'] = [
                                'day' => $resource['day'],
                                'day_total_energy_kW' => $resource['day_total_energy_kW'],
                                'generation_minutes' => $resource['generation_minutes'],
                                'generation_time' => $resource['generation_time']
                            ];
                        }
                    }
                }
            }

            // api6003_today_bjr_will 결과 가져오기
            $sql3 = "
                SELECT
                    CURDATE() AS today,
                    SUM(t1.yesterday_total_energy_kW +
                    ((t2.today_sunlight_minutes - t2.yesterday_sunlight_minutes) / t2.yesterday_sunlight_minutes) * t1.yesterday_total_energy_kW) AS estimated_today_energy_kW,
                    CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
                FROM (
                    SELECT
                        SUM(s.pv_output) / 1000 AS yesterday_total_energy_kW,
                        f.lora_id
                    FROM RTU_SolarInputData s
                    JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
                    JOIN RTU_user u ON f.user_id = u.user_id
                    WHERE u.user_id = '" . $_SESSION['user_id'] . "'
                    AND s.energy_type IN ('0101', '0102')
                    AND s.fault_status = 0
                    AND s.pv_output > 0
                    AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                    GROUP BY f.lora_id
                ) AS t1
                JOIN RTU_lora l ON t1.lora_id = l.lora_id
                JOIN (
                    SELECT
                        TIMESTAMPDIFF(MINUTE, st_today.sunrise, st_today.sunset) AS today_sunlight_minutes,
                        TIMESTAMPDIFF(MINUTE, st_yesterday.sunrise, st_yesterday.sunset) AS yesterday_sunlight_minutes
                    FROM RTU_sun_times_365 st_today
                    JOIN RTU_sun_times_365 st_yesterday
                        ON DATE(st_yesterday.rdate) = DATE_SUB(st_today.rdate, INTERVAL 1 DAY)
                    WHERE DATE(st_today.rdate) = CURDATE()
                ) AS t2
                GROUP BY short_powerstation;";
				
			 // <수정> pv_output 에서 >> 누적발전량 차이로 계산 cumulative_energy	출력이 안나와 , 이전거로 함
            $sql33 = "
					SELECT         
						CURDATE() AS today,        
						(SUM(t1.yesterday_total_energy_kW) + 
						((t2.today_sunlight_minutes - t2.yesterday_sunlight_minutes) / t2.yesterday_sunlight_minutes) * SUM(t1.yesterday_total_energy_kW)) AS estimated_today_energy_kW,
                        CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
					FROM (
						SELECT 
							f.cid,
							(MAX(s.cumulative_energy) - MIN(s.cumulative_energy)) / 1000 AS yesterday_total_energy_kW,  f.lora_id
						FROM RTU_SolarInputData s     
						JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id     
						JOIN RTU_user u ON f.user_id = u.user_id      
						WHERE u.user_id = '".$_SESSION['user_id']."'       
						  AND s.energy_type IN ('0101', '0102')    
						  AND s.fault_status = 0    
						  AND s.cumulative_energy > 0      
						  AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
						GROUP BY f.cid
					) AS t1
                JOIN RTU_lora l ON t1.lora_id = l.lora_id
                JOIN (
						SELECT
							TIMESTAMPDIFF(MINUTE, st_today.sunrise, st_today.sunset) AS today_sunlight_minutes,
							TIMESTAMPDIFF(MINUTE, st_yesterday.sunrise, st_yesterday.sunset) AS yesterday_sunlight_minutes
						FROM RTU_sun_times_365 st_today
						JOIN RTU_sun_times_365 st_yesterday
							ON DATE(st_yesterday.rdate) = DATE_SUB(st_today.rdate, INTERVAL 1 DAY)
						WHERE DATE(st_today.rdate) = CURDATE()
					) AS t2;		
                GROUP BY short_powerstation;				
			";
			

            $stmt3 = $conn->prepare($sql3);
            $stmt3->execute();
            $today_estimation_result = $stmt3->fetchAll(PDO::FETCH_ASSOC);

				if (!empty($today_estimation_result)) {
					foreach ($today_estimation_result as $resource) {
						foreach ($response_data as &$entry) {
							if ($entry['short_powerstation'] === $resource['short_powerstation']) {
								$entry['today_bjr_will'] = [
									'today' => $resource['today'],
									'estimated_today_energy_kW' => $resource['estimated_today_energy_kW']
								];
							}
						}
					}
				}

            // api6004_today_bjtime_will 결과 가져오기
			 // <수정> pv_output 에서 >> 누적발전량 차이로 계산 cumulative_energy	
            $sql4 = "SELECT 
                        CURDATE() AS today,  
                        (t1.yesterday_generation_minutes +   
                        ((t2.today_sunlight_minutes - t2.yesterday_sunlight_minutes) / t2.yesterday_sunlight_minutes) * t1.yesterday_generation_minutes) AS estimated_today_generation_minutes,
                        CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
                    FROM (
                        SELECT 
                            TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS yesterday_generation_minutes,
                            f.lora_id
                        FROM RTU_SolarInputData s   
                        JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id   
                        JOIN RTU_user u ON f.user_id = u.user_id    
                        WHERE u.user_id = '" . $_SESSION['user_id'] . "'   
                        AND s.energy_type IN ('0101', '0102')   
                        AND s.fault_status = 0    
						AND s.cumulative_energy > 0   
                        AND DATE(s.rdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                        GROUP BY f.lora_id
                    ) AS t1
                    JOIN RTU_lora l ON t1.lora_id = l.lora_id
                    JOIN (
                        SELECT  
                            TIMESTAMPDIFF(MINUTE, st_today.sunrise, st_today.sunset) AS today_sunlight_minutes,  
                            TIMESTAMPDIFF(MINUTE, st_yesterday.sunrise, st_yesterday.sunset) AS yesterday_sunlight_minutes  
                        FROM RTU_sun_times_365 st_today 
                        JOIN RTU_sun_times_365 st_yesterday  
                            ON DATE(st_yesterday.rdate) = DATE_SUB(st_today.rdate, INTERVAL 1 DAY)  
                        WHERE DATE(st_today.rdate) = CURDATE()  
                    ) AS t2
                    GROUP BY short_powerstation;";

            $stmt4 = $conn->prepare($sql4);
            $stmt4->execute();
            $today_time_estimation_result = $stmt4->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($today_time_estimation_result)) {
                foreach ($today_time_estimation_result as $resource) {
                    foreach ($response_data as &$entry) {
                        if ($entry['short_powerstation'] === $resource['short_powerstation']) {
                            $entry['today_bjtime_will'] = [
                                'today' => $resource['today'],
                                'estimated_today_generation_minutes' => $resource['estimated_today_generation_minutes']
                            ];
                        }
                    }
                }
            }

            // 현재출력
            $sql5 = "
                SELECT 
                    CURDATE() AS today, 
                    SUM(s.current_output) AS current_output,
                    CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
                FROM RTU_SolarInputData s
                JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
                JOIN RTU_user u ON f.user_id = u.user_id
                JOIN RTU_lora l ON f.lora_id = l.lora_id
                JOIN (
                    SELECT ltid, MAX(rdate) AS max_rdate
                    FROM RTU_SolarInputData
                    WHERE DATE(rdate) = CURDATE()
                    GROUP BY ltid
                ) AS latest ON s.ltid = latest.ltid AND s.rdate = latest.max_rdate
                WHERE u.user_id = '" . $_SESSION['user_id'] . "'   
                AND s.energy_type IN ('0101', '0102')
                GROUP BY short_powerstation;";

            $stmt5 = $conn->prepare($sql5);
            $stmt5->execute();
            $current_output_result = $stmt5->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($current_output_result)) {
                foreach ($current_output_result as $resource) {
                    foreach ($response_data as &$entry) {
                        if ($entry['short_powerstation'] === $resource['short_powerstation']) {
                            $entry['current_output'] = [
                                'today' => $resource['today'],
                                'current_output_data' => $resource['current_output']
                            ];
                        }
                    }
                }
            }

            // 가동상태 : 정상 1 / 고장 0
            $sql6 = "
                SELECT 
					CURDATE() AS today, 
					CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation,
					CASE 
						WHEN MIN(s.fault_status) = 0 THEN '1' 
						ELSE '0' 
					END AS lora_status
				FROM RTU_SolarInputData s
				JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
				JOIN RTU_user u ON f.user_id = u.user_id
				JOIN RTU_lora l ON f.lora_id = l.lora_id
				WHERE u.user_id = '" . $_SESSION['user_id'] . "'
				AND s.energy_type IN ('0101', '0102')
				AND s.rdate >= NOW() - INTERVAL 1 DAY
				GROUP BY short_powerstation;";

            $stmt6 = $conn->prepare($sql6);
            $stmt6->execute();
            $status_result = $stmt6->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($status_result)) {
                foreach ($status_result as $resource) {
                    foreach ($response_data as &$entry) {
                        if ($entry['short_powerstation'] === $resource['short_powerstation']) {
                            $entry['lora_status'] = [
                                'today' => $resource['today'],
                                'status' => $resource['lora_status']
                            ];
                        }
                    }
                }
            }

             // 총 발전량 , 총 발전시간
            $sql7 = "
                SELECT
                    SUM(s.pv_output) / 1000 AS total_day_total_energy_kW,
                    TIMESTAMPDIFF(MINUTE, MIN(s.rdate), MAX(s.rdate)) AS total_generation_minutes,
                    CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation
                FROM RTU_SolarInputData s
                JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
                JOIN RTU_user u ON f.user_id = u.user_id
                JOIN RTU_lora l ON f.lora_id = l.lora_id
                WHERE u.user_id = '" . $_SESSION['user_id'] . "'
                AND s.energy_type IN ('0101', '0102')
                AND s.fault_status = 0
                AND s.pv_output > 0
                GROUP BY short_powerstation, l.id
                ORDER BY l.id
                LIMIT 0, 25";

				

            $stmt7 = $conn->prepare($sql7);
            $stmt7->execute();
            $tot_bj_sum_result = $stmt7->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($tot_bj_sum_result)) {
                foreach ($tot_bj_sum_result as $resource) {
                    $total_minutes = $resource['total_generation_minutes'];
                    $hours = floor($total_minutes / 60);
                    $minutes = $total_minutes % 60;
                    $resource['total_generation_time'] = $hours . "시간 " . $minutes . "분";
					
                    foreach ($response_data as &$entry) {
                        if ($entry['short_powerstation'] === $resource['short_powerstation']) {
                            $entry['tot_bj_sum'] = [
                               'total_day_total_energy_kW' => $resource['total_day_total_energy_kW'],
                               'total_generation_minutes' => $resource['total_generation_minutes'],
                                'total_generation_time' => $resource['total_generation_time']
                            ];
                        }
                    }
                }
            }

            // 각 발전소의 인버터 목록과 인버터별 상태, 발전량, 현재 출력 
            $sql8 = "
				SELECT 
					f.lora_id,
					CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation,
					f.cid,
					CASE WHEN MIN(s.fault_status) = 0 THEN '1' ELSE '0' END AS inverter_status,
					SUM(s.pv_output) / 1000 AS inverter_bjr,
					MAX(s.current_output) AS inverter_current_output
				FROM RTU_SolarInputData s
				JOIN RTU_facility f ON RIGHT(s.ltid, 16) = f.lora_id
				JOIN RTU_user u ON f.user_id = u.user_id
				JOIN RTU_lora l ON f.lora_id = l.lora_id
				WHERE u.user_id = '" . $_SESSION['user_id'] . "'
				AND s.energy_type IN ('0101', '0102')
				AND s.fault_status = 0
				AND s.pv_output > 0
				GROUP BY f.lora_id, f.cid, short_powerstation
				ORDER BY short_powerstation, f.cid
				LIMIT 0, 25;
            ";
						//echo $sql8;	
			$stmt8 = $conn->prepare($sql8);
			$stmt8->execute();
			$inverter_result = $stmt8->fetchAll(PDO::FETCH_ASSOC);

			if (!empty($inverter_result)) {
				foreach ($inverter_result as $resource) {
					foreach ($response_data as &$entry) {
						if ($entry['short_powerstation'] === $resource['short_powerstation']) {
							$entry['inverter_bj_list'][] = [
								'cid' => $resource['cid'],
								'inverter_status' => $resource['inverter_status'],
								'inverter_bjr' => $resource['inverter_bjr'],
								'inverter_current_output' => $resource['inverter_current_output']
							];
						}
					}
				}
			}

            //////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Success JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [
                    'data' => $response_data,
                    'msg' => null
                ]
            ]);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////

        } catch (PDOException $e) {
            // Query Error Handling
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
        // Invalid token case
        api_error102();
    }
}




//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function api7000_set($start_record_number, $itemsPerPage, $search, $keyword, $startDate, $endDate, $status) {

    session_start();
    global $conn;

    // Token authentication
    $token = token_auth();
    if ($token != "") {
        $tk_userinfo = ck_token_user($token);
        $_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
        $_SESSION['user_name'] = $tk_userinfo[0]['user_name'];
    }

    $tk_cnt = ck_token_cnt($token);
    if ($tk_cnt[0]['cnt'] > 0) {
        $response_data = [];
        $total_count = 0;

        try {
            // Date format specification
            $start_date = date('Y-m-d 00:00:00', strtotime($startDate));
            $end_date = date('Y-m-d 23:59:59', strtotime($endDate));

            // Count query for total records
			$count_sql = "
				SELECT COUNT(DISTINCT ih.id) AS tot_count
				FROM RTU_Issue_History_New ih
				JOIN RTU_facility f ON ih.facility_id = f.cid
				JOIN RTU_user u ON ih.user_idx = u.user_idx
				JOIN RTU_lora l ON ih.lora_idx = l.id
				JOIN RTU_issue_type it ON ih.issue_name = it.issue_type_id
				INNER JOIN (
					SELECT facility_id, issue_name, status, four_hex, MIN(issue_start_date) AS min_date
					FROM RTU_Issue_History_New
					WHERE viewline = 1
					GROUP BY facility_id, issue_name, status, four_hex
				) AS earliest
				ON ih.facility_id = earliest.facility_id
				AND ih.issue_name = earliest.issue_name
				AND ih.status = earliest.status
				AND ih.issue_start_date = earliest.min_date
				AND ih.four_hex = earliest.four_hex
				INNER JOIN (
					SELECT facility_id, issue_name, status, four_hex, MAX(issue_last_date) AS max_date
					FROM RTU_Issue_History_New
					WHERE viewline = 1
					GROUP BY facility_id, issue_name, status, four_hex
				) AS latest
				ON ih.facility_id = latest.facility_id
				AND ih.issue_name = latest.issue_name
				AND ih.status = latest.status
				AND ih.four_hex = latest.four_hex
				WHERE ih.viewline = 1 AND u.user_id = :user_id
			";

			// 필터 조건 추가
			if (!empty($startDate) && !empty($endDate)) {
				$count_sql .= " AND ih.issue_start_date >= :start_date AND ih.issue_last_date <= :end_date";
			}
			if ($status !== '0') {
				$count_sql .= " AND ih.status = :status";
			}
			if ($search == "user_name" && !empty($keyword)) {
				$count_sql .= " AND u.user_name LIKE :keyword";
				$keyword = '%' . $keyword . '%';
			}
			if ($search == "issue_name" && !empty($keyword)) {
				$count_sql .= " AND it.issue_name LIKE :keyword";
				$keyword = '%' . $keyword . '%';
			}
 

            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->bindValue(':user_id', $_SESSION['user_id']);
            if (!empty($startDate) && !empty($endDate)) {
                $count_stmt->bindValue(':start_date', $start_date);
                $count_stmt->bindValue(':end_date', $end_date);
            }
            if ($status !== '0') {
                $count_stmt->bindValue(':status', $status);
            }
            if (!empty($search) && !empty($keyword)) {
                $count_stmt->bindValue(':keyword', $keyword);
            }
            $count_stmt->execute();
            $total_count = $count_stmt->fetch(PDO::FETCH_ASSOC)['tot_count'];
            
            // Main data query
            $sql = "
                SELECT 
                    ih.id AS issue_idx,
                    CASE 
                        WHEN ih.fault_description IS NOT NULL AND ih.fault_description != '' THEN
                            CASE 
                                WHEN CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')) + 1 > 1 THEN
                                    CONCAT(SUBSTRING_INDEX(ih.fault_description, ',', 1), ' 외 ', 
                                           CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')))
                                ELSE 
                                    ih.fault_description
                            END
                        ELSE 
                            it.issue_name
                    END AS issue_name, 
                    it.issue_type_id AS issue_type_id,
                    earliest.min_date AS error_first_day,
                    latest.max_date AS error_last_day,
                    ih.status,
                    f.cid AS cid_info,
                    f.lora_id AS lora_id,  
                    u.user_name AS user_name,
                    u.user_id AS user_id,
                    ih.fault_description AS fault_description,  
                    ih.four_hex AS four_hex,
                    -- fault_description_summary 필드 추가
                    CASE 
                        WHEN CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')) + 1 > 1 THEN
                            CONCAT(SUBSTRING_INDEX(ih.fault_description, ',', 1), ' 외 ', 
                                   CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')))
                        ELSE 
                            ih.fault_description
                    END AS fault_description_summary,					
                    CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS power_station
                FROM RTU_Issue_History_New ih
                JOIN RTU_facility f ON ih.facility_id = f.cid
                JOIN RTU_user u ON ih.user_idx = u.user_idx
                JOIN RTU_lora l ON ih.lora_idx = l.id
                JOIN RTU_issue_type it ON ih.issue_name = it.issue_type_id
                INNER JOIN (
                    SELECT facility_id, issue_name, status, four_hex,
                           MIN(issue_start_date) AS min_date
                    FROM RTU_Issue_History_New
                    WHERE viewline = 1
                    GROUP BY facility_id, issue_name, status, four_hex
                ) AS earliest
                ON ih.facility_id = earliest.facility_id
                AND ih.issue_name = earliest.issue_name
                AND ih.status = earliest.status
                AND ih.issue_start_date = earliest.min_date
                AND ih.four_hex = earliest.four_hex
                INNER JOIN (
                    SELECT facility_id, issue_name, status, four_hex, MAX(issue_last_date) AS max_date
                    FROM RTU_Issue_History_New
                    WHERE viewline = 1
                    GROUP BY facility_id, issue_name, status, four_hex
                ) AS latest
                ON ih.facility_id = latest.facility_id
                AND ih.issue_name = latest.issue_name
                AND ih.status = latest.status
                AND ih.four_hex = latest.four_hex
                WHERE ih.viewline = 1 AND u.user_id = '" . $_SESSION['user_id'] . "'
            ";

            // Adding filters to the main query
            if (!empty($startDate) && !empty($endDate)) {
			  // $endDateQuery = $endDate .' 23:59:59'; // 쿼리에 사용할 endDateQuery
			   $sql .= " AND ih.issue_start_date >= :start_date AND issue_last_date <= :end_date";
               //$sql .= " AND ih.issue_date BETWEEN :start_date AND :end_date";
            }
            if ($status !== '0') {
                $sql .= " AND ih.status = :status";
            }
            if ($search == "user_name" && !empty($keyword)) {
                $sql .= " AND u.user_name LIKE :keyword";
                $keyword = '%' . $keyword . '%';
            }
            if ($search == "issue_name" && !empty($keyword)) {
                $sql .= " AND it.issue_name LIKE :keyword";
                $keyword = '%' . $keyword . '%';
            }

            // Order and pagination
            $sql .= " ORDER BY 
                        CASE WHEN ih.status = '0' THEN 0 
                             WHEN ih.status = '1' THEN 1 
                             WHEN ih.status = '2' THEN 2 
                             WHEN ih.status = '3' THEN 3 
                             WHEN ih.status = '4' THEN 4 
                        END, 
                        ih.issue_start_date DESC
                      LIMIT :start_record, :items_per_page";

            $stmt = $conn->prepare($sql);
           // $stmt->bindParam(':user_id', $_SESSION['user_id']);
            
            if (!empty($startDate) && !empty($end_date)) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            if ($status !== '0') {
                $stmt->bindParam(':status', $status);
            }
            if (!empty($search) && !empty($keyword)) {
                $stmt->bindParam(':keyword', $keyword);
            }
            $stmt->bindValue(':start_record', (int)$start_record_number, PDO::PARAM_INT);
            $stmt->bindValue(':items_per_page', (int)$itemsPerPage, PDO::PARAM_INT);


           // echo "<PRE>".$sql."</PRE>";

            $stmt->execute();
            $issue_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($issue_result)) {
                foreach ($issue_result as $issue) {
                    $response_data[] = [
                        'issue_idx' => $issue['issue_idx'],
                        'issue_name' => $issue['issue_name'],
                        'issue_type_id' => $issue['issue_type_id'],
                        'fault_description' => $issue['fault_description'],
                        'fault_description_summary' => $issue['fault_description_summary'],	
                        'error_first_day' => $issue['error_first_day'],
                        'error_last_day' => $issue['error_last_day'],
                        'cid_info' => $issue['cid_info'],
                        'lora_id' => $issue['lora_id'],
                        'user_name' => $issue['user_name'],
                        'user_id' => $issue['user_id'],
                        'power_station' => $issue['power_station'],
                        'status' => $issue['status']
                    ];
                }
            }

            // Return success response with total count
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [
                    'data' => [
                        'tot_count' => $total_count,
                        'issue_history' => $response_data
                    ],
                    'msg' => null
                ]
            ]);

        } catch (PDOException $e) {
            // Handle query error
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
        // Invalid token response
        api_error102();
    }
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function api7001_set($issue_idx) {
    session_start();
    global $conn;

    // 토큰 인증 처리
    $token = token_auth();
    if ($token !== "") {
        $tk_userinfo = ck_token_user($token);
        $_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
        $_SESSION['user_name'] = $tk_userinfo[0]['user_name'];
    }

    $tk_cnt = ck_token_cnt($token);
    if ($tk_cnt[0]['cnt'] > 0) {
        try {
            // 장애 이력 상세 정보 조회
            $sql = "
                SELECT 
                    ih.id AS issue_idx,
                    CASE 
                        WHEN ih.fault_description IS NOT NULL AND ih.fault_description != '' THEN
                            CASE 
                                WHEN CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')) + 1 > 1 THEN
                                    CONCAT(SUBSTRING_INDEX(ih.fault_description, ',', 1), ' 외 ', 
                                           CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')))
                                ELSE 
                                    ih.fault_description
                            END
                        ELSE 
                            it.issue_name
                    END AS issue_name,
                    ih.fault_description AS fault_description,  
                    it.issue_type_id AS issue_type_id,
                    earliest.min_date AS error_first_day,
                    latest.max_date AS error_last_day,
                    f.cid AS cid_info,
                    f.lora_id AS lora_id,  
                    u.user_name AS user_name,
                    u.user_id AS user_id,
                    CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS power_station,
                    ih.status,
                    ih.four_hex AS four_hex
                FROM RTU_Issue_History_New ih
                JOIN RTU_facility f ON ih.facility_id = f.cid
                JOIN RTU_user u ON ih.user_idx = u.user_idx
                JOIN RTU_lora l ON ih.lora_idx = l.id
                JOIN RTU_issue_type it ON ih.issue_name = it.issue_type_id
                INNER JOIN (
                    SELECT facility_id, issue_name, status, four_hex, MIN(issue_start_date) AS min_date
                    FROM RTU_Issue_History_New
                    WHERE viewline = 1
                    GROUP BY facility_id, issue_name, status, four_hex
                ) AS earliest
                ON ih.facility_id = earliest.facility_id
                AND ih.issue_name = earliest.issue_name
                AND ih.status = earliest.status
                AND ih.issue_start_date = earliest.min_date
                AND ih.four_hex = earliest.four_hex
                INNER JOIN (
                    SELECT facility_id, issue_name, status, four_hex, MAX(issue_last_date) AS max_date
                    FROM RTU_Issue_History_New
                    WHERE viewline = 1
                    GROUP BY facility_id, issue_name, status, four_hex
                ) AS latest
                ON ih.facility_id = latest.facility_id
                AND ih.issue_name = latest.issue_name
                AND ih.status = latest.status
                AND ih.four_hex = latest.four_hex
                WHERE ih.viewline = 1 AND u.user_id = :user_id AND ih.id = :issue_idx
            ";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':issue_idx', $issue_idx, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $issue_detail = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($issue_detail) {
                // 데이터가 존재할 때의 처리
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS'
                    ],
                    'body' => [
                        'data' => $issue_detail,
                        'msg' => null
                    ]
                ]);
            } else {
                // 장애 이력을 찾을 수 없는 경우, issue_idx를 포함해 응답
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 404,
                        'codeName' => 'NOT_FOUND',
                        'message' => '해당 장애 이력을 찾을 수 없습니다.',
                        'issue_idx' => $issue_idx  // issue_idx 값 포함
                    ]
                ]);
            }

        } catch (PDOException $e) {
            // 쿼리 오류 시 issue_idx 포함하여 응답
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage(),
                    'issue_idx' => $issue_idx  // issue_idx 값 포함
                ]
            ]);
        }
    } else {
        // 유효하지 않은 토큰 응답
        api_error102();
    }
}




//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function api7002_set($issue_id, $notes, $technician_id, $status = 2, $saved_file_name = null, $original_file_name = null) {
    session_start();
    global $conn;

    // Token authentication
    $token = token_auth();
    if ($token != "") {
        $tk_userinfo = ck_token_user($token);
        $_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
        $_SESSION['user_name'] = $tk_userinfo[0]['user_name'];
    }

    $tk_cnt = ck_token_cnt($token);
    if ($tk_cnt[0]['cnt'] > 0) {
        try {
            // 1. technician_id가 RTU_Technician 테이블에 존재하는지 확인
            $checkTechnicianSql = "SELECT COUNT(*) FROM RTU_Technician WHERE technician_id = :technician_id";
            $checkStmt = $conn->prepare($checkTechnicianSql);
            $checkStmt->bindParam(':technician_id', $technician_id, PDO::PARAM_STR);
            $checkStmt->execute();
            $technicianExists = $checkStmt->fetchColumn();

            if ($technicianExists == 0) {
                // technician_id가 RTU_Technician에 없으면 오류 메시지 반환
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 400,
                        'codeName' => 'BAD_REQUEST',
                        'message' => '유효하지 않은 technician_id입니다.'
                    ]
                ]);
                return;
            }

            // 2. as_num 생성
            $year = date('y');
            $month = date('m');
            $hexPart = strtoupper(dechex($year)) . strtoupper(dechex($month));
            $timePart = date('is');
            $microseconds = substr(microtime(), 2, 2);
            $randomNumber = rand(0, 9);
            $as_num = $hexPart . $timePart . $microseconds . $randomNumber;

            // 3. AS 요청 추가
            $sql = "INSERT INTO RTU_AS_Request (
                        issue_id, request_date, technician_id, notes, 
                        created_at, updated_at, as_num, 
                        saved_file_name, original_file_name
                    ) VALUES (
                        :issue_id, NOW(), :technician_id, :notes, 
                        NOW(), NOW(), :as_num, 
                        :saved_file_name, :original_file_name
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':issue_id', $issue_id, PDO::PARAM_INT);
            $stmt->bindParam(':technician_id', $technician_id, PDO::PARAM_STR);
            $stmt->bindParam(':notes', $notes, PDO::PARAM_STR);
            $stmt->bindParam(':as_num', $as_num, PDO::PARAM_STR);
            $stmt->bindParam(':saved_file_name', $saved_file_name, PDO::PARAM_STR);
            $stmt->bindParam(':original_file_name', $original_file_name, PDO::PARAM_STR);
            $stmt->execute();

            // 4. RTU_Issue_History_New 테이블 업데이트
            $updateStatusSql = "UPDATE RTU_Issue_History_New 
                                SET status = :status, updated_at = NOW() 
                                WHERE id = :issue_id";
            $updateStmt = $conn->prepare($updateStatusSql);
            $updateStmt->bindParam(':status', $status, PDO::PARAM_STR);
            $updateStmt->bindParam(':issue_id', $issue_id, PDO::PARAM_INT);
            $updateStmt->execute();

            // 성공 응답 반환
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [
                    'data' => [
                        'as_id' => $conn->lastInsertId(),
                        'issue_id' => $issue_id,
                        'status' => $status,
                        'notes' => $notes,
                        'technician_id' => $technician_id,
                        'request_date' => date('Y-m-d H:i:s'),
                        'attachment' => [
                            'saved_file_name' => $saved_file_name,
                            'original_file_name' => $original_file_name
                        ]
                    ],
                    'msg' => 'AS 요청이 성공적으로 제출되었습니다.'
                ]
            ]);

        } catch (PDOException $e) {
            // 데이터베이스 오류 응답
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
        // 유효하지 않은 토큰 응답
        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 401,
                'codeName' => 'UNAUTHORIZED',
                'message' => '유효하지 않은 토큰입니다.'
            ]
        ]);
    }
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function api7002_set_noTOKEN($issue_id, $notes, $technician_id, $status) {
    global $conn; 
    
    try {
        // 1. technician_id가 RTU_Technician 테이블에 존재하는지 확인
        $checkTechnicianSql = "SELECT COUNT(*) FROM RTU_Technician WHERE technician_id = :technician_id";
        $checkStmt = $conn->prepare($checkTechnicianSql);
        $checkStmt->bindParam(':technician_id', $technician_id, PDO::PARAM_STR);
        $checkStmt->execute();
        $technicianExists = $checkStmt->fetchColumn();

        if ($technicianExists == 0) {
            // technician_id가 RTU_Technician에 없으면 오류 메시지 반환
            return [
                'header' => [
                    'resultCode' => 400,
                    'codeName' => 'BAD_REQUEST',
                    'message' => '유효하지 않은 technician_id입니다.'
                ]
            ];
        }

        // 2. as_num 생성 (앞 문자 2자리: 년월을 16진수로 변환 + 숫자 6자리: 분초 + 마이크로초 + 랜덤 숫자)
        $year = date('y'); // 년도 마지막 2자리
        $month = date('m'); // 월
        $hexPart = strtoupper(dechex($year)) . strtoupper(dechex($month)); // 년월을 16진수로 변환하여 대문자로 변환

        $timePart = date('is'); // 분초 정보 (4자리)
        $microseconds = substr(microtime(), 2, 2); // 마이크로초 중 2자리
        $randomNumber = rand(0, 9); // 0에서 9 사이의 랜덤 숫자 1자리

        $as_num = $hexPart . $timePart . $microseconds . $randomNumber; // 예: "10A0534524"

        // 3. AS 요청 추가 (RTU_AS_Request 테이블에 삽입)
        $sql = "INSERT INTO RTU_AS_Request (issue_id, request_date, technician_id, notes, created_at, updated_at, as_num)
                VALUES (:issue_id, NOW(), :technician_id, :notes, NOW(), NOW(), :as_num)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':issue_id', $issue_id, PDO::PARAM_STR);
        $stmt->bindParam(':technician_id', $technician_id, PDO::PARAM_STR);
        $stmt->bindParam(':notes', $notes, PDO::PARAM_STR);
        $stmt->bindParam(':as_num', $as_num, PDO::PARAM_STR);
        
        $stmt->execute();

        // 4. RTU_Issue_History_New 테이블에서 issue_id에 해당하는 status 업데이트
        $updateStatusSql = "UPDATE RTU_Issue_History_New 
                            SET status = :status, updated_at = NOW() 
                            WHERE id = :issue_id";
        
        $updateStmt = $conn->prepare($updateStatusSql);
        $updateStmt->bindParam(':status', $status, PDO::PARAM_STR);
        $updateStmt->bindParam(':issue_id', $issue_id, PDO::PARAM_STR);
        
        $updateStmt->execute();

        return [
            'header' => [
                'resultCode' => 200,
                'codeName' => 'SUCCESS'
            ]
        ];
    } catch (PDOException $e) {
        return [
            'header' => [
                'resultCode' => 500,
                'codeName' => 'INTERNAL_SERVER_ERROR',
                'message' => $e->getMessage()
            ]
        ];
    }
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function api7003_set($as_id) {
    session_start();
    global $conn;

    // Token authentication
    $token = token_auth();
    if ($token != "") {
        $tk_userinfo = ck_token_user($token);
        $_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
        $_SESSION['user_name'] = $tk_userinfo[0]['user_name'];
    }

    $tk_cnt = ck_token_cnt($token);
    if ($tk_cnt[0]['cnt'] > 0) {
        try {
            // 상세 정보 조회
            $sql = "
                SELECT 
                    ar.as_id,
                    ar.as_num,
                    ar.completion_date,
                    ar.notes,
                    ar.reservation_date,
                    ar.as_memo,
                    ih.issue_name,
                    ih.issue_start_date,
                    ih.facility_id,
                    ih.lora_idx,
                    ih.status AS issue_status,
                    ih.fault_description AS fault_description,
                    ih.viewline,
                    ih.created_at AS issue_created_at,
                    CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS power_station
                FROM RTU_AS_Request ar
                JOIN RTU_Issue_History_New ih ON ar.issue_id = ih.id
                JOIN RTU_issue_type it ON ih.issue_name = it.issue_type_id
                JOIN RTU_facility f ON ih.facility_id = f.cid
                JOIN RTU_user u ON ih.user_idx = u.user_idx
                JOIN RTU_lora l ON ih.lora_idx = l.id
                WHERE ar.as_id = :as_id
            ";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':as_id', $as_id, PDO::PARAM_INT);
            $stmt->execute();
            $detail = $stmt->fetch(PDO::FETCH_ASSOC);

            // 결과가 없을 때 처리
            if (!$detail) {
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 404,
                        'codeName' => 'NOT_FOUND',
                        'message' => '해당 AS 내역이 존재하지 않습니다.'
                    ]
                ]);
                exit;
            }

            // JSON 응답 생성
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [
                    'as_id' => $detail['as_id'],
                    'issue_status' => $detail['issue_status'],
                    'as_num' => $detail['as_num'],
                    'completion_date' => $detail['completion_date'],
                    'as_memo' => $detail['as_memo'],
                    'issue_start_date' => $detail['issue_start_date'],
                    'fault_description' => $detail['fault_description'],
                    'powerstation' => $detail['power_station'],
					'rtu'  => "태양광 LoRa RTU",
                    'notes' => $detail['notes'],
                    'reservation_date' => $detail['reservation_date'],
                    'facility_id' => $detail['facility_id'],
                    'lora_idx' => $detail['lora_idx']
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            // 데이터베이스 오류 응답
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        // 유효하지 않은 토큰 응답
        header('Content-Type: application/json');
        echo json_encode([
            'header' => [
                'resultCode' => 401,
                'codeName' => 'UNAUTHORIZED',
                'message' => '유효하지 않은 토큰입니다.'
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }    
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function api5000_set() {
    global $conn;

    // Token 인증 확인
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        try {
            // 공지사항 목록 조회 쿼리 예시
            $sql = "SELECT notice_id, title, content, created_at FROM RTU_Notice WHERE delYN = 'N' ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

         // JSON 응답 생성
        header('Content-Type: application/json');
		
		// 성공적인 JSON 응답 생성
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
					],
					'body' => [
						'data' => $notices,
						'msg' => null
					]						
				], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			} catch (PDOException $e) {
        // 데이터베이스 오류 응답
        header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
				]
			], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        // 인증 실패 응답
        echo json_encode([
            'header' => [
                'resultCode' => 401,
                'codeName' => 'UNAUTHORIZED',
                'message' => '유효하지 않은 토큰입니다.'
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function api5001_faq($category_id = null) {
    global $conn;

    // 토큰 인증 및 검증
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);

    if ($tk_cnt[0]['cnt'] > 0) {
        try {
            // 카테고리 필터링 조건에 따라 SQL 쿼리 설정
            if ($category_id) {
                $sql = "
                    SELECT f.faq_id, f.question, f.answer, f.display_order, f.created_at, f.updated_at, c.category_id, c.category_name
                    FROM RTU_FAQ f
                    JOIN RTU_FAQ_Category c ON f.category_id = c.category_id
                    WHERE f.category_id = :category_id AND f.is_active = 1 AND c.is_active = 1
                    ORDER BY f.display_order ASC, f.created_at DESC
                ";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            } else {
                // 모든 카테고리의 FAQ를 조회
                $sql = "
                    SELECT f.faq_id, f.question, f.answer, f.display_order, f.created_at, f.updated_at,  c.category_id, c.category_name
                    FROM RTU_FAQ f
                    JOIN RTU_FAQ_Category c ON f.category_id = c.category_id
                    WHERE f.is_active = 1 AND c.is_active = 1
                    ORDER BY c.display_order ASC, f.display_order ASC, f.created_at DESC
                ";
                $stmt = $conn->prepare($sql);
            }

            // 쿼리 실행
            $stmt->execute();
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 결과가 없는 경우 처리
            if (empty($resources)) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS'
                    ],
                    'body' => null
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return;
            }

            // 성공적인 JSON 응답 (여러 결과 반환)
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [
                    'data' => $resources
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            // 쿼리 오류 발생 시 처리
            error_log('Query Error: ' . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        // 유효하지 않은 토큰 처리
        api_error102();
    }
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function api5002_qna($inquiry_id = null) {
    global $conn;

    // Token authentication
    $token = token_auth();
    if ($token != "") {
        $tk_userinfo = ck_token_user($token);
        $_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
        $_SESSION['user_name'] = $tk_userinfo[0]['user_name'];
    }
    $tk_cnt = ck_token_cnt($token);
    if ($tk_cnt[0]['cnt'] > 0) {
        try {
            // 특정 문의 ID가 있을 경우 해당 문의와 답변을 조회하는 쿼리
            if ($inquiry_id) {
                $sql = "
                    SELECT i.inquiry_id, i.user_id, i.title, i.content, i.status, i.created_at, i.updated_at,     i.saved_file_name,     i.original_file_name, u.user_name as user_name, u.user_id as user_id,
                           r.reply_id, r.admin_id, r.content AS reply_content, r.created_at AS reply_created_at, r.updated_at AS reply_updated_at
                    FROM RTU_Inquiry i
                    LEFT JOIN RTU_Inquiry_Reply r ON i.inquiry_id = r.inquiry_id
					Join RTU_user u ON u.user_idx = i.user_id and u.user_id = '".$_SESSION['user_id']."' 
                    WHERE i.inquiry_id = :inquiry_id
                    ORDER BY r.created_at ASC
                ";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':inquiry_id', $inquiry_id, PDO::PARAM_INT);
            } else {
                // 특정 문의 ID가 없을 경우 전체 문의와 답변 목록 조회
                $sql = "
                    SELECT i.inquiry_id, i.user_id, i.title, i.content, i.status, i.created_at, i.updated_at, u.user_name as user_name, u.user_id as user_id,
                           r.reply_id, r.admin_id, r.content AS reply_content, r.created_at AS reply_created_at, r.updated_at AS reply_updated_at
                    FROM RTU_Inquiry i
                    LEFT JOIN RTU_Inquiry_Reply r ON i.inquiry_id = r.inquiry_id
  					Join RTU_user u ON u.user_idx = i.user_id and u.user_id = '".$_SESSION['user_id']."'
                  ORDER BY i.created_at DESC, r.created_at ASC
                ";
                $stmt = $conn->prepare($sql);
            }

            // 쿼리 실행
            $stmt->execute();
            $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 결과가 없는 경우 처리
            if (empty($resources)) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'header' => [
                        'resultCode' => 200,
                        'codeName' => 'SUCCESS'
                    ],
                    'body' => null
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return;
            }

            // 성공적인 JSON 응답 (여러 결과 반환)
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [
                    'data' => $resources
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            // 쿼리 오류 발생 시 처리
            error_log('Query Error: ' . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage()
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        // 유효하지 않은 토큰 처리
        api_error102();
    }
}
 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 1:1 qna 입력받기
function api_5003_qna_input($title, $content, $saved_file_name = null, $original_file_name = null)
{
    global $conn;

    // Token 인증 확인
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);
    if ($token != "") {
        $tk_userinfo = ck_token_user($token);
        $_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
        $_SESSION['user_name'] = $tk_userinfo[0]['user_name'];
    }

    if ($tk_cnt[0]['cnt'] > 0) {
        header('Content-Type: application/json; charset=UTF-8');

        // 필수 값 검증
        if ($title === null || $content === null) {
            echo json_encode([
                'header' => [
                    'resultCode' => 400,
                    'codeName' => 'BAD_REQUEST'
                ],
                'body' => [
                    'message' => '필수 필드가 누락되었습니다.'
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            // DB 연결
            $conn = getDbConnection();

            // RTU_user 테이블에서 user_idx 값 가져오기
            $sql_user = "SELECT user_idx FROM RTU_user WHERE user_id = :user_id LIMIT 1";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
            $stmt_user->execute();
            $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

            if (!$user_data || !isset($user_data['user_idx'])) {
                // 유효하지 않은 사용자 처리
                echo json_encode([
                    'header' => [
                        'resultCode' => 404,
                        'codeName' => 'USER_NOT_FOUND'
                    ],
                    'body' => [
                        'message' => '유효하지 않은 사용자입니다.'
                    ]
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return;
            }

            $user_idx = $user_data['user_idx']; // 가져온 user_idx 값

            // QNA 등록 쿼리
            $sql = "INSERT INTO RTU_Inquiry (user_id, title, content, saved_file_name, original_file_name, status, created_at, updated_at) 
                    VALUES (:user_idx, :title, :content, :saved_file_name, :original_file_name, :status, NOW(), NOW())";
            $stmt = $conn->prepare($sql);

            // 바인딩 데이터
            $stmt->bindParam(':user_idx', $user_idx, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':saved_file_name', $saved_file_name, PDO::PARAM_STR);
            $stmt->bindParam(':original_file_name', $original_file_name, PDO::PARAM_STR);
            $stmt->bindValue(':status', '0', PDO::PARAM_STR); // ENUM 값에 맞는 문자열 '0'

            // 쿼리 실행
            $stmt->execute();

            // 생성된 ID 가져오기
            $inquiry_id = $conn->lastInsertId();

            // 성공 응답 반환
            echo json_encode([
                'header' => [
                    'resultCode' => 200,
                    'codeName' => 'SUCCESS'
                ],
                'body' => [
                    'inquiry_id' => $inquiry_id,
                    'message' => 'QNA가 성공적으로 등록되었습니다.',
                    'attachment' => [
                        'saved_file_name' => $saved_file_name,
                        'original_file_name' => $original_file_name
                    ]
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            // DB 오류 처리
            echo json_encode([
                'header' => [
                    'resultCode' => 500,
                    'codeName' => 'INTERNAL_SERVER_ERROR'
                ],
                'body' => [
                    'message' => '서버 내부 오류가 발생했습니다.',
                    'error' => $e->getMessage()
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        // 인증 실패 응답
        echo json_encode([
            'header' => [
                'resultCode' => 401,
                'codeName' => 'UNAUTHORIZED'
            ],
            'body' => [
                'message' => '유효하지 않은 토큰입니다.'
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
 
 
 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 통계분석
function api8000_set($cid, $view_type = 't_day', $date, $date_offset = 0) {
    global $conn;

    // Token 인증 확인
    $token = token_auth();
    $tk_cnt = ck_token_cnt($token);
    if ($token != "") {
        $tk_userinfo = ck_token_user($token);
        $_SESSION['user_id'] = $tk_userinfo[0]['user_id'];
        $_SESSION['user_name'] = $tk_userinfo[0]['user_name'];
    }
    
    if ($tk_cnt[0]['cnt'] > 0) {
        try {
            // 날짜 및 시간 설정
            $date = $date ?: date('Y-m-d');
            $date_format = 'Y-m-d';

            // view_type에 따른 날짜 조정
            switch ($view_type) {
                case 't_day':
                    $date_format = 'Y-m-d';
                    $new_date = strtotime("$date $date_offset day");
                    $date = date('Y-m-d', $new_date);
                    break;
                case 't_month':
                    $date_format = 'Y-m';
                    $new_date = strtotime("$date $date_offset month");
                    $date = date('Y-m', $new_date);
                    break;
                case 't_year':
                    $date_format = 'Y';
                    $new_date = strtotime("$date $date_offset year");
                    $date = date('Y', $new_date);
                    break;
                case 't_between':
                    // 기간 선택 (달력)
                    break;
                default: 
                    $new_date = strtotime("$date $date_offset day");
                    $date = date('Y-m-d', $new_date);
            }

            $user_id = $_SESSION['user_id'];
            $cid_list = get_cid_list_by_user($user_id, $cid);
            if (!$cid) $cid = $cid_list[0]['cid'];

            // 통계 함수 호출
            $statistics = match ($view_type) {
                't_day' => api_daily_statistics($cid, $date),
                't_month' => api_monthly_statistics($cid, $date),
                't_year' => api_yearly_statistics($cid, $date),
                default => []
            };

            $total_output = getTotalGeneration($cid, $date, $view_type) / 1000;
            $average_output = getAverageGeneration($cid, $date, $view_type) / 1000;
            $total_minutes = getTotalGenerationTime($cid, $date, $view_type);
			
			$total_minutes_is = "";
			$hours = floor($total_minutes / 60);
			$minutes = $total_minutes % 60;
			if($hours > 0){ 
				$total_minutes_is = $hours . "시간 "; 
			}else{
				$total_minutes_is = "00시간";
			}
			
			if($minutes > 0){
				$total_minutes_is = $total_minutes_is . $minutes . "분";
			}else{
				$total_minutes_is = $total_minutes_is . "00분";
			}

            // 이전 날짜 및 다음 날짜 계산
            $prev_date = date($date_format, strtotime("$date -1 day"));
            $next_date = date($date_format, strtotime("$date +1 day"));         

            return json_response(200, [
                'total_output_kw' => number_format($total_output, 2),
                'average_output_kwh' => number_format($average_output, 2),
                'date' => $date,
                'total_hours' => $total_minutes_is,
                'statistics' => $statistics
            ]);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return json_response(500, [
                'resultCode' => 500,
                'codeName' => 'INTERNAL_SERVER_ERROR',
                'message' => '데이터베이스 오류가 발생했습니다.'
            ]);
        }
    } else {
        return json_response(401, [
            'resultCode' => 401,
            'message' => 'Invalid Token'
        ]);
    }
}

// CID 목록 가져오기
function get_cid_list_by_user($user_id, $cid) {
    global $conn;            
    $sql = "
        SELECT f.cid, 
               CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation 
        FROM RTU_facility f
        JOIN RTU_lora l ON f.lora_id = l.lora_id
        WHERE f.user_id = :user_id
        ORDER BY f.install_confirm_date ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 일별 통계 함수
function api_daily_statistics($cid, $date) {
    global $conn;
    $max_output_sql = "SELECT inverter_capacity * 1000 AS max_output FROM RTU_facility WHERE cid = :cid";
    $max_output_stmt = $conn->prepare($max_output_sql);
    $max_output_stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $max_output_stmt->execute();
    $max_output_row = $max_output_stmt->fetch(PDO::FETCH_ASSOC);
    $max_output = $max_output_row['max_output'] ?? 0;
 
	$sql = "	
        SELECT 
            HOUR(rdate) AS hour,
            CAST((MAX(cumulative_energy) - MIN(cumulative_energy)) AS UNSIGNED) / 1000 AS total_output,   
            ROUND(((MAX(cumulative_energy) - MIN(cumulative_energy)) / :max_output) * 100, 2) AS avg_efficiency,  
            IF(SUM(fault_status) > 0, '0', '1') AS inverter_status
        FROM RTU_SolarInputData
        WHERE cid = :cid AND DATE(rdate) = :date
        GROUP BY HOUR(rdate)
        HAVING total_output > 0
        ORDER BY hour
	";		
				
	
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':max_output', $max_output, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 월별 통계 분석 함수
function api_monthly_statistics($cid, $month) {
    global $conn;
    try {
        // 최대 출력 확인
        $max_output_stmt = $conn->prepare("SELECT inverter_capacity * 1000 AS max_output FROM RTU_facility WHERE cid = :cid");
        $max_output_stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $max_output_stmt->execute();
        $max_output = $max_output_stmt->fetch(PDO::FETCH_ASSOC)['max_output'] ?? 0;

       $sql = "
				SELECT 
					DATE(hourly_data.date) AS date,
					SUM(hourly_data.hourly_output) / 1000 AS total_output, 
					ROUND((SUM(hourly_data.hourly_output) / :max_output) * 100, 2) AS avg_efficiency,  
					CONCAT(
						LPAD(FLOOR(MAX(daily_data.generation_minutes) / 60), 2, '0'), '시간 ',
						LPAD(MAX(daily_data.generation_minutes) % 60, 2, '0'), '분'
					) AS generation_minutes,
					MIN(daily_data.inverter_status) AS inverter_status
				FROM (
					SELECT 
						DATE(rdate) AS date,
						HOUR(rdate) AS hour,
						MAX(cumulative_energy) - MIN(cumulative_energy) AS hourly_output,
						SUM(fault_status) AS fault_status
					FROM RTU_SolarInputData
					WHERE cid = :cid
					  AND DATE_FORMAT(rdate, '%Y-%m') = :month
					  AND cumulative_energy IS NOT NULL
					  AND cumulative_energy > 0
					GROUP BY DATE(rdate), HOUR(rdate)
					HAVING hourly_output > 0
				) AS hourly_data
				JOIN (
					SELECT 
						DATE(rdate) AS date,
						TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)) AS generation_minutes,
						IF(SUM(fault_status) > 10, '0', '1') AS inverter_status
					FROM RTU_SolarInputData
					WHERE cid = :cid
					  AND DATE_FORMAT(rdate, '%Y-%m') = :month
					  AND cumulative_energy IS NOT NULL
					  AND cumulative_energy > 0
					GROUP BY DATE(rdate)
					HAVING MAX(cumulative_energy) - MIN(cumulative_energy) > 0
				) AS daily_data ON hourly_data.date = daily_data.date
				GROUP BY DATE(hourly_data.date)
				ORDER BY date;
        ";
		
		//echo $sql;
		
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt->bindParam(':max_output', $max_output, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// 연별 통계 함수 예시
function api_yearly_statistics($cid, $date) {
    global $conn;
    // 구현 필요
}

// 기간 통계 함수 예시
function api_period_statistics($cid, $start_date, $end_date) {
    global $conn;
    // 구현 필요
}


// 총 발전량 계산
function getTotalGeneration($cid, $date, $view_type) {
    global $conn;
    // 타입에 따라 일/월/연별 총 발전량 계산 쿼리 적용
     
    if ($view_type=="t_day") {
		$sql = "	
			SELECT SUM(hourly_output) AS total_generation
			FROM (
				SELECT 
					HOUR(rdate) AS hour,
					MAX(cumulative_energy) - MIN(cumulative_energy) AS hourly_output
				FROM RTU_SolarInputData
				WHERE cid = :cid AND DATE(rdate) = :date
				GROUP BY HOUR(rdate)
				HAVING hourly_output > 0
			) AS hourly_data				
		";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['total_generation'] ?? 0;
	}elseif ($view_type=="t_month") {
        $sql = "
            SELECT SUM(daily_output) AS total_generation
            FROM (
                SELECT 
                    DATE(rdate) AS date,
                    MAX(cumulative_energy) - MIN(cumulative_energy) AS daily_output
                FROM RTU_SolarInputData
                WHERE cid = :cid AND DATE_FORMAT(rdate, '%Y-%m') = :month
                GROUP BY DATE(rdate)
                HAVING daily_output > 0
            ) AS daily_data
        ";
	 
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':month', $date, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_generation'] ?? 0;
  }
}

// 평균 발전량 계산
function getAverageGeneration($cid, $date, $view_type) {
    global $conn;
    // 타입에 따라 일/월/연별 평균 발전량 계산 쿼리 적용
    if ($view_type=="t_day") {	
	
		$sql = "
			SELECT AVG(hourly_output) AS avg_generation
			FROM (
				SELECT 
					HOUR(rdate) AS hour,
					MAX(cumulative_energy) - MIN(cumulative_energy) AS hourly_output
				FROM RTU_SolarInputData
				WHERE cid = :cid AND DATE(rdate) = :date
				GROUP BY HOUR(rdate)
				HAVING hourly_output > 0
			) AS hourly_data			

		";
 
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['avg_generation'] ?? 0;	
	}elseif ($view_type=="t_month") {		
        $sql = "
            SELECT AVG(daily_output) AS avg_generation
            FROM (
                SELECT 
                    DATE(rdate) AS date,
                    MAX(cumulative_energy) - MIN(cumulative_energy) AS daily_output
                FROM RTU_SolarInputData
                WHERE cid = :cid AND DATE_FORMAT(rdate, '%Y-%m') = :month
                GROUP BY DATE(rdate)
                HAVING daily_output > 0
            ) AS daily_data
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':month', $date, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['avg_generation'] ?? 0;
  }
	
}

// 총 발전 시간 계산
function getTotalGenerationTime($cid, $date, $view_type) {
    global $conn;
    // 타입에 따라 일/월/연별 발전 시간 계산 쿼리 적용
    if ($view_type=="t_day") {	
	
		$sql = "
			SELECT COUNT(*) AS generation_hours
			FROM (
				SELECT HOUR(rdate) AS hour
				FROM RTU_SolarInputData
				WHERE cid = :cid
				  AND DATE(rdate) = :date
				GROUP BY HOUR(rdate)
				HAVING MAX(cumulative_energy) - MIN(cumulative_energy) > 0
			) AS hourly_data;
		";
		$sql = "
			 SELECT 
				  TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)) AS generation_minutes
			FROM 
				RTU_SolarInputData
			WHERE 
			cid = :cid
			AND DATE(rdate) = :date
			AND cumulative_energy IS NOT NULL
			AND cumulative_energy > 0			
		";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['generation_minutes'] ?? 0;
	}elseif ($view_type=="t_month") {		
        $sql = "
            SELECT COUNT(DISTINCT DATE(rdate)) AS generation_days
            FROM RTU_SolarInputData
            WHERE cid = :cid AND DATE_FORMAT(rdate, '%Y-%m') = :month AND pv_output > 0
        ";
		
         $sql = "
            SELECT 
                TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)) AS generation_minutes
            FROM RTU_SolarInputData
            WHERE cid = :cid AND DATE_FORMAT(rdate, '%Y-%m') = :month AND pv_output > 0
        "; 
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':month', $date, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['generation_minutes'] ?? 0;
  }	
}

// JSON 응답 함수
function json_response($code = 200, $data = null) {
    header_remove();
    http_response_code($code);
    header('Content-Type: application/json; charset=UTF-8');
    $status = [
        200 => 'OK',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        401 => 'Unauthorized'
    ];
    // 응답 구조 생성
    $response = [
        'header' => [
            'resultCode' => $code,
            'codeName' => $status[$code] ?? 'UNKNOWN'
        ],
        'body' => [
            'data' => $data ?? []
        ]
    ];

    // JSON 출력
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

















function api9999_set()
{
    header('Content-Type: application/json; charset=utf-8');

    // Sample data to match the requested format
    $response = [
        "header" => [
            "resultCode" => 200,
            "codeName" => "SUCCESS"
        ],
        "body" => [
            "data" => [
                "status" => 1,
                "originCurrentPower" => "120",
                "originGeneratedEnergy" => "3775",
                "originGenerationTime" => "11시간20분"
            ],
            "msg" => null
        ]
    ];

    // Output JSON response
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}








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
 

// 토큰 유효성 검사 
function ck_token_cnt($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT IFNULL(count(user_id),'0') as cnt FROM `RTU_user` WHERE  delYN = 'N'  and user_token = '".$token."'");
    $stmt->execute(); 
    $token_cnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $token_cnt;
}


// 토큰 유효성 검사 
function ck_token_user($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT user_id, user_name FROM `RTU_user` WHERE  delYN = 'N'  AND user_token = '".$token."'");
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



function user_partner_id($token){
    global $conn;
	
    $sql = "SELECT partner_id  FROM `RTU_user` WHERE  delYN = 'N'  and user_token = '".$token."'";
    // SQL 실행 준비
    $stmt = $conn->prepare($sql);
 
    // SQL 실행
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);	
}


  

function list_total_cnt($table_name) {
    global $conn;
    // SQL 인젝션 방지를 위해 준비된 문 사용
    $stmt = $conn->prepare("SELECT count(*) as count FROM ".$table_name);
    
    // 쿼리 실행
    $stmt->execute();
    
    // 결과 가져오기
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['count'] ?? 0; // 결과가 null일 경우 기본값으로 0 반환
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























// 히스토리 추가 
 function add_history($h_type,$h_action,$h_col1,$h_col2){
    session_start(); 
    global $conn;
	
    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];

	
	$date = date("Y-m-d H:i:s");	
	// 히스토리추가
	if ($h_action=="앵글내 제품목록 조회") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고의 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="앵글을 삽입") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고에 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="앵글을 삭제") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."창고의 ".$h_col2."','','".$h_action."','m02')");		
	}else if ($h_action=="창고를 등록") {	
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','','".$h_action."','m02')");		
	}else if ($h_action=="창고를 삭제") {	
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','','".$h_action."','m02')");		
	}else if ($h_action=="창고명을 변경") {	
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','".$h_col2."','".$h_action."','m02')");		
	}else if ($h_action=="앵글명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','창고관리','".$h_col1."','".$h_col2."','".$h_action."','m02')");		
	}else if ($h_action=="로그인 성공") {
		if ($_SESSION['sys'] == "N") {
			$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		 			
		}
		if ($_SESSION['sys'] == "Y") {
			$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['sys_admin_name']."','".$_SESSION['sys_admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		 			
		}		
	}else if ($h_action=="로그인 실패") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$h_col1."','".$h_col2."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		
	}else if ($h_action=="로그아웃") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','로그인관리','','','".$h_action."','m00')");		
	}else if ($h_action=="제품을 등록") {	
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품의 분류를 변경") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품정보를 변경") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품을 삭제") {	
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if (($h_action=="창고 입고등록") || ($h_action=="창고 입고등록")) {	
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
	}else if ($h_action=="제품분류를 등록") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1." 이름으로 ".$h_col2."','','".$h_action."','m03')");		
	}else if ($h_action=="제품분류명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="제품분류명을 삭제") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','제품관리','".$h_col1."','".$h_col2."','".$h_action."','m03')");		
	}else if ($h_action=="재고이동") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','재고관리','".$h_col1."','".$h_col2."','".$h_action."','m04')");		
	}else if ($h_action=="앵글로 이동") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','재고관리','".$h_col1."','".$h_col2."','".$h_action."','m04')");		
	}else if ($h_action=="거래처를 등록") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','A', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1." 이름으로 ".$h_col2."','','".$h_action."','m06')");		
	}else if ($h_action=="거래처명을 변경") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','B', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
	}else if ($h_action=="출고완료") {
		$stmt_history = $conn->prepare("INSERT INTO RTU_history (partner_id, h_type, h_name, h_id, h_date, h_ip, h_location, h_col1, h_col2, h_action, h_loc_code) VALUES ('".$partner_id."','C', '".$_SESSION['admin_name']."','".$_SESSION['admin_id']."','".$date."','".$_SERVER['REMOTE_ADDR']."','입출고관리','".$h_col1."','".$h_col2."','".$h_action."','m06')");		
	}

    $stmt_history->execute();
}










 
 /////////////////////////////////////    이하 관리자모드 ///////////////////////////////////////////////////////

class AdminManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

	
	// (미사용 함수) 사용자 등록
	public function registerUser(string $admin_id, string $admin_pw, string $admin_role): void {
		$admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);
		$partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.
		$stmt = $this->conn->prepare("INSERT INTO RTU_admin (partner_id, admin_id, admin_pw, admin_role) VALUES (:partner_id,:admin_id, :admin_pw, :admin_role)");
		$stmt->bindParam(':admin_id', $admin_id);
		$stmt->bindParam(':admin_pw', $admin_pw);
		$stmt->bindParam(':admin_role', $admin_role);
		$stmt->bindParam(':partner_id', $partner_id); // partner_id 바인딩
		$stmt->execute();
	}
		
	

    //pt 로그인
    public function loginAdmin(int $partner_id, string $admin_id, string $admin_pw): bool {
        $stmt = $this->conn->prepare("SELECT * FROM RTU_admin WHERE partner_id = :partner_id and admin_id = :admin_id and admin_use = 'Y' and delYN = 'N'");
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


		// 세션이 이미 시작되었는지 확인
		if (session_status() == PHP_SESSION_NONE) {
			// 세션 이름 설정
			if ($user['admin_id'] == "sysid") {
			//	session_name("SYSID_SESSION");
			} else {
			//	session_name("ADMIN_SESSION");
			}
            //error_log("Session Name: " . session_name());
			// 세션 시작
			session_start();
		}

			$sessionLifetime = 86400; // 24시간(1일)
			
			// 세션 쿠키 수명 설정
			session_set_cookie_params($sessionLifetime);
			
			// 세션 캐시 제어 설정
			session_cache_limiter('private'); 
			
			// PHP 세션 설정 변경
			ini_set("session.cookie_lifetime", $sessionLifetime); 
			ini_set("session.cache_expire", $sessionLifetime); 
			ini_set("session.gc_maxlifetime", $sessionLifetime); 


        if ($user && password_verify($admin_pw, $user['admin_pw'])) {
			

			$_SESSION['admin_idx']   = $user['admin_idx'];
			$_SESSION['partner_id']    = $user['partner_id'];
			$_SESSION['admin_id']    = $user['admin_id'];
			$_SESSION['admin_name']    = $user['admin_name'];
			$_SESSION['admin_role']  = $user['admin_role'];   
 
			if ($user['admin_id']=="sysid") {
				$_SESSION['sys'] = "Y";
			}else{
				$_SESSION['sys'] = "N";
			}

			add_history('A','로그인 성공',$_SESSION['admin_role'],'');    
			return true;    
			
        } else {
			
			if (session_status() === PHP_SESSION_ACTIVE) {
				//session_unset();
				//session_destroy();
			}			
		 	 
			$_SESSION['partner_id'] = $partner_id;
 			$admin_name_search = admin_id_to_admin_name_with_partner_id($admin_id,$partner_id);			
			add_history('A','로그인 실패',$admin_name_search[0]['admin_name'],$admin_id);		
 
            return false;
        }
    }




    //pt 로그인   sysid로부터 관리자 로그인
    public function loginAdmin_from_sys(int $partner_id, string $admin_id): bool {
        $stmt = $this->conn->prepare("SELECT * FROM RTU_admin WHERE partner_id = :partner_id and admin_id = :admin_id and admin_use = 'Y' and delYN = 'N'");
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


		// 세션이 이미 시작되었는지 확인
		if (session_status() == PHP_SESSION_NONE) {

			// 세션 시작
			session_start();
		}

			$sessionLifetime = 86400; // 24시간(1일)
			
			// 세션 쿠키 수명 설정
			session_set_cookie_params($sessionLifetime);
			
			// 세션 캐시 제어 설정
			session_cache_limiter('private'); 
			
			// PHP 세션 설정 변경
			ini_set("session.cookie_lifetime", $sessionLifetime); 
			ini_set("session.cache_expire", $sessionLifetime); 
			ini_set("session.gc_maxlifetime", $sessionLifetime); 


        if ($user) {
			

			$_SESSION['admin_idx']   = $user['admin_idx'];
			$_SESSION['partner_id']    = $user['partner_id'];
			$_SESSION['admin_id']    = $user['admin_id'];
			$_SESSION['admin_name']    = $user['admin_name'];
			$_SESSION['admin_role']  = $user['admin_role'];   
			$_SESSION['sys'] = "N";


			add_history('A','로그인 성공',$_SESSION['admin_role'],'');    
			return true;    
			
        } else {
			
			if (session_status() === PHP_SESSION_ACTIVE) {
				//session_unset();
				//session_destroy();
			}			
		 	 
			$_SESSION['partner_id'] = $partner_id;
 			$admin_name_search = admin_id_to_admin_name_with_partner_id($admin_id,$partner_id);			
			add_history('A','로그인 실패',$admin_name_search[0]['admin_name'],$admin_id);		
 
            return false;
        }
    }



    public function sysloginAdmin(int $partner_id, string $admin_id, string $admin_pw): bool {
   	 
        $stmt = $this->conn->prepare("SELECT * FROM RTU_admin WHERE partner_id = :partner_id and admin_id = :admin_id and admin_use = 'Y' and delYN = 'N'");
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


		// 세션이 이미 시작되었는지 확인
		if (session_status() == PHP_SESSION_NONE) {
			// 세션 시작
			session_start();					
		}

			$sessionLifetime = 86400; // 24시간(1일)
			
			// 세션 쿠키 수명 설정
			session_set_cookie_params($sessionLifetime);
			
			// 세션 캐시 제어 설정
			session_cache_limiter('private'); 
			
			// PHP 세션 설정 변경
			ini_set("session.cookie_lifetime", $sessionLifetime); 
			ini_set("session.cache_expire", $sessionLifetime); 
			ini_set("session.gc_maxlifetime", $sessionLifetime); 
		
        if ($user && password_verify($admin_pw, $user['admin_pw'])) {
 			
			$_SESSION['sys_admin_idx']   = $user['admin_idx'];
			$_SESSION['sys_partner_id']    = $user['partner_id'];
			$_SESSION['sys_admin_id']    = $user['admin_id'];
			$_SESSION['sys_admin_name']    = $user['admin_name'];
			$_SESSION['sys_admin_role']  = $user['admin_role'];   
 
			if ($user['partner_id']=="1111") {
				$_SESSION['sys'] = "Y";
			}else{
				$_SESSION['sys'] = "N";
			}

			//add_history('A','로그인 성공',$_SESSION['sys_admin_role'],'');    
 			return true;    
			
        } else {

			if (session_status() === PHP_SESSION_ACTIVE) {
				//session_unset();
				//session_destroy();
			}			
            //echo "로그인 실패"; exit();
			$_SESSION['sys_partner_id'] = $partner_id;
 			$admin_name_search = admin_id_to_admin_name_with_partner_id($admin_id,$partner_id);	
			add_history('A','로그인 실패',$admin_name_search[0]['admin_name'],$admin_id);		
 			       	   
            return false;
        }

    }


    // 사용자 로그아웃
    public function logoutAdmin(): void {     
	    
        session_start();
        add_history('A', '로그아웃', $_SESSION['admin_role'], '');                    
        
        //session_unset();
        //session_destroy();
		
		// 특정 세션 변수를 제외한 모든 세션 변수 제거
		foreach ($_SESSION as $key => $value) {
			if ($key !== 'sys_admin_id' && $key !== 'sys_admin_idx' && $key !== 'sys_partner_id' && $key !== 'sys_admin_name' && $key !== 'sys_admin_role') {
				unset($_SESSION[$key]);
			}
		}		
    }

    // 사용자 로그아웃
    public function syslogoutAdmin(): void {     
	    
        session_start();
       // add_history('A', '로그아웃', $_SESSION['admin_role'], '');     
	   
		// 특정 세션 변수만 제거
		if (isset($_SESSION['sys_admin_id'])) {
			unset($_SESSION['sys_admin_id']);
		}
		if (isset($_SESSION['sys_admin_idx'])) {
			unset($_SESSION['sys_admin_idx']);
		}
		if (isset($_SESSION['sys_partner_id'])) {
			unset($_SESSION['sys_partner_id']);
		}
		if (isset($_SESSION['sys_admin_name'])) {
			unset($_SESSION['sys_admin_name']);
		}
		if (isset($_SESSION['sys_admin_role'])) {
			unset($_SESSION['sys_admin_role']);
		}

    }

    // 사용자 권한 확인
    public function checkUserRole(): array|false {
        session_start();

        if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
            return false; // 사용자가 로그인하지 않았음
        }

        $stmt = $this->conn->prepare("SELECT admin_role FROM RTU_admin WHERE partner_id = :partner_id and admin_id = :admin_id");
        $stmt->bindParam(':partner_id', $_SESSION['partner_id']);
        $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        $stmt->execute();
        $userRole = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $userRole; // 권한 반환
    }
}

$adminManager = new AdminManager($conn);
 
 
function admin_id_to_admin_name(string $admin_id): array {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옴
    
    $stmt = $conn->prepare("SELECT admin_name FROM RTU_admin WHERE partner_id = :partner_id and admin_id = :admin_id");
    $stmt->bindParam(':partner_id', $partner_id);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function admin_id_to_admin_name_with_partner_id(string $admin_id,int $partner_id): array {
    global $conn;
    //$partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옴
    
    $stmt = $conn->prepare("SELECT admin_name FROM RTU_admin WHERE partner_id = :partner_id and admin_id = :admin_id");
    $stmt->bindParam(':partner_id', $partner_id);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 슬라이드메뉴 * //

// 관리자모드 현접속자 권한명 출력
function cate_name() {
    global $conn;

    // 세션에서 partner_id와 admin_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];
    $admin_id = $_SESSION['admin_id'];
	
	if ($_SESSION['partner_id'] == "") {
		 $partner_id = $_SESSION['sys_partner_id'];
	}
	if ($_SESSION['admin_id'] == "") {
		 $admin_id = $_SESSION['sys_admin_id'];
	}

    // SQL 쿼리 준비 및 실행 (한 줄로 작성)
    $stmt = $conn->prepare("SELECT c.cate_name as cate_name FROM RTU_admin_cate c JOIN RTU_admin a ON c.cate_admin_role = a.admin_role WHERE a.admin_id = :admin_id AND a.partner_id = :partner_id");
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 공통다중사용 * //

// 창고앵글 일괄삭제 setting 값 가져오기
 
// 사용자 비밀번호 초기화
function user_reset_pw($user_id) {
    global $conn;
    
    // 비밀번호 해시 생성
    $user_pw = password_hash('1234', PASSWORD_DEFAULT);
    
    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];
    
    // SQL 쿼리 준비 및 실행 (한 줄로 작성)
    $stmt = $conn->prepare("UPDATE RTU_user SET user_pw = :user_pw WHERE user_id = :user_id AND partner_id = :partner_id");
    $stmt->bindParam(':user_pw', $user_pw);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
}

  
// 사용자 활성/비활성 변경
function user_change_state($user_id, $user_use) {
    global $conn;

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];

    // SQL 쿼리 준비 및 실행 (한 줄로 작성)
    $stmt = $conn->prepare("UPDATE RTU_user SET user_use = :user_use WHERE user_id = :user_id AND partner_id = :partner_id");
    $stmt->bindParam(':user_use', $user_use);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
}

// 사용자 비밀번호 변경
function user_change_pw($user_id, $user_pw) {
    global $conn;

    // 비밀번호 해시 생성
    $user_pw = password_hash($user_pw, PASSWORD_DEFAULT);

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];

    // SQL 쿼리 준비 및 실행 (한 줄로 작성)
    $stmt = $conn->prepare("UPDATE RTU_user SET user_pw = :user_pw WHERE user_id = :user_id AND partner_id = :partner_id");
    $stmt->bindParam(':user_pw', $user_pw);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
}


// 운영자 비밀번호 초기화
function reset_pw($admin_id) {
    global $conn;

    // 비밀번호 해시 생성
    $admin_pw = password_hash('1234', PASSWORD_DEFAULT);

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];

    // SQL 쿼리 준비 및 실행 (한 줄로 작성)
    $stmt = $conn->prepare("UPDATE RTU_admin SET admin_pw = :admin_pw WHERE admin_id = :admin_id AND partner_id = :partner_id");
    $stmt->bindParam(':admin_pw', $admin_pw);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
}

// 운영자 비밀번호 변경
function change_pw($admin_id, $admin_pw) {
    global $conn;
    try {
        // 트랜잭션 시작
        $conn->beginTransaction();

		// 비밀번호 해시 생성
		$admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);

		// 세션에서 partner_id를 가져옵니다.
		$partner_id = $_SESSION['partner_id'];

		// SQL 쿼리 준비 및 실행 (한 줄로 작성)
		$stmt = $conn->prepare("UPDATE RTU_admin SET admin_pw = :admin_pw WHERE admin_id = :admin_id AND partner_id = :partner_id");
		$stmt->bindParam(':admin_pw', $admin_pw);
		$stmt->bindParam(':admin_id', $admin_id);
		$stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt->execute();
		
		$sql = " INSERT INTO RTU_pw_setting (partner_id, admin_id, set_state, set_rdate) ";
		$sql .= " SELECT :partner_id, :admin_id, 'Y', :set_rdate ";
		$sql .= " WHERE NOT EXISTS ( ";
		$sql .= " SELECT 1 ";
		$sql .= " FROM RTU_pw_setting ";
		$sql .= " WHERE partner_id = :partner_id and admin_id= :admin_id )";		
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':partner_id', $partner_id);
		$stmt->bindParam(':admin_id', $admin_id);
		$stmt->bindParam(':set_rdate', date("Y-m-d H:i:s"));	
		$stmt->execute();
        // 트랜잭션 커밋
        $conn->commit();
		
    } catch (Exception $e) {
        // 오류 발생 시 트랜잭션 롤백
        $conn->rollBack();
        // 오류 메시지 또는 로깅
        echo "Error: " . $e->getMessage();
    }		
}

// 운영자 비밀번호 변경
function sys_change_pw($admin_id, $admin_pw) {
    global $conn;

    // 비밀번호 해시 생성
    $admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['sys_partner_id'];

    // SQL 쿼리 준비 및 실행 (한 줄로 작성)
    $stmt = $conn->prepare("UPDATE RTU_admin SET admin_pw = :admin_pw WHERE admin_id = :admin_id AND partner_id = :partner_id");
    $stmt->bindParam(':admin_pw', $admin_pw);
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
}


// SYS 운영자 비밀번호 변경
function syschange_pw($admin_id, $admin_pw) {
    global $conn;
    try {
        // 트랜잭션 시작
        $conn->beginTransaction();

		// 비밀번호 해시 생성
		$admin_pw = password_hash($admin_pw, PASSWORD_DEFAULT);
		
		// 세션에서 partner_id를 가져옵니다.
		$partner_id = str_replace('sysid','',$admin_id);
		
		$SQL = "UPDATE RTU_admin SET admin_pw = :admin_pw WHERE admin_id = :admin_id";
		// SQL 쿼리 준비 및 실행 (한 줄로 작성)
		$stmt = $conn->prepare($SQL);
		$stmt->bindParam(':admin_pw', $admin_pw);
		$stmt->bindParam(':admin_id', $admin_id);
		$stmt->execute();
		
		//$sql = "INSERT INTO RTU_pw_setting (partner_id, admin_id, set_state, set_rdate) VALUES (:partner_id, :admin_id,'Y',:set_rdate)";
		
		// SQL 쿼리 준비 및 실행
		$sql = " INSERT INTO RTU_pw_setting (partner_id, admin_id, set_state, set_rdate) ";
		$sql .= " SELECT :partner_id, :admin_id, 'Y', :set_rdate ";
		$sql .= " WHERE NOT EXISTS ( ";
		$sql .= " SELECT 1 ";
		$sql .= " FROM RTU_pw_setting ";
		$sql .= " WHERE partner_id = :partner_id and admin_id= :admin_id )";		
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':partner_id', $partner_id);
		$stmt->bindParam(':admin_id', $admin_id);
		$stmt->bindParam(':set_rdate', date("Y-m-d H:i:s"));	
		$stmt->execute();
        // 트랜잭션 커밋
        $conn->commit();	
		
    } catch (Exception $e) {
        // 오류 발생 시 트랜잭션 롤백
        $conn->rollBack();
        // 오류 메시지 또는 로깅
        echo "Error: " . $e->getMessage();
    }			
}

// 권한 체크
function permission_ck($where, $type, $who) {
    global $conn;

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];

    // SQL 쿼리 준비 및 실행 (한 줄로 작성)
    $stmt = $conn->prepare("SELECT CASE WHEN COUNT(*) = 0 THEN 'F' ELSE 'T' END AS pm_rst FROM RTU_access_crud WHERE (access_name = :where AND access_type = :type AND access_value = :who) OR (:who = '100' AND partner_id = :partner_id)");
    $stmt->bindParam(':where', $where);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':who', $who);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}


// 제품분류명 가져오기
function cate_id_to_cate_name($item_cate) {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.
	
    $stmt = $conn->prepare("SELECT cate_name FROM RTU_cate WHERE cate_id = :item_cate AND partner_id = :partner_id");
    $stmt->bindParam(':item_cate', $item_cate, PDO::PARAM_INT);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn(); // 단일 값을 반환합니다.
}


// 제품분류번호 가져오기
function cate_name_to_cate_id($item_cate) {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.
    $stmt = $conn->prepare("SELECT cate_id FROM RTU_cate WHERE cate_name = :item_cate AND partner_id = :partner_id");
    $stmt->bindParam(':item_cate', $item_cate, PDO::PARAM_STR);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // 단일 행을 가져옴

    // 결과가 있는지 확인하고 cate_id를 반환
    if ($result) {
        return $result['cate_id'];
    } else {
        return null; // 결과가 없는 경우 null 반환
    }	
}

// 제품분류번호 가져오기
function cate_name_to_cate_id_use($item_cate) {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.
    $stmt = $conn->prepare("SELECT cate_id FROM RTU_cate WHERE cate_name = :item_cate AND partner_id = :partner_id AND cate_use = 'Y' AND delYN = 'N'");
    $stmt->bindParam(':item_cate', $item_cate, PDO::PARAM_STR);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // 단일 행을 가져옴

    // 결과가 있는지 확인하고 cate_id를 반환
    if ($result) {
        return $result['cate_id'];
    } else {
        return null; // 결과가 없는 경우 null 반환
    }	
}


// 엑셀업로드시, 카테고리 필요시 생성
function add_category($partner_id, $cate_name, $date) {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.

    $sql = "INSERT INTO RTU_cate (partner_id, cate_name, cate_rdate, cate_use) VALUES (:partner_id, :cate_name, :cate_rdate, 'Y')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':partner_id', $partner_id);
    $stmt->bindParam(':cate_name', $cate_name);
    $stmt->bindParam(':cate_rdate', $date);

    if ($stmt->execute()) {
        return $conn->lastInsertId();
    } else {
        throw new Exception('카테고리 추가 중 오류 발생');
    }
}










// 입출고 거래처명 가져오기
function company_id_to_company_name($item_cate) {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.
    $stmt = $conn->prepare("SELECT cate_name FROM RTU_company WHERE cate_id = :item_cate AND partner_id = :partner_id");
    $stmt->bindParam(':item_cate', $item_cate, PDO::PARAM_INT);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}

// 입출고 거래처 ID 가져오기
function company_name_to_company_id($item_cate) {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.
    $stmt = $conn->prepare("SELECT cate_id FROM RTU_company WHERE cate_name = :item_cate AND partner_id = :partner_id");
    $stmt->bindParam(':item_cate', $item_cate);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}


// pt 창고 목록 가져오기
 

// 창고 목록 가져오기
 


// 창고명 업데이트 중복검사
 

// 창고명 업데이트  
 
// 창고의 앵글 목록 가져오기
 

// 앵글로 재고이동 또는 제품입고 등록 시 거래처 리스트 불러오기
 

// 제품명 이름추출
 

// 미지정 창고 id 번호 찾기
 

// 미지정 앵글 id 번호 찾기
 

 // 제품바코드 이름추출
 
// 로그인 성공후, 비번 변경 유무 검사 (관리자 생성후, 관리자 비번 변경요청) 0 = 변경요청 / 1 =  pass
function is_change_pw(){
    global $conn;

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];	
	$h_id		= "sysid".$_SESSION['partner_id'];	
	
    $stmt = $conn->prepare("SELECT COUNT(*) > 0 AS pw_setting_exist FROM  RTU_history h WHERE h.partner_id = :partner_id and h.h_id = :h_id AND h.h_action = '로그인 성공' AND EXISTS (  SELECT 1 FROM RTU_pw_setting p  WHERE p.partner_id = h.partner_id AND p.admin_id = h.h_id)");
    $stmt->bindParam(':h_id', $h_id);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);		
}






// 운영자 목록 가져오기
function sys_getRTU_users($start_record_number, $itemsPerPage, $search, $SearchString) {
    global $conn;
    $partner_id = $_SESSION['sys_partner_id']; // partner_id를 세션에서 가져옵니다.
	
    // 검색조건
    $condition_sql = "";
    if ($SearchString != "") {
        $condition_sql = " AND a.$search LIKE :search_string ";
    }

    //  where 1 = 1 ".$condition_sql."
    if ($_SESSION['sys_admin_role'] == "100") {
        $stmt = $conn->prepare("SELECT * FROM RTU_admin a JOIN RTU_admin_cate c ON a.admin_role = c.cate_admin_role and a.partner_id = c.partner_id  where a.partner_id = :partner_id  $condition_sql ORDER BY c.cate_admin_role DESC LIMIT :start_record_number, :itemsPerPage");
    } else {
        $stmt = $conn->prepare("SELECT * FROM RTU_admin a JOIN RTU_admin_cate c ON a.admin_role = c.cate_admin_role and a.partner_id = c.partner_id  where a.partner_id = :partner_id and admin_role < 100 $condition_sql ORDER BY c.cate_admin_role DESC LIMIT :start_record_number, :itemsPerPage");
    }

    if ($SearchString != "") {
        $stmt->bindValue(':search_string', '%' . $SearchString . '%');
    }
    $stmt->bindValue(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    // 파라미터 바인딩
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
	$stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




// 운영자 목록 가져오기
function sys_getRTU_admin(int $start_record_number, int $itemsPerPage, string $search_add): array {
    global $conn;
    $partner_id = $_SESSION['sys_partner_id']; // partner_id를 세션에서 가져옵니다.

    // 쿼리에서 LIMIT 절의 변수 바인딩을 제거합니다
    $sql = "SELECT a.admin_idx as admin_idx, a.partner_id as partner_id, a.admin_id as admin_id, a.admin_name as admin_name, a.admin_role as admin_role, a.admin_rdate as admin_rdate,";
    $sql .= " a.admin_use, a.delYN, ";
    $sql .= " CASE ";
    $sql .= " WHEN p.set_state = 'Y' THEN 'Y' ";
    $sql .= " ELSE 'N' ";
    $sql .= " END AS set_state ";
    $sql .= " FROM RTU_admin a LEFT JOIN RTU_pw_setting p ";
    $sql .= " ON a.admin_id = p.admin_id ";
    $sql .= " WHERE a.admin_use <> 'D' and a.partner_id <> :partner_id AND a.admin_id LIKE 'sysid%' ";
    $sql .= " $search_add ORDER BY a.partner_id DESC LIMIT :start_record_number, :itemsPerPage";
    
    // SQL 실행 준비
    $stmt = $conn->prepare($sql);
    
    // 바인딩할 변수들
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    // 직접 쿼리문에 값 대입
    $stmt->bindValue(':start_record_number', (int) $start_record_number, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', (int) $itemsPerPage, PDO::PARAM_INT);
    
    // SQL 실행
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// 사용자 추가
function addUser($user_id, $user_name) {
    global $conn;
    $user_pw = password_hash('1234', PASSWORD_DEFAULT);
	$partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.
	$user_token = strrev(substr(password_hash(date("Y-m-d H:i:s"), PASSWORD_DEFAULT), -32)); 
    $stmt = $conn->prepare("INSERT INTO RTU_user (partner_id, user_id, user_name, user_pw, user_rdate, user_token) VALUES (:partner_id, :user_id, :user_name, :user_pw, :user_rdate, :user_token)");
	$stmt->bindParam(':partner_id', $partner_id); // partner_id 바인딩
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
	$partner_id = $_SESSION['partner_id']; // 세션에서 partner_id를 가져옵니다.
    $stmt = $conn->prepare("INSERT INTO RTU_admin (partner_id, admin_id, admin_name, admin_pw, admin_role, admin_rdate) VALUES (:partner_id, :admin_id, :admin_name, :admin_pw, :admin_role, :admin_rdate)");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':admin_name', $admin_name);
    $stmt->bindParam(':admin_pw', $admin_pw);
    $stmt->bindParam(':admin_role', $cate_admin_role);
	$stmt->bindParam(':partner_id', $partner_id); // partner_id 바인딩
    $stmt->bindValue(':admin_rdate', date("Y-m-d H:i:s"));
    $stmt->execute();
}

function ck_cate_cnt($role_num) {
    global $conn;

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];

    // SQL 쿼리 준비
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(cate_id), '0') AS role_cnt FROM `RTU_admin_cate` WHERE delYN = 'N' AND cate_admin_role = :role_num AND partner_id = :partner_id");

    // 파라미터 바인딩
    $stmt->bindParam(':role_num', $role_num);
    $stmt->bindParam(':partner_id', $partner_id);

    // 쿼리 실행
    $stmt->execute();

    // 결과 반환
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 사용자등록 아이디 중복검사
function ck_user2_cnt($user_id) {
    global $conn;

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];

    // SQL 쿼리 준비
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(user_id), '0') AS user_cnt FROM `RTU_user` WHERE delYN = 'N' AND user_id = :user_id AND partner_id = :partner_id");

    // 파라미터 바인딩
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':partner_id', $partner_id);

    // 쿼리 실행
    $stmt->execute();

    // 결과 반환
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 운영자등록 아이디 중복검사
function ck_user_cnt($admin_id) {
    global $conn;

    // 세션에서 partner_id를 가져옵니다.
    $partner_id = $_SESSION['partner_id'];

    // SQL 쿼리 준비
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(admin_id), '0') AS user_cnt FROM `RTU_admin` WHERE delYN = 'N' AND admin_id = :admin_id AND partner_id = :partner_id");

    // 파라미터 바인딩
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->bindParam(':partner_id', $partner_id);

    // 쿼리 실행
    $stmt->execute();

    // 결과 반환
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




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
        $pagination .= ' <a href="' . $url . '?page=' . $i . '&itemsPerPage='.$itemsPerPage.$addpara.'" class="num' . $activeClass . '" style="font-size:18px;color:#666;text-decoration: none">' . ($i == $currentPage ? '<span style="font-size:20px;color:#000"> <strong>' . $i . '</strong></span>' : $i) . ' </a>&nbsp;';
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



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// * 운영자관리 * //
// 조회
function get_admin_cate_add_cate_use($start_record_number, $itemsPerPage, $sql_cate_use) {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // partner_id를 세션에서 가져옵니다.

   $stmt = $conn->prepare("SELECT cate_id, cate_name, cate_use, cate_expose, cate_rdate, cate_comment, cate_admin_role, (SELECT COUNT(*) FROM RTU_admin WHERE partner_id = :partner_id  and admin_use='Y' AND RTU_admin_cate.cate_admin_role = RTU_admin.admin_role) as use_admin_role_cnt, (SELECT COUNT(*) FROM RTU_admin WHERE partner_id = :partner_id and admin_use='N' AND RTU_admin_cate.cate_admin_role = RTU_admin.admin_role) as notuse_admin_role_cnt  FROM RTU_admin_cate WHERE  partner_id = :partner_id  and cate_admin_role <> 100 AND cate_use $sql_cate_use AND cate_admin_role <= :admin_role ORDER BY cate_admin_role DESC LIMIT :start_record_number, :itemsPerPage");
    $stmt->bindParam(':admin_role', $_SESSION['admin_role'], PDO::PARAM_INT);
    $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_admin_cate_reg($start_record_number, $itemsPerPage) {
    global $conn;
    $partner_id = $_SESSION['partner_id']; // partner_id를 세션에서 가져옵니다.

    $stmt = $conn->prepare("SELECT * FROM RTU_admin_cate WHERE partner_id = :partner_id   AND  cate_admin_role <= :admin_role AND cate_use = 'Y' AND cate_expose = 'Y' ORDER BY cate_admin_role DESC LIMIT :start_record_number, :itemsPerPage");
    $stmt->bindParam(':admin_role', $_SESSION['admin_role'], PDO::PARAM_INT);
    $stmt->bindParam(':start_record_number', $start_record_number, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function del_admin_partner($partner_id){
	//echo "<script>alert('".$partner_id."');</script>";
    global $conn;
		
		//////////////////////////////////////////////////////////////////////////
		$stmt1 = $conn->prepare("DELETE FROM RTU_pw_setting WHERE partner_id = :partner_id");
		$stmt1->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt1->execute();	
 
		//////////////////////////////////////////////////////////////////////////
		$stmt4 = $conn->prepare("DELETE FROM RTU_setting WHERE partner_id = :partner_id");
		$stmt4->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt4->execute();	

		//////////////////////////////////////////////////////////////////////////
		$stmt5 = $conn->prepare("DELETE FROM RTU_admin_cate WHERE partner_id = :partner_id");
		$stmt5->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt5->execute();	

		//////////////////////////////////////////////////////////////////////////
		$stmt6 = $conn->prepare("DELETE FROM RTU_access_crud WHERE partner_id = :partner_id");
		$stmt6->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt6->execute();	
		
		//////////////////////////////////////////////////////////////////////////
		$stmt7 = $conn->prepare("DELETE FROM RTU_admin WHERE partner_id = :partner_id");
		$stmt7->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt7->execute();	
 
		//////////////////////////////////////////////////////////////////////////
		$stmt8 = $conn->prepare("DELETE FROM RTU_cate WHERE partner_id = :partner_id");
		$stmt8->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt8->execute();	
 
		//////////////////////////////////////////////////////////////////////////
		$stmt10 = $conn->prepare("DELETE FROM RTU_history WHERE partner_id = :partner_id");
		$stmt10->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt10->execute();	
					
		//////////////////////////////////////////////////////////////////////////
		$stmt16 = $conn->prepare("DELETE FROM RTU_user WHERE partner_id = :partner_id");
		$stmt16->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
		$stmt16->execute();	
		
}


function add_admin_partner($partner_id, $admin_id, $admin_name) {
    global $conn;
    $admin_pw = password_hash('1234', PASSWORD_DEFAULT);
 	$admin_role = 100;
		
    try {	
		
	
		//트랜잭션을 사용하여 코드를 개선
        // 트랜잭션 시작
        $conn->beginTransaction();			
		//////////////////////////////////////////////////////////////////////////
		
        // 데이터가 이미 존재하는지 확인
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM RTU_admin WHERE partner_id = :partner_id AND admin_id = :admin_id");
        $stmtCheck->bindParam(':partner_id', $partner_id);
        $stmtCheck->bindParam(':admin_id', $admin_id);
        $stmtCheck->execute();
        $count = $stmtCheck->fetchColumn();

        if ($count == 0) {
            // `RTU_admin` 테이블에 데이터 삽입
            $stmt = $conn->prepare("INSERT INTO RTU_admin (partner_id, admin_id, admin_name, admin_pw, admin_role, admin_rdate) 
                VALUES (:partner_id, :admin_id, :admin_name, :admin_pw, :admin_role, :admin_rdate)");
            $stmt->bindParam(':partner_id', $partner_id);
            $stmt->bindParam(':admin_id', $admin_id);
            $stmt->bindParam(':admin_name', $admin_name);
            $stmt->bindParam(':admin_pw', $admin_pw);
            $stmt->bindParam(':admin_role', $admin_role);
            $stmt->bindValue(':admin_rdate', date("Y-m-d H:i:s"));
            $stmt->execute();
        }		
		
		 //////////////////////////////////////////////////////////////////////////

		$SQL2 ="INSERT INTO `RTU_access_crud` (`partner_id`, `access_id`, `access_name`, `access_type`, `access_value`, `access_rdate`, `access_use`, `access_order`, `delYN`) VALUES ";
		$SQL2 = $SQL2." (:partner_id, 3, '제품', 'D', 99, :admin_rdate, 'Y', 3, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 5, '입고지시관리', 'D', 99, :admin_rdate, 'Y', 6, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 3, '제품', 'U', 99, :admin_rdate, 'Y', 3, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 4, '제품카테고리', 'U', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 1, '창고', 'W', 99, :admin_rdate, 'Y', 1, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 4, '제품카테고리', 'D', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 1, '창고', 'U', 99, :admin_rdate, 'Y', 1, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 1, '창고', 'D', 99, :admin_rdate, 'Y', 1, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 1, '창고', 'R', 99, :admin_rdate, 'Y', 1, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 4, '제품카테고리', 'W', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 5, '입고지시관리', 'U', 99, :admin_rdate, 'Y', 6, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 6, '출고지시관리', 'U', 99, :admin_rdate, 'Y', 7, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 6, '출고지시관리', 'D', 99, :admin_rdate, 'Y', 7, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 6, '출고지시관리', 'W', 99, :admin_rdate, 'Y', 7, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 3, '제품', 'W', 99, :admin_rdate, 'Y', 3, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 3, '제품', 'R', 99, :admin_rdate, 'Y', 3, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 7, '재고', 'R', 99, :admin_rdate, 'Y', 5, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 7, '재고', 'W', 99, :admin_rdate, 'Y', 5, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 7, '재고', 'U', 99, :admin_rdate, 'Y', 5, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 7, '재고', 'D', 99, :admin_rdate, 'Y', 5, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 6, '출고지시관리', 'R', 99, :admin_rdate, 'Y', 7, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 5, '입고지시관리', 'W', 99, :admin_rdate, 'Y', 6, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 5, '입고지시관리', 'R', 99, :admin_rdate, 'Y', 6, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 4, '제품카테고리', 'R', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 8, '운영자목록', 'R', 99, :admin_rdate, 'Y', 12, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 8, '운영자목록', 'W', 99, :admin_rdate, 'Y', 12, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 8, '운영자목록', 'U', 99, :admin_rdate, 'Y', 12, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 8, '운영자목록', 'D', 99, :admin_rdate, 'Y', 12, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 9, '운영자분류명관리', 'R', 99, :admin_rdate, 'Y', 13, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 9, '운영자분류명관리', 'W', 99, :admin_rdate, 'Y', 13, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 9, '운영자분류명관리', 'U', 99, :admin_rdate, 'Y', 13, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 9, '운영자분류명관리', 'D', 99, :admin_rdate, 'Y', 13, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 10, '권한관리목록', 'W', 99, :admin_rdate, 'Y', 14, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 10, '권한관리목록', 'U', 99, :admin_rdate, 'Y', 14, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 11, '시스템설정', 'R', 99, :admin_rdate, 'Y', 8, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 11, '시스템설정', 'U', 99, :admin_rdate, 'Y', 8, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 12, 'HISTORY', 'R', 99, :admin_rdate, 'Y', 10, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 12, 'HISTORY', 'W', 99, :admin_rdate, 'Y', 10, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 12, 'HISTORY', 'U', 99, :admin_rdate, 'Y', 10, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 12, 'HISTORY', 'D', 99, :admin_rdate, 'Y', 10, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 13, 'HOME입출고현황그래프', 'W', 99, :admin_rdate, 'Y', 9, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 13, 'HOME입출고현황그래프', 'U', 99, :admin_rdate, 'Y', 9, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 13, 'HOME입출고현황그래프', 'D', 99, :admin_rdate, 'Y', 9, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 11, '시스템설정', 'W', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 11, '시스템설정', 'D', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 2, '앵글', 'R', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 2, '앵글', 'W', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 2, '앵글', 'U', 99, :admin_rdate, 'Y', 4, 'N'), ";
		$SQL2 = $SQL2." (:partner_id, 2, '앵글', 'D', 99, :admin_rdate, 'Y', 4, 'N')";	
		
		$stmt2 = $conn->prepare($SQL2);
		$stmt2->bindParam(':partner_id', $partner_id);
		$stmt2->bindValue(':admin_rdate', date("Y-m-d H:i:s"));
		$stmt2->execute();
			
		//////////////////////////////////////////////////////////////////////////
	 
		$SQL3 ="INSERT INTO RTU_admin_cate (partner_id, cate_name, cate_use, cate_expose, cate_rdate, cate_comment, cate_admin_role,delYN) ";
		$SQL3 = $SQL3." SELECT :partner_id, cate_name, cate_use, cate_expose, :cate_rdate, cate_comment, cate_admin_role,delYN ";
		$SQL3 = $SQL3." FROM RTU_admin_cate WHERE partner_id = 1111 ";
		
		$stmt3 = $conn->prepare($SQL3);
		$stmt3->bindParam(':partner_id', $partner_id);
		$stmt3->bindValue(':cate_rdate', date("Y-m-d H:i:s"));
		$stmt3->execute();
	 
	 
		//////////////////////////////////////////////////////////////////////////
		
		$SQL4 ="INSERT INTO `RTU_setting` (`partner_id`, `set_id`, `set_name`, `set_comment`, `set_state`, `set_rdate`) VALUES ";
		$SQL4 = $SQL4." (:partner_id, 1, '창고앵글 일괄삭제', '창고삭제시 빈앵글도 함께 삭제합니다.', 'Y', :set_rdate), ";
		$SQL4 = $SQL4." (:partner_id, 2, '재고수량 0 노출', '재고수량이 0 (ZERO)일때도 목록에 표시합니다', 'N', :set_rdate), ";
		$SQL4 = $SQL4." (:partner_id, 3, '------', '------', 'N', :set_rdate) ";
		
		$stmt4 = $conn->prepare($SQL4);
		$stmt4->bindParam(':partner_id', $partner_id);
		$stmt4->bindValue(':set_rdate', date("Y-m-d H:i:s"));
		$stmt4->execute();
 
		//////////////////////////////////////////////////////////////////////////

		$SQL7 = "INSERT INTO `RTU_admin` (`partner_id`, `admin_id`, `admin_name`, `admin_pw`, `admin_role`, `admin_rdate`, `admin_use`, `delYN`) VALUES ";
		$SQL7 = $SQL7." (:partner_id, 'admin61', '운영자61', :admin_pw, 61, :admin_rdate, 'Y', 'N'),  ";
		$SQL7 = $SQL7." (:partner_id, 'admin41', '운영자41', :admin_pw, 41, :admin_rdate, 'Y', 'N'), "; 
		$SQL7 = $SQL7." (:partner_id, 'admin92', '김길동', :admin_pw, 92, :admin_rdate, 'Y', 'N'),  ";
		$SQL7 = $SQL7." (:partner_id, 'admin99', '백길동', :admin_pw, 99, :admin_rdate, 'Y', 'N')";
		//$SQL7 = $SQL7." (:partner_id, :admin_id, :admin_name, :admin_pw, 100, :admin_rdate, 'Y', 'N')";
		
		// SQL 쿼리 준비
		$stmt7 = $conn->prepare($SQL7);

		// 파라미터 바인딩
		$stmt7->bindParam(':partner_id', $partner_id);
        //$stmt7->bindParam(':admin_id', $admin_id777);
		//$stmt7->bindParam(':admin_name', $admin_name);
		$stmt7->bindParam(':admin_pw', $admin_pw);
		$stmt7->bindValue(':admin_rdate', date("Y-m-d H:i:s"));

		// 실행
		$stmt7->execute();
		
        // 모든 쿼리가 성공하면 커밋
        $conn->commit();		
	
        // 성공 시 팝업 창 닫기 및 부모 창 새로 고침
        echo "<script> alert('파트너가 성공적으로 추가되었습니다.');  window.opener.location.reload(); window.close(); </script>";
    } catch (PDOException $e) {
		
        // 오류 발생 시 롤백
        $conn->rollBack();
        echo "Failed to add admin and related data: " . $e->getMessage();		
		
        // 실패 시 오류 메시지 출력
       // echo "<script>  alert('파트너 추가에 실패하였습니다. 다시 시도해 주세요.'); window.close(); </script>";
    }
	
		
}

 
// max 파트너코드 가져오기
function getRTU_max_partner_code(){
    global $conn;

    // 쿼리 준비 및 실행
    $stmt = $conn->prepare("SELECT IFNULL(MAX(partner_id), '2000') AS partner_id FROM RTU_admin");
    $stmt->execute();

    // 결과 반환
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function ck_partner_id_cnt($partner_id) {
    global $conn;
 
    // SQL 쿼리 준비
    $stmt = $conn->prepare("SELECT IFNULL(COUNT(partner_id), '2000') AS partner_id_cnt FROM `RTU_admin` where partner_id = :partner_id");

    // 파라미터 바인딩
    $stmt->bindParam(':partner_id', $partner_id);

    // 쿼리 실행
    $stmt->execute();

    // 결과 반환
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// RTU_Configuration 테이블에서 partner_id로 데이터 가져오기
function get_RTU_Config($partner_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM RTU_Configuration WHERE partner_id = :partner_id");
    $stmt->bindParam(':partner_id', $partner_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // 첫 번째 레코드 반환
}

?>		 