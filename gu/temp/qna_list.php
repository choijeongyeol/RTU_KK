<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// QNA 목록 가져오기
$sql = "SELECT i.*, r.reply_id, r.content AS reply_content, r.updated_at AS reply_updated_at, 
               u.user_name as user_name, u.user_id as user_id
        FROM RTU_Inquiry i
        LEFT JOIN RTU_Inquiry_Reply r ON i.inquiry_id = r.inquiry_id
        JOIN RTU_user u ON u.user_idx = i.user_id
        ORDER BY i.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$qna_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>QNA 목록</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #333;
        }
        .actions a:hover {
            color: #4CAF50;
        }
        .add-qna {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .add-qna:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>QNA 목록</h2>
    <a href="qna_add.php" class="add-qna">+ QNA 등록</a>
    <table>
        <thead>
            <tr>
                <th>문의자</th>
                <th>문의 제목</th>
                <th>작성일</th>
                <th>상태</th>
                <th>답변</th>
                <th>첨부파일</th>
                <th>답변일</th>
                <th>액션</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($qna_list as $qna): ?>
                <tr>
                    <td><?= htmlspecialchars($qna['user_name']) ?> (<?= htmlspecialchars($qna['user_id']) ?>)</td>
                    <td><a href="qna_edit.php?id=<?= $qna['inquiry_id'] ?>"><?= htmlspecialchars($qna['title']) ?></a></td>
                    <td><?= htmlspecialchars($qna['created_at']) ?></td>
                    <td><?= $qna['status'] == '0' ? '대기중' : '답변완료' ?></td>
                    <td><?= htmlspecialchars($qna['reply_content'] ?? '답변 없음') ?></td>
                    <td>
                        <?php if (!empty($qna['original_file_name'])): ?>
                            <a href="/uploads/<?= htmlspecialchars($qna['saved_file_name']) ?>" download>
                                <?= htmlspecialchars($qna['original_file_name']) ?>
                            </a>
                        <?php else: ?>
                            없음
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($qna['reply_updated_at']) ? htmlspecialchars($qna['reply_updated_at']) : '없음' ?></td>
                    <td class="actions">
                        <a href="qna_edit.php?id=<?= $qna['inquiry_id'] ?>">수정</a>
                        <a href="qna_delete.php?id=<?= $qna['inquiry_id'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
