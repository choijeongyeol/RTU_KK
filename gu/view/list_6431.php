<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
 
// RTU_6431 데이터를 가져오기
$rows = $userManager->listRTU6431();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTU_6431 리스트</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            font-size: 0.9rem;
        }

        .table-cell {
            padding: 4px 6px;
            border-right: 1px solid #ddd;
            flex-grow: 1;
            flex-basis: 100px;
            min-width: 80px;
            max-width: 400px;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
            line-height: 1.2;
            font-size: 0.85rem;
        }
		.table-hfcell {
            padding: 4px 6px;
            border-right: 1px solid #ddd;
            flex-grow: 1;
            flex-basis: 100px;
            min-width: 40px;
            max-width: 200px;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
            line-height: 1.2;
            font-size: 0.85rem;
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
    </style>
</head>
<body>
    <h1>RTU_6431 테이블 리스트</h1>
    
    <div class="table-container">
        <div class="table-header">
            <div class="table-hfcell">ID</div>
            <div class="table-hfcell">ty</div>
            <div class="table-cell">ri</div>
            <div class="table-cell">rn</div>
            <div class="table-cell">pi</div>
            <div class="table-cell">ct</div>
            <div class="table-cell">lt</div>
            <div class="table-cell">gwl</div>
            <!-- <div class="table-cell">geui</div> -->
            <div class="table-cell">devl</div>
            <div class="table-hfcell">fp</div>
            <div class="table-hfcell">trid</div>
            <div class="table-hfcell">plidx</div>
            <div class="table-hfcell">ctype</div>
            <div class="table-hfcell">fixType</div>
            <div class="table-hfcell">result</div>
            <div class="table-hfcell">accuracy</div>
            <div class="table-cell">sr</div>
            <div class="table-cell">et</div>
            <div class="table-hfcell">st</div>
            <div class="table-cell">cr</div>
            <div class="table-cell">cnf</div>
            <div class="table-hfcell">cs</div>
            <div class="table-cell">con</div>
            <div class="table-cell">containerCurrentByteSize</div>
            <div class="table-cell">ltid</div>
        </div>
        
        <!-- Data Rows -->
        <?php if (!empty($rows) && is_array($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <div class="table-row">
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['id']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['ty']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['ri']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['rn']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['pi']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['ct']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['lt']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['gwl']); ?></div>
                    <!-- <div class="table-cell"><?php echo htmlspecialchars($row['geui']); ?></div> -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['devl']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['fp']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['trid']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['plidx']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['ctype']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['fixType']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['result']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['accuracy']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['sr']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['et']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['st']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['cr']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['cnf']); ?></div>
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['cs']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['con']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['containerCurrentByteSize']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($row['ltid']); ?></div>
				</div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="table-row">
                <div class="table-cell" colspan="26">데이터가 없습니다.</div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
