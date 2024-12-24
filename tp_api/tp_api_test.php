<?php

// ThingPlugAPI 클래스 포함 (URL 경로에 따라 수정 필요)
require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/ThingPlugAPI.php');

try {
    // ThingPlug API에 필요한 변수 설정
    //$appEUI = "0060261000000799";
    //$uKey = "bmp3WWFyUzhBNmFLcEdicS9FUnJMMkNTN1lDYlZLdTBhaExEdWdoanUrdlZ4Sm9ZczduV09qTi9rUTZuaHBOcg==";
    //$LTID = "00000799d02544fffef3ca7e"; //"00000799d02544fffef3ac16";  // 실제 LTID 값으로 변경 필요
    $subscription_1 = $subscription_key; //"etrons_3";
    // ThingPlugAPI 객체 생성
    $thingPlug = new ThingPlugAPI($appEUI, $uKey);

    // 1. 노드 정보 가져오기
    echo "<h2>노드 정보</h2>";
    $nodeInfo = $thingPlug->getNodeInfo($LTID);
    if ($nodeInfo) {
        echo "<pre>" . print_r($nodeInfo, true) . "</pre>";
    } else {
        echo "노드 정보를 가져오지 못했습니다.<br>";
    }

    // 2. 리모트 CSE 정보 가져오기
    echo "<h2>리모트 CSE 정보</h2>";
    $remoteCSEInfo = $thingPlug->getRemoteCSEInfo($LTID);
    if ($remoteCSEInfo) {
        echo "<pre>" . print_r($remoteCSEInfo, true) . "</pre>";
    } else {
        echo "리모트 CSE 정보를 가져오지 못했습니다.<br>";
    }

    // 3. 주기 데이터의 최신 정보 가져오기
    echo "<h2>최신 데이터</h2>";
    $latestData = $thingPlug->getLatestData($LTID);
    if ($latestData) {
        echo "<pre>" . print_r($latestData, true) . "</pre>";
    } else {
        echo "최신 데이터를 가져오지 못했습니다.<br>";
    }

    // 4. 디바이스 리셋 실행
    echo "<h2>디바이스 리셋</h2>";
    $resetResponse = $thingPlug->resetDevice($LTID);
    if ($resetResponse) {
        echo "<pre>" . print_r($resetResponse, true) . "</pre>";
    } else {
        echo "디바이스 리셋을 실행하지 못했습니다.<br>";
    }

    // subscription Retrieve
    echo "<h2>subscription Retrieve</h2>";
    $RetrieveInfo = $thingPlug->getRetrieve_Subscription($LTID,$subscription_1);
    if ($RetrieveInfo) {
        echo "<pre>" . print_r($RetrieveInfo, true) . "</pre>";
    } else {
        echo "Retrieve 정보를 가져오지 못했습니다.<br>";
    }
	
	
    // 5. 센서 데이터 저장하기
    echo "<h2>센서 데이터 저장</h2>";
    $sensorData = "0123913923912391239129329329319293129319293192391293192391293923123123";  // 예시 데이터
    $contentResponse = $thingPlug->createContentInstance($LTID, $sensorData);
    if ($contentResponse) {
        echo "<pre>" . print_r($contentResponse, true) . "</pre>";
    } else {
        echo "센서 데이터를 저장하지 못했습니다.<br>";
    }



} catch (Exception $e) {
    // 예외 발생 시 에러 메시지 출력
    echo "<h2>오류 발생</h2>";
    echo "Error: " . $e->getMessage();
}

?>
