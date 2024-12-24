<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
 
// Composer autoload 파일을 include
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// 파일명 가져오기
$file = isset($_GET['file']) ? basename($_GET['file']) : ''; // basename() 함수로 파일명 검증
$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/excel/data_excel/';
$filePath = $uploadDir . $file;

if (file_exists($filePath)) {
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
    } catch (Exception $e) {
        echo "Error loading file: " . htmlspecialchars($e->getMessage());
        exit;
    }

    // HTML 출력
    ?>
    <html>
    <head>
        <title>업로드 결과</title>
    </head>
    <body>
        <h1>업로드 결과</h1>
        <table border='1'>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <?php foreach ($row as $cell): ?>
                        <td><?php echo htmlspecialchars($cell); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <a href='/excel/upload_download.php?file=<?php echo urlencode($file); ?>'>결과 파일 다운로드</a>
        <script>
            // 최상위 창 리로드 스크립트는 HTML 코드의 맨 아래에 위치
			parent.location.reload();
        </script>
    </body>
    </html>
    <?php
} else {
    echo "파일이 존재하지 않습니다.";
}
?>
