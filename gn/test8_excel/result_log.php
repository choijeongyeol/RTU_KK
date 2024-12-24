<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Log Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .error-log {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <h1>Error Log</h1>
    <div class="error-log">
        <?php
        // 로그 파일 경로
        $logFilePath = $_SERVER['DOCUMENT_ROOT'] . '/gn/logs/error.log';

        // 파일 존재 여부 확인
        if (file_exists($logFilePath)) {
            // 파일 내용 읽기
            $errorLog = file_get_contents($logFilePath);
            echo htmlspecialchars($errorLog);
        } else {
            echo "Error log file not found.";
        }
        ?>
    </div>
</body>
</html>
