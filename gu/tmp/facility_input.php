<?
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

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

    // 필수값 체크 (NULL 또는 빈 값이 있는지 확인)
    if (empty($cid) || empty($business_year) || empty($business_type) || empty($user_name) || empty($rtu_company) || empty($communication_type) || empty($install_type) || empty($lora_id)) {
        echo json_encode(['status' => 'error', 'message' => '필수 입력값이 누락되었습니다.']);
        exit();
    }
    
    // LoRa ID 유효성 확인 start //  
    try {
        // LoRa ID가 RTU_lora 테이블에 있는지 조회
        $lora_sql = "SELECT rtu_code, app_eui, ukey FROM RTU_lora WHERE lora_id = :lora_id";
        $lora_stmt = $conn->prepare($lora_sql);
        $lora_stmt->bindParam(':lora_id', $lora_id);
        $lora_stmt->execute();
        $lora_result = $lora_stmt->fetch(PDO::FETCH_ASSOC);
        
        // LoRa ID가 없으면 오류 반환
        if (!$lora_result) {
            echo json_encode(['status' => 'error', 'message' => '유효하지 않은 LoRa ID입니다.'], JSON_UNESCAPED_UNICODE);
            exit();
        }
        
        // appEUI와 uKey를 변수에 저장
        $rtu_code = $lora_result['rtu_code'];
        $appEUI = $lora_result['app_eui'];
        $uKey = $lora_result['ukey'];
        
        // LTID 만들기
        $lastEightChars = substr($appEUI, -8);
        $LTID = $lastEightChars . $lora_id;

        // 구독 신청 전에 RTU_facility에서 user_id와 lora_id가 이미 존재하는지 확인
        $check_sql = "SELECT COUNT(*) AS count FROM RTU_facility WHERE user_id = :user_id AND lora_id = :lora_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->bindParam(':lora_id', $lora_id);
        $check_stmt->execute();
        $check_result = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        // 이미 존재하면 구독신청 절차를 건너뜀
        if ($check_result['count'] == 0) {
            // 구독신청 부분 시작
            require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/ThingPlugAPI_only.php');

            try {
                // ThingPlugAPI 인스턴스 생성
                $thingPlug = new ThingPlugAPI($appEUI, $uKey);

                // 구독 생성 요청 (LTID는 $lastEightChars와 $lora_id의 조합)
                $subscriptionResponse = $thingPlug->createSubscription($LTID);

                // 구독 생성 성공 시 처리
                if ($subscriptionResponse) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => "Subscription created successfully for LTID: $LTID"
                    ], JSON_UNESCAPED_UNICODE);
                    error_log("Subscription created successfully for LTID: $LTID");

                    // 구독 성공 시, 즉시 업데이트 서브스크립션 요청 실행
                    $subscription_1 = $rtu_code; //"some_updated_value";  // 실제 업데이트에 필요한 값으로 변경
                    $updateResponse = $thingPlug->update_subscription($LTID, $subscription_1);
                    
                    if ($updateResponse) {
                        echo json_encode(['status' => 'success', 'message' => 'Subscription updated successfully']);
                        error_log("Subscription updated successfully for LTID: $LTID");
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to update subscription']);
                        error_log("Failed to update subscription for LTID: $LTID");
                    }
                } else {
                    // 구독 생성 실패 시 처리
                    error_log("Failed to create subscription for LTID: $LTID");
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to create subscription'
                    ], JSON_UNESCAPED_UNICODE);
                    exit();  // 구독 실패 시 데이터베이스 삽입 중단
                }
				
            } catch (Exception $e) {
                // 예외 처리: 오류 발생 시 메시지 출력
                error_log("Error during subscription creation: " . $e->getMessage());
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Exception occurred: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
                exit();  // 예외 발생 시 데이터베이스 삽입 중단
            }
            // 구독신청 부분 끝
        } else {
            // 이미 구독 신청이 되어있는 경우, 구독 신청 절차를 생략하고 계속 진행
            echo json_encode(['status' => 'info', 'message' => "이미 구독 신청이 되어 신청절차를 생략하고 계속 진행 LTID: $LTID"], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (PDOException $e) {
        // 오류 메시지를 화면에 출력
        echo "Error: " . $e->getMessage();
    }

    // 데이터베이스 삽입 부분
    try {
        $sql = "INSERT INTO RTU_facility (cid, business_year, business_type, user_id, user_name, install_confirm_date, install_confirm_num, Last_access_date, Last_reception_date, rtu_company, rtu_company_tel, communication_type, lora_id, multi, address, latitude, longitude, install_type, module_class, module_capacity, total_capacity, module_manufacturer, module_model, module_azimuth, module_angle, module_series, module_parallel, inverter_manufacturer, inverter_model, inverter_capacity, phase_type, tracker_system, company_name, company_contact, construction_date, monitor_date, as_request_enddate, grid_connection, building_use, special_note)
                VALUES (:cid, :business_year, :business_type, :user_id, :user_name, IFNULL(:install_confirm_date, CURRENT_DATE), :install_confirm_num, :Last_access_date, :Last_reception_date, :rtu_company, :rtu_company_tel, :communication_type, :lora_id, :multi, :address, :latitude, :longitude, :install_type, :module_class, :module_capacity, :total_capacity, :module_manufacturer, :module_model, :module_azimuth, :module_angle, :module_series, :module_parallel, :inverter_manufacturer, :inverter_model, :inverter_capacity, :phase_type, :tracker_system, :company_name, :company_contact, :construction_date, :monitor_date, :as_request_enddate, :grid_connection, :building_use, :special_note)";
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
        $stmt->bindParam(':lora_id', $lora_id);  // LoRa ID 추가
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

        echo json_encode(['status' => 'success', 'message' => '설비가 성공적으로 등록되었습니다.'], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        // 오류 메시지를 화면에 출력
        echo "Error: " . $e->getMessage();
    }

} else {
    echo json_encode(['status' => 'error', 'message' => '잘못된 요청입니다.'], JSON_UNESCAPED_UNICODE);
}
?>