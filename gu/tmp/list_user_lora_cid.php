<?php
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// SQL 쿼리 실행
$sql = "
    SELECT 
        u.user_name AS 이용자명, 
        u.user_id AS 이용자ID, 
        f.lora_id AS 로라ID, 
        s.cid AS CID
    FROM 
        RTU_user u
    JOIN 
        RTU_facility f ON u.user_id = f.user_id
    JOIN 
        RTU_SolarInputData s ON f.cid = s.cid
    WHERE 
        f.lora_id IS NOT NULL
    GROUP BY 
        u.user_name, u.user_id, f.lora_id, s.cid
    ORDER BY 
        u.user_name, f.lora_id";

// 쿼리 실행 및 결과 가져오기
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>이용자 LoRa ID 및 CID 목록</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .table-container {
            display: block;
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            border: 1px solid #ddd;
        }

        .table-header, .table-row {
            display: flex;
            justify-content: flex-start;
            border-bottom: 1px solid #ddd;
        }

        .table-header {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .table-cell {
            padding: 6px 8px;
            border-right: 1px solid #ddd;
            flex-grow: 1;
            flex-basis: 100px;
            min-width: 80px;
            max-width: 400px;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
        }

        .table-cell:last-child {
            border-right: none;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .table-row:hover {
            background-color: #f9f9f9;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #888;
        }
    </style>
</head>
<body>
    <h1>이용자 LoRa ID 및 CID 목록</h1>

    <div class="table-container">
        <div class="table-header">
            <div class="table-cell">이용자명</div>
            <div class="table-cell">이용자ID</div>
            <div class="table-cell">LoRa ID</div>
            <div class="table-cell">CID</div>
        </div>
        
        <!-- 데이터 행들 출력 -->
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <div class="table-row">
                    <div class="table-cell"><?php echo htmlspecialchars($row['이용자명']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['이용자ID']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['로라ID']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['CID']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data">데이터가 없습니다.</div>
        <?php endif; ?>
    </div>
</body>
</html>
