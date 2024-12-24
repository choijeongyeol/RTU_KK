<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Composer autoload 파일을 include
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// MySQL 연결 설정
include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 업로드 디렉토리 설정
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/gm/test8_excel/uploads/';

    // 파일명에 현재 날짜와 시간을 추가
    $originalFileName = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
    $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $dateTimeSuffix = date('YmdHis');
    $newFileName = $originalFileName . '_' . $dateTimeSuffix . '.' . $fileExtension;
    $uploadFile = $uploadDir . $newFileName;

    // 디렉토리가 존재하는지 확인하고, 존재하지 않으면 생성
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo "파일이 업로드되었습니다.\n";

        $spreadsheet = IOFactory::load($uploadFile);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // 첫 번째 행은 헤더로 간주하고 건너뜁니다.
        $header = true;
        foreach ($rows as $row) {
            if ($header) {
                $header = false;
                continue;
            }

            // 데이터베이스에 데이터 삽입
            $column1 = $row[0];
            $column2 = $row[1];
            $column3 = $row[2];

            $sql = "INSERT INTO data (column1, column2, column3) VALUES (:column1, :column2, :column3)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':column1', $column1);
            $stmt->bindParam(':column2', $column2);
            $stmt->bindParam(':column3', $column3);

            if ($stmt->execute()) {
                echo "데이터가 성공적으로 삽입되었습니다.<br>";
            } else {
                echo "오류: 데이터 삽입 실패.<br>";
            }
        }
    } else {
        echo "파일 업로드 실패.\n";
    }
}

$conn = null;
?>
