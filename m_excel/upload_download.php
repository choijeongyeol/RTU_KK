<?php
$file = $_GET['file'];
$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/m_excel/data_excel/';
$filePath = $uploadDir . $file;

if (file_exists($filePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    echo "파일이 존재하지 않습니다.";
}
?>
