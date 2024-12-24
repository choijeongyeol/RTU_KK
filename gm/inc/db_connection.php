<?php
// db_connection.php

$servername = "localhost";
$username = "myhanis";
$password = "Hanis123!";
$dbname = "myhanis_multi";

// �����ͺ��̽� ���� ����
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// ONLY_FULL_GROUP_BY ��带 ��Ȱ��ȭ
    $conn->exec("SET SESSION sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

} catch (PDOException $e) {
    die("���� ����: " . $e->getMessage());
}

// �����ͺ��̽� ���� �Լ�
function getDbConnection(): ?PDO {
    $host = 'localhost'; // �����ͺ��̽� ȣ��Ʈ
    $dbname = 'myhanis_multi'; // �����ͺ��̽� �̸�
    $username = 'myhanis'; // �����ͺ��̽� ����� �̸�
    $password = 'Hanis123!'; // �����ͺ��̽� ��й�ȣ

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// ONLY_FULL_GROUP_BY ��带 ��Ȱ��ȭ
		$conn->exec("SET SESSION sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
		
        return $conn;
    } catch (PDOException $e) {
        echo '���� ����: ' . $e->getMessage();
        return null;
    }
}
?>
