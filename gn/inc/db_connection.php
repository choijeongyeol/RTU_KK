<?php
// db_connection.php

$servername = "localhost";
$username = "myhanis";
$password = "Hanis123!";
$dbname = "myhanis";

// �����ͺ��̽� ���� ����
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("���� ����: " . $e->getMessage());
}

// �����ͺ��̽� ���� �Լ�
function getDbConnection(): ?PDO {
    $host = 'localhost'; // �����ͺ��̽� ȣ��Ʈ
    $dbname = 'myhanis'; // �����ͺ��̽� �̸�
    $username = 'myhanis'; // �����ͺ��̽� ����� �̸�
    $password = 'Hanis123!'; // �����ͺ��̽� ��й�ȣ

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo '���� ����: ' . $e->getMessage();
        return null;
    }
}
?>
