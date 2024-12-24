<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/ThingPlugAPI.php');

// API 응답을 JSON 형식으로 반환하는 함수
function sendResponse($status, $message, $data = null) {
    header("Content-Type: application/json; charset=UTF-8");

    $jsonResponse = json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);

    if ($jsonResponse === false) {
        error_log("JSON encode error: " . json_last_error_msg());
        echo json_encode([
            'status' => 500,
            'message' => 'Internal server error: Failed to encode JSON.'
        ]);
    } else {
        echo $jsonResponse;
    }
    exit;
}

// ThingPlug API 연결 설정
//$appEUI = "0060261000000799";
//$uKey = "bmp3WWFyUzhBNmFLcEdicS9FUnJMMkNTN1lDYlZLdTBhaExEdWdoanUrdlZ4Sm9ZczduV09qTi9rUTZuaHBOcg==";


$thingPlug = new ThingPlugAPI($appEUI, $uKey);

 
// 요청을 처리하는 부분
if (!isset($_GET['function'])) {
   // sendResponse(400, "No function specified.");
}

 // function GET 또는 POST로 받기
if (isset($_POST['function'])) { 
	$function = $_POST['function']; 
}else{
	if (isset($_GET['function'])) { 
		$function = $_GET['function']; 	
	}else{
		exit();
	}
}

// subscription 회수 파라미터 받기
if (isset($_GET['subscription_1'])) { $subscription_1 = $_GET['subscription_1']; }
 
switch ($function) {
    // 1. 노드 정보 조회
    case 'node_info':
        if (!isset($_GET['lTid'])) {
            sendResponse(400, "No LTID provided.");
        }
        $lTid = $_GET['lTid'];
        error_log("Fetching node info for LTID: $lTid");

        $nodeInfo = $thingPlug->getNodeInfo($lTid);
        if ($nodeInfo) {
            sendResponse(200, "Node info retrieved successfully.", $nodeInfo);
        } else {
            sendResponse(500, "Failed to retrieve node info.");
        }
        break;

    // 2. 리모트 CSE 정보 조회
    case 'remote_cse_info':
        if (!isset($_GET['lTid'])) {
            sendResponse(400, "No LTID provided.");
        }
        $lTid = $_GET['lTid'];
        error_log("Fetching remote CSE info for LTID: $lTid");

        $remoteCSEInfo = $thingPlug->getRemoteCSEInfo($lTid);
        if ($remoteCSEInfo) {
            sendResponse(200, "Remote CSE info retrieved successfully.", $remoteCSEInfo);
        } else {
            sendResponse(500, "Failed to retrieve remote CSE info.");
        }
        break;

    // 3. 최신 데이터 조회
    case 'latest_data':
        if (!isset($_GET['lTid'])) {
            sendResponse(400, "No LTID provided.");
        }
        $lTid = $_GET['lTid'];
        error_log("Fetching latest data for LTID: $lTid");

        $latestData = $thingPlug->getLatestData($lTid);
        if ($latestData) {
            sendResponse(200, "Latest data retrieved successfully.", $latestData);
        } else {
            sendResponse(500, "Failed to retrieve latest data.");
        }
        break;

    // 디바이스 리셋 (PUT 요청)
    case 'reset_device':
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            sendResponse(405, "Invalid request method. Use PUT.");
        }

        $inputData = file_get_contents("php://input");
        error_log("Received input for reset device: " . $inputData);

        parse_str($inputData, $putVars);

        if (!isset($putVars['lTid'])) {
            sendResponse(400, "No LTID provided.");
        }

        $lTid = $putVars['lTid'];
        error_log("Resetting device for LTID: $lTid");

        $resetResponse = $thingPlug->resetDevice($lTid);
        if ($resetResponse) {
            sendResponse(200, "Device reset successfully.", $resetResponse);
        } else {
            sendResponse(500, "Failed to reset device.");
        }
        break;

    // 구독 생성 (POST 요청)
    case 'create_subscription':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(405, "Invalid request method. Use POST.");
        }

        error_log("Processing subscription creation");

        //if (!isset($_POST['lTid']) || !isset($_POST['notification_Url'])) {
         //   sendResponse(400, "No LTID or notification URL provided.");
        //}

        $lTid = $_POST['lTid'];
        //$notification_Url = $_POST['notification_Url'];
        error_log("Creating subscription for LTID: $lTid with notification URL: $notification_Url");
 
		//echo $lTid;
		//echo $notification_Url;
		//exit();	
        $subscriptionResponse = $thingPlug->createSubscription($lTid);

        if ($subscriptionResponse) {
            sendResponse(201, "Subscription created successfully.", $subscriptionResponse);
        } else {
            sendResponse(500, "Failed to create subscription.");
        }
        break;



    // 업데이트 서브스크립션 (PUT 요청)
    case 'update_subscription':
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            sendResponse(405, "Invalid request method. Use PUT.");
        }

        $inputData = file_get_contents("php://input");
        error_log("Received input for update subscription: " . $inputData);

        parse_str($inputData, $putVars);

        if (!isset($putVars['lTid'])) {
            sendResponse(400, "No LTID provided.");
        }

        $lTid = $putVars['lTid'];
        error_log("Update subscription for LTID: $lTid");

		// subscription_1 항목 확인
		if (!isset($putVars['subscription_1'])) {
			sendResponse(400, "No subscription_1 provided.");
		}
		$subscription_1 = $putVars['subscription_1'];
		error_log("Subscription 1 value: $subscription_1");
		
        $resetResponse = $thingPlug->update_subscription($lTid, $subscription_1);
        if ($resetResponse) {
            sendResponse(200, "Subscription update successfully", $resetResponse);  
        } else {
            sendResponse(500, "Failed to update subscription.");
        }
        break;

	// 구독 삭제 (DELETE 요청)
	case 'delete_subscription':
		if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
 			sendResponse(405, "Invalid request method. Use DELETE.");
		}

		$inputData = file_get_contents("php://input");
		error_log("Received input for delete subscription: " . $inputData);
 
		parse_str($inputData, $deleteVars);   // $inputData = lTid=00001132d02544fffef3bad6&subscription_1=kk_3
		                                      // $deleteVars['lTid'] = 00001132d02544fffef3bad6  $deleteVars['subscription_1'] =kk_3

		// LTID 확인
		if (!isset($deleteVars['lTid'])) {
			sendResponse(400, "No LTID provided.");
		}

		$lTid = $deleteVars['lTid'];
		error_log("Delete subscription for LTID: $lTid");

		// subscription_1 항목 확인
		if (!isset($deleteVars['subscription_1'])) {
			sendResponse(400, "No subscription_1 provided.");
		}

		$subscription_1 = $deleteVars['subscription_1'];
		 error_log("Subscription 1 value for deletion: $subscription_1");

		// ThingPlug API를 사용하여 subscription 삭제
	     error_log("Attempting to delete subscription with LTID: $lTid and subscription_1: $subscription_1");	
		 
		$deleteResponse = $thingPlug->delete_subscription($lTid, $subscription_1);
		
		if ($deleteResponse) {
			sendResponse(200, "Subscription deleted successfully", $deleteResponse);  
		} else {
		 error_log("ThingPlug API delete_subscription failed for LTID: $lTid and subscription_1: $subscription_1");	
			sendResponse(500, "Failed to delete subscription.");
		}
		break;

    // 5. 센서 데이터 저장 (POST 요청)
    case 'save_sensor_data':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendResponse(405, "Invalid request method. Use POST.");
        }

        if (!isset($_POST['lTid']) || !isset($_POST['sensorData'])) {
            sendResponse(400, "No LTID or sensor data provided.");
        }

        $lTid = $_POST['lTid'];
        $sensorData = $_POST['sensorData'];
        error_log("Saving sensor data for LTID: $lTid");

        $saveResponse = $thingPlug->createContentInstance($lTid, $sensorData);
        if ($saveResponse) {
            sendResponse(200, "Sensor data saved successfully.", $saveResponse);
        } else {
            sendResponse(500, "Failed to save sensor data.");
        }
        break;


    // 6.6.3 <subscription> Retrieve  기 설정된 <subscription> 자원을 회수
    case 'getRetrieve_Subscription':
        if (!isset($_GET['lTid'])) {
            sendResponse(400, "No LTID provided.");
        }
        $lTid = $_GET['lTid'];

        $remoteCSEInfo = $thingPlug->getRetrieve_Subscription($lTid,$subscription_1);
        if ($remoteCSEInfo) {
            sendResponse(200, "Remote CSE info retrieved successfully.", $remoteCSEInfo);
        } else {
            sendResponse(500, "Failed to retrieve remote CSE info.");
        }
        break;




    // 잘못된 기능 요청에 대한 처리
    default:
        sendResponse(400, "Invalid function specified.");
}
?>
