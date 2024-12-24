<?php
// receive_data.php

// ThingPlug에서 보낸 POST 데이터를 받음
$input = file_get_contents('php://input');

// ThingPlug에서 받은 데이터 로그로 남기기
file_put_contents('thingplug_received_data.log', $input, FILE_APPEND);

// 받은 데이터를 JSON으로 변환
$data = json_decode($input, true);

// 데이터가 유효한지 확인
if ($data && isset($data['mgc'])) {
    echo json_encode(['status' => 'success', 'message' => 'Data received successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data received.']);
}
?>
