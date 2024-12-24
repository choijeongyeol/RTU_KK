<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

$tid = $_POST['tid'] ?? null;
$name = $_POST['name'];
$contact_number = $_POST['contact_number'];
$email = $_POST['email'];

try {
    if ($tid) {
        // 기존 담당자 수정
        $sql = "UPDATE RTU_Technician 
                SET name = :name, contact_number = :contact_number, email = :email, updated_at = NOW() 
                WHERE tid = :tid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':tid', $tid, PDO::PARAM_INT);
    } else {
        // 새로운 담당자 등록
        $sql = "INSERT INTO RTU_Technician (name, contact_number, email, created_at, updated_at) 
                VALUES (:name, :contact_number, :email, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
    }

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    header("Location: technician_list.php");
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
}
?>
