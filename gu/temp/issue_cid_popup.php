<?php
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

$cid = $_GET['cid'] ?? '';
$issueTypeId = $_GET['issue_type_id'] ?? ''; // issue_type_id 파라미터 추가
$fourHex = $_GET['four_hex'] ?? ''; // four_hex 파라미터 추가

try {
    // 특정 CID와 issue_type_id, four_hex에 해당하는 장애 이력 데이터를 조회하는 쿼리
    $sql = "
        SELECT 
            ih.id AS issue_id,
            CASE 
                WHEN ih.fault_description IS NOT NULL AND ih.fault_description != '' THEN
                    CASE 
                        WHEN CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')) + 1 > 1 THEN
                            CONCAT(SUBSTRING_INDEX(ih.fault_description, ',', 1), ' 외 ', 
                                   CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')))
                        ELSE 
                            ih.fault_description
                    END
                ELSE 
                    it.issue_name
            END AS 장애명,
            ih.issue_start_date AS 장애발생일시,
            ih.issue_last_date AS 장애최종일시,
            ih.status AS 해결여부,
            ih.fault_description AS 장애상세
        FROM RTU_Issue_History_New ih
        JOIN RTU_issue_type it ON ih.issue_name = it.issue_type_id
        WHERE ih.facility_id = :cid
          AND it.issue_type_id = :issue_type_id
          AND ih.four_hex = :four_hex
          AND ih.viewline = 1
        ORDER BY 
            CASE WHEN ih.status = '0' THEN 0 
                 WHEN ih.status = '1' THEN 1 
                 WHEN ih.status = '2' THEN 2 
                 WHEN ih.status = '3' THEN 3 
                 WHEN ih.status = '4' THEN 4 
            END, 
            ih.issue_start_date ASC

    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cid', $cid);
    $stmt->bindParam(':issue_type_id', $issueTypeId);
    $stmt->bindParam(':four_hex', $fourHex);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>CID <?= htmlspecialchars($cid) ?> 장애 이력</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>CID <?= htmlspecialchars($cid) ?> - 장애 이력 목록</h2>

<table>
    <tr>
        <th>장애명</th> 
        <th>장애상세</th> <!-- 장애상세 칼럼 추가 -->
        <th>장애발생일시</th>
        <th>장애최종일시</th>
        <th>해결여부</th>
    </tr>

    <?php if (!empty($results)): ?>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['장애명']) ?></td> 
                <td><?= htmlspecialchars($row['장애상세']) ?></td> <!-- 장애상세 출력 -->
                <td><?= htmlspecialchars($row['장애발생일시']) ?></td>
                <td><?= htmlspecialchars($row['장애최종일시']) ?></td>
                <td>
                    <?php 
                    switch ($row['해결여부']) {
                        case 1:
                            echo "미신청";
                            break;
                        case 2:
                            echo "접수";
                            break;
                        case 3:
                            echo "처리중";
                            break;
                        case 4:
                            echo "처리완료";
                            break;
                        default:
                            echo "상태 알 수 없음";
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">해당 CID와 장애명에 대한 장애 이력이 없습니다.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
