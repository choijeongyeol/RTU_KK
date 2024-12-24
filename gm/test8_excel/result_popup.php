<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


// Composer autoload 파일을 include
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// 파일명 가져오기
$file = isset($_GET['file']) ? $_GET['file'] : '';
$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/gm/test8_excel/uploads/';
$filePath = $uploadDir . $file;

 

if (file_exists($filePath)) {
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
    } catch (Exception $e) {
        echo "Error loading file: " . $e->getMessage();
        exit;
    }

    echo "<html><head><title>업로드 결과</title></head><body>";
    echo "<h1>업로드 결과</h1>";
    echo "<table border='1'>";
    foreach ($rows as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><a href='download.php?file=" . urlencode($file) . "'>결과 파일 다운로드</a>";
    echo "</body></html>";
} else {
    echo "파일이 존재하지 않습니다.";
}
?>
