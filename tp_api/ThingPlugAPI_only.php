<?php
session_start();

//$default_Url = "http://43.200.77.82";
//$subscription_key = "etrons_3";
//$notification_ip = "http://43.200.77.82:80";
//$notification_Url = $default_Url . "/tp_api/receive_notification.php";
 
 $partner_id = $_SESSION['partner_id']; // 업체 ID를 통해, RTU_Configuration 정보를 추출한다.
 
// get_RTU_Config 업체 정보 가져오기
 function get_RTU_Config($partner_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * from RTU_Configuration WHERE partner_id = :partner_id");
	
    // 파라미터 바인딩
    $stmt->bindParam(':partner_id', $partner_id);   
	$stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
   
// 함수 호출
$config = get_RTU_Config($partner_id);

// 결과 출력
if (!empty($config)) {
    foreach ($config as $row) {
		$appEUI = $row['app_eui'];
		$uKey = $row['u_key'];
		$subscription_key = $row['subscription_key'];
		$notification_ip = $row['notification_ip'];
		$default_Url = $row['default_url'];
		$notification_Url = $default_Url . "/tp_api/receive_notification.php";
    }
} else {
    echo "<p>No configuration found for partner ID: $partner_id</p>";
}
 
 //echo $notification_Url;
 
// URL 형식 유효성 검사
if (!filter_var($notification_Url, FILTER_VALIDATE_URL)) {
    die("Error: Invalid notification URL.");
}



// ThingPlug API와 상호작용하는 주요 기능을 포함한 클래스 파일.
// 모든 API 호출을 처리하는 역할을 합니다. 이를 ThingPlug에서 제공한 **appEUI**와 uKey 값으로 설정합니다.
class ThingPlugAPI {
    private $appEUI;
    private $uKey;
    private $baseUrl;
    private $subscriptionKey; // 추가: 구독 키 변수 선언
    private $notificationIp; // 추가: 구독 알림 URL 변수 선언	
	
    public function __construct($appEUI, $uKey) {
        global $subscription_key, $notification_ip; // set_info.php에서 가져온 변수 사용		
        $this->appEUI = $appEUI;
        $this->uKey = $uKey;
        $this->baseUrl = "http://thingplugpf.sktiot.com:9000/{$appEUI}/v1_0/";
        $this->subscriptionKey = $subscription_key; // 구독 키 설정	
        $this->notificationIp = $notification_ip; // 올바른 구독 알림 URL 설정	
    }

	private function callAPI($method, $endpoint, $data, $isXML = false,$fn_name) {


		$url = $this->baseUrl . $endpoint;
		error_log("API Request URL: $url");	
		
		$ch = curl_init();
        if ($fn_name=="createSubscription") {
			$headers = [
				'Accept: application/xml',
				'Content-Type: application/vnd.onem2m-res+xml;ty=23',
				'locale: ko',
				'X-M2M-RI: 12345',
				'X-M2M-Origin: ' . $this->appEUI,
                'X-M2M-NM: ' . $this->subscriptionKey, // 구독 키를 변수로 처리
				'uKey: ' . $this->uKey
			];	
 		 
        }elseif ($fn_name=="update_subscription") {
			$headers = [
				'Accept: application/xml',
				'Content-Type: application/vnd.onem2m-res+xml',
				'locale: ko',
				'X-M2M-RI: 12345',
				'X-M2M-Origin: ' . $this->appEUI,
				'uKey: ' . $this->uKey
			];	
 		 
        }elseif ($fn_name=="delete_subscription") {
			$headers = [
				'Accept: application/xml',
				'Content-Type: application/vnd.onem2m-res+xml',
				'locale: ko',
				'X-M2M-RI: 12345',
				'X-M2M-Origin: ' . $this->appEUI,
				'uKey: ' . $this->uKey
			];	
 		 
        }else{
			$headers = [
				'Accept: application/xml',
				'Content-Type: application/vnd.onem2m-res+xml',
				'locale: ko',
				'X-M2M-RI: 12345',
				'X-M2M-Origin: ' . $this->appEUI,
				'uKey: ' . $this->uKey
			];			
        }


		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		if ($method === 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			if ($data) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  // POST 데이터를 설정
			}
		} elseif ($method === 'PUT') {  // 괄호 추가
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  // PUT 요청을 설정
			if ($data) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  // PUT 요청의 데이터를 설정
			}
		} elseif ($method === 'DELETE') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  // DELETE 요청을 설정
		}

		$response = curl_exec($ch);

		if ($response === false) {
			error_log('cURL Error: ' . curl_error($ch));
			curl_close($ch);
			return null;
		}
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		error_log("HTTP 상태 코드: " . $httpCode);  // 상태 코드 로그 출력
		error_log("응답 내용: " . $response);  // API 응답 로그

		curl_close($ch);

		if (($httpCode !== 200)&&($httpCode !== 201)) {		
			//error_log("HTTP 요청 실패. 상태 코드: " . $httpCode);  // 200이 아닌 경우 실패
			echo "HTTP 요청 실패. 상태 코드:".$httpCode."<BR>"; // exit();
			return null;
		}

		return simplexml_load_string($response);
	}
 
 
    // 노드 정보 조회
    public function getNodeInfo($LTID) {
        return $this->callAPI('GET', "node-{$LTID}",'','','getNodeInfo');
    }

    // 리모트 CSE 정보 조회
    public function getRemoteCSEInfo($LTID) {
        return $this->callAPI('GET', "remoteCSE-{$LTID}",'','','getRemoteCSEInfo');
    }

    // 최신 데이터 조회
    public function getLatestData($LTID) {
        return $this->callAPI('GET', "remoteCSE-{$LTID}/container-LoRa/latest",'','','getLatestData');
    }

    // 디바이스 리셋 (관리 명령)
    public function resetDevice($LTID) {
		$data = '<m2m:mgc 
				xmlns:m2m="http://www.onem2m.org/xml/protocols" 
				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
				<exe>true</exe>
				<exra>0</exra>
			</m2m:mgc>';

		error_log("Reset Device request for LTID: $LTID");  // LTID 로그
		error_log("Request XML: " . $data);  // XML 요청 본문 로그
		
		// API 호출 및 응답 기록
		$response = $this->callAPI('PUT', "mgmtCmd-{$LTID}_DevReset", $data, true,'resetDevice');
		
		if (!$response) {
			error_log("Failed to reset device for LTID: $LTID");
			return false;
		}

		error_log("Response from ThingPlug API: " . print_r($response, true));  // 응답 로그 기록
		return $response;
    }

    // 센서 데이터 저장 (contentInstance 생성)
    public function createContentInstance($LTID, $sensorData) {
        $data = '<m2m:cin xmlns:m2m="http://www.onem2m.org/xml/protocols">
                    <cnf>application/xml</cnf>
                    <con>' . htmlspecialchars($sensorData) . '</con>
                 </m2m:cin>';
        return $this->callAPI('POST', "remoteCSE-{$LTID}/container-LoRa", $data, true,'createContentInstance');
    }
	

    // 구독 생성
    public function createSubscription($LTID) {

		$data = '<m2m:sub 
					xmlns:m2m="http://www.onem2m.org/xml/protocols" 
					xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
					<enc>
					   <rss>1</rss>
					</enc>
					<nu>HTTP|' . $this->notificationIP . '</nu>
					<nct>2</nct>
				</m2m:sub>';

		error_log("Reset Device request for LTID: $LTID");  // LTID 로그
		error_log("Request XML: " . $data);  // XML 요청 본문 로그
		

		// API 호출 및 응답 기록
		$response = $this->callAPI('POST', "remoteCSE-{$LTID}/container-LoRa", $data, true,'createSubscription');
			
		
		if (!$response) {
			error_log("Failed to subscription Create for LTID: $LTID");
			return false;
		}

		error_log("Response from ThingPlug API: " . print_r($response, true));  // 응답 로그 기록
		return $response;
    }	
	


    // 서브스크립션 업데이트
    public function update_subscription($LTID,$subscription_1) {

		$data = '<m2m:sub 
					xmlns:m2m="http://www.onem2m.org/xml/protocols" 
					xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
					<enc>
					   <rss>1</rss>
					</enc>
					<nu>HTTP|http://43.200.77.82:80/tp_api/receive_notification.php</nu>
					<nct>2</nct>
				</m2m:sub>';

		// API 호출 및 응답 기록
		$response = $this->callAPI('PUT', "remoteCSE-{$LTID}/container-LoRa/subscription-{$subscription_1}", $data, true,'update_subscription');
		
		if (!$response) {
			error_log("Failed to subscription update for LTID: $LTID");
			return false;
		}

		error_log("Response from ThingPlug API: " . print_r($response, true));  // 응답 로그 기록
		return $response;
    }	
	
	// 서브스크립션 삭제
	public function delete_subscription($LTID, $subscription_1) {

		// 삭제 작업을 위해 XML 데이터 불필요
		error_log("Delete subscription request for LTID: $LTID");  // LTID 로그

		// API 호출 및 응답 기록
		$response = $this->callAPI('DELETE', "remoteCSE-{$LTID}/container-LoRa/subscription-{$subscription_1}", null, true, 'delete_subscription');
		
		if (!$response) {
			error_log("Failed to delete subscription for LTID: $LTID");
			return false;
		}

		error_log("Response from ThingPlug API: " . print_r($response, true));  // 응답 로그 기록
		return $response;
	}
		
	
    // <subscription> Retrieve 자원을 회수하는 Resource AP
    public function getRetrieve_Subscription($LTID,$subscription_1) {
		// API 호출 및 응답 기록
		return $this->callAPI('GET', "remoteCSE-{$LTID}/container-LoRa/subscription-{$subscription_1}",'','','getRetrieve_Subscription');
	}
}
 
?>
