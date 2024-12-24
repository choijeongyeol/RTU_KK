<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// 장애 유형 목록 조회
try {
    $sql = "SELECT issue_type_id, issue_name FROM RTU_issue_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $issue_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>장애 유형 관리 - 관리자 모드</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-btn {
            padding: 5px 10px;
            margin: 0 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>장애 유형 관리 - 관리자 모드</h2>

<!-- 장애유형 추가 폼 -->
<form action="issue_type_add.php" method="post">
    <label for="new_issue_name">새로운 장애명 추가:</label>
    <input type="text" id="new_issue_name" name="new_issue_name" required>
    <button type="submit">장애유형 추가</button>
</form>

<!-- 장애유형 목록 테이블 -->
<table>
    <tr>
        <th>장애유형 ID</th>
        <th>장애명</th>
        <th>수정</th>
        <th>삭제</th>
    </tr>
    <?php foreach ($issue_types as $type): ?>
        <tr>
            <td><?= htmlspecialchars($type['issue_type_id']) ?></td>
            <td><?= htmlspecialchars($type['issue_name']) ?></td>
            <td>
                <form action="issue_type_edit.php" method="post" style="display:inline;">
                    <input type="hidden" name="issue_type_id" value="<?= $type['issue_type_id'] ?>">
                    <input type="text" name="new_issue_name" value="<?= htmlspecialchars($type['issue_name']) ?>" required>
                    <button type="submit" class="action-btn">수정</button>
                </form>
            </td>
            <td>
                <form action="issue_type_delete.php" method="post" style="display:inline;" onsubmit="return confirm('이 항목을 삭제하시겠습니까?');">
                    <input type="hidden" name="issue_type_id" value="<?= $type['issue_type_id'] ?>">
                    <button type="submit" class="action-btn">삭제</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
