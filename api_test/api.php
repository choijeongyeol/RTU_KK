<?php
// 데이터베이스 연결 (여기서는 파일 기반 데이터 저장을 사용합니다)
$filename = 'data.json';

// 데이터 파일이 존재하지 않으면 생성
if (!file_exists($filename)) {
    file_put_contents($filename, json_encode([]));
}

// HTTP 메서드에 따라 동작 결정
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// 데이터 가져오기
function getData() {
    global $filename;
    return json_decode(file_get_contents($filename), true);
}

// 데이터 저장하기
function saveData($data) {
    global $filename;
    file_put_contents($filename, json_encode($data));
}

// GET 요청 처리
if ($method === 'GET') {
    $data = getData();
    if ($id === null) {
        echo json_encode($data);
    } else {
        if (isset($data[$id])) {
            echo json_encode($data[$id]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Resource not found"]);
        }
    }
}

// POST 요청 처리
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $data = getData();
    $id = count($data);
    $data[$id] = $input;
    saveData($data);
    http_response_code(201);
    echo json_encode(["id" => $id]);
}

// PUT 요청 처리
if ($method === 'PUT') {
    if ($id === null) {
        http_response_code(400);
        echo json_encode(["message" => "ID is required"]);
    } else {
        $input = json_decode(file_get_contents('php://input'), true);
        $data = getData();
        if (isset($data[$id])) {
            $data[$id] = $input;
            saveData($data);
            echo json_encode(["message" => "Resource updated"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Resource not found"]);
        }
    }
}

// DELETE 요청 처리
if ($method === 'DELETE') {
    if ($id === null) {
        http_response_code(400);
        echo json_encode(["message" => "ID is required"]);
    } else {
        $data = getData();
        if (isset($data[$id])) {
            unset($data[$id]);
            saveData(array_values($data));
            echo json_encode(["message" => "Resource deleted"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Resource not found"]);
        }
    }
}
?>
