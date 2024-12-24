<?php
// create_partner.php

require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => '필수 파라미터가 누락되었습니다.']);
    exit;
}

$name = $data['name'];
$admin_id = $data['admin_id'];
$address = $data['address'] ?? null;
$contact = $data['contact'] ?? null;

$response = create_partner($conn, $name, $admin_id, $address, $contact);
echo json_encode($response);

$conn->close();
?>
