<?
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/ThingPlugAPI_only.php');
 

// POST 요청이 있을 때만 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 입력된 데이터 가져오기 (필수값 검사)
    $cid = $_POST['cid'] ?? null;
    $business_year = $_POST['business_year'] ?? null;
    $business_type = $_POST['business_type'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $user_name = $_POST['user_name'] ?? null;
    $install_confirm_date = !empty($_POST['install_confirm_date']) ? $_POST['install_confirm_date'] : date('Y-m-d');
    $install_confirm_num = $_POST['install_confirm_num'] ?? null;
    $Last_access_date = $_POST['Last_access_date'] ?? null;
    $Last_reception_date = $_POST['Last_reception_date'] ?? null;
    $rtu_company = $_POST['rtu_company'] ?? null;
    $rtu_company_tel = $_POST['rtu_company_tel'] ?? null;
    $communication_type = $_POST['communication_type'] ?? null;
    $lora_id = $_POST['lora_id'] ?? null;  // LoRa ID 추가
    $multi = !empty($_POST['multi']) ? $_POST['multi'] : '0';  // 기본값 설정  
    $address = $_POST['address'] ?? null;
    $latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : '0';  // 기본값 설정
    $longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : '0';  // 기본값 설정  
    $install_type = $_POST['install_type'] ?? null;
    $module_class = $_POST['module_class'] ?? null;
    $module_capacity = !empty($_POST['module_capacity']) ? $_POST['module_capacity'] : '0';  // 기본값 설정  
    $total_capacity = !empty($_POST['total_capacity']) ? $_POST['total_capacity'] : '0';  // 기본값 설정  
    $module_manufacturer = $_POST['module_manufacturer'] ?? null;
    $module_model = $_POST['module_model'] ?? null;
    $module_azimuth = $_POST['module_azimuth'] ?? null;
    $module_angle = $_POST['module_angle'] ?? null;
    $module_series = $_POST['module_series'] ?? null;
    $module_parallel = $_POST['module_parallel'] ?? null;
    $inverter_manufacturer = $_POST['inverter_manufacturer'] ?? null;
    $inverter_model = $_POST['inverter_model'] ?? null;
    $inverter_capacity = $_POST['inverter_capacity'] ?? null;
    $phase_type = $_POST['phase_type'] ?? null;
    $tracker_system = $_POST['tracker_system'] ?? null;
    $company_name = $_POST['company_name'] ?? null;
    $company_contact = $_POST['company_contact'] ?? null;
    $construction_date = $_POST['construction_date'] ?? null;
    $monitor_date = !empty($_POST['monitor_date']) ? $_POST['monitor_date'] : date('Y-m-d H:i:s');
    $as_request_enddate = $_POST['as_request_enddate'] ?? null;
    $grid_connection = $_POST['grid_connection'] ?? null;
    $building_use = $_POST['building_use'] ?? null;
    $special_note = $_POST['special_note'] ?? null;


 
	$missingFields = [];
	if (empty($cid)) $missingFields[] = 'CID';
	if (empty($business_year)) $missingFields[] = '사업 연도';
	if (empty($business_type)) $missingFields[] = '사업 구분';
	if (empty($user_name)) $missingFields[] = '사용자 이름';
	if (empty($rtu_company)) $missingFields[] = 'RTU 관리업체';
	if (empty($communication_type)) $missingFields[] = '통신 방식';
	if (empty($install_type)) $missingFields[] = '설치 유형';
	if (empty($lora_id)) $missingFields[] = 'LoRa ID';
	
	if (!empty($missingFields)) {
	    http_response_code(400);		
		echo json_encode(['status' => 'error', 'message' => '필수 입력값 누락: ' . implode(', ', $missingFields)], JSON_UNESCAPED_UNICODE);
		exit();
	}
 
 

 
 
 
 
 
 
 
 
	// 데이터베이스 업데이트 부분
	try {
		$sql = "UPDATE RTU_facility SET 
					business_year = :business_year, 
					business_type = :business_type, 
					user_id = :user_id, 
					user_name = :user_name, 
					install_confirm_date = IFNULL(:install_confirm_date, CURRENT_DATE), 
					install_confirm_num = :install_confirm_num, 
					Last_access_date = :Last_access_date, 
					Last_reception_date = :Last_reception_date, 
					rtu_company = :rtu_company, 
					rtu_company_tel = :rtu_company_tel, 
					communication_type = :communication_type, 
					lora_id = :lora_id, 
					multi = :multi, 
					address = :address, 
					latitude = :latitude, 
					longitude = :longitude, 
					install_type = :install_type, 
					module_class = :module_class, 
					module_capacity = :module_capacity, 
					total_capacity = :total_capacity, 
					module_manufacturer = :module_manufacturer, 
					module_model = :module_model, 
					module_azimuth = :module_azimuth, 
					module_angle = :module_angle, 
					module_series = :module_series, 
					module_parallel = :module_parallel, 
					inverter_manufacturer = :inverter_manufacturer, 
					inverter_model = :inverter_model, 
					inverter_capacity = :inverter_capacity, 
					phase_type = :phase_type, 
					tracker_system = :tracker_system, 
					company_name = :company_name, 
					company_contact = :company_contact, 
					construction_date = :construction_date, 
					monitor_date = :monitor_date, 
					as_request_enddate = :as_request_enddate, 
					grid_connection = :grid_connection, 
					building_use = :building_use, 
					special_note = :special_note
				WHERE cid = :cid";
		
		$stmt = $conn->prepare($sql);


		// 각 파라미터를 SQL 쿼리에 바인딩
		$stmt->bindParam(':cid', $cid);
		$stmt->bindParam(':business_year', $business_year);
		$stmt->bindParam(':business_type', $business_type);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->bindParam(':user_name', $user_name);
		$stmt->bindParam(':install_confirm_date', $install_confirm_date);
		$stmt->bindParam(':install_confirm_num', $install_confirm_num);
		$stmt->bindParam(':Last_access_date', $Last_access_date);
		$stmt->bindParam(':Last_reception_date', $Last_reception_date);
		$stmt->bindParam(':rtu_company', $rtu_company);
		$stmt->bindParam(':rtu_company_tel', $rtu_company_tel);
		$stmt->bindParam(':communication_type', $communication_type);
		$stmt->bindParam(':lora_id', $lora_id);
		$stmt->bindParam(':multi', $multi);
		$stmt->bindParam(':address', $address);			
		$stmt->bindParam(':latitude', $latitude);
		$stmt->bindParam(':longitude', $longitude);
		$stmt->bindParam(':install_type', $install_type);
		$stmt->bindParam(':module_class', $module_class);
		$stmt->bindParam(':module_capacity', $module_capacity);
		$stmt->bindParam(':total_capacity', $total_capacity);
		$stmt->bindParam(':module_manufacturer', $module_manufacturer);
		$stmt->bindParam(':module_model', $module_model);
		$stmt->bindParam(':module_azimuth', $module_azimuth);
		$stmt->bindParam(':module_angle', $module_angle);
		$stmt->bindParam(':module_series', $module_series);
		$stmt->bindParam(':module_parallel', $module_parallel);
		$stmt->bindParam(':inverter_manufacturer', $inverter_manufacturer);
		$stmt->bindParam(':inverter_model', $inverter_model);
		$stmt->bindParam(':inverter_capacity', $inverter_capacity);
		$stmt->bindParam(':phase_type', $phase_type);
		$stmt->bindParam(':tracker_system', $tracker_system);
		$stmt->bindParam(':company_name', $company_name);
		$stmt->bindParam(':company_contact', $company_contact);
		$stmt->bindParam(':construction_date', $construction_date);
		$stmt->bindParam(':monitor_date', $monitor_date);
		$stmt->bindParam(':as_request_enddate', $as_request_enddate);
		$stmt->bindParam(':grid_connection', $grid_connection);
		$stmt->bindParam(':building_use', $building_use);
		$stmt->bindParam(':special_note', $special_note);

		// 쿼리 실행
		$stmt->execute();


		//echo json_encode(['status' => 'success', 'message' => '설비가 성공적으로 수정되었습니다.'], JSON_UNESCAPED_UNICODE);
		echo "설비가 성공적으로 수정되었습니다.";
        echo "<script>setTimeout(function() { window.location.href = 'facility_form.php?cid=$cid'; }, 1000);</script>";		
	} catch (PDOException $e) {
		// 오류 메시지를 화면에 출력
		echo "Error: " . $e->getMessage();
	}

 


} else { 
	
	if ($_SERVER["REQUEST_METHOD"] !== "POST") {
		http_response_code(405);
		echo json_encode(['status' => 'error', 'message' => '허용되지 않은 요청 방식입니다.'], JSON_UNESCAPED_UNICODE);
		exit();
	}

}
?>