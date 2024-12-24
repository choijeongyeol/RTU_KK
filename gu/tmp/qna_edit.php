<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$inquiry_id = $_GET['id'];

// QNA 기본 정보 가져오기
$sql = "SELECT * FROM RTU_Inquiry WHERE inquiry_id = :inquiry_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':inquiry_id', $inquiry_id, PDO::PARAM_INT);
$stmt->execute();
$qna = $stmt->fetch(PDO::FETCH_ASSOC);

// 답변 정보 가져오기
$sql_reply = "SELECT * FROM RTU_Inquiry_Reply WHERE inquiry_id = :inquiry_id";
$stmt_reply = $conn->prepare($sql_reply);
$stmt_reply->bindParam(':inquiry_id', $inquiry_id, PDO::PARAM_INT);
$stmt_reply->execute();
$reply = $stmt_reply->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $reply_content = $_POST['reply_content'];
    $admin_id = 1; // 관리자 ID (테스트용, 실제 환경에서는 로그인 세션 사용)

    // QNA 수정
    $sql = "UPDATE RTU_Inquiry SET title = :title, content = :content, status = :status, updated_at = NOW() WHERE inquiry_id = :inquiry_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':inquiry_id', $inquiry_id, PDO::PARAM_INT);
    $stmt->execute();

    // 답변 수정 또는 삽입
    if ($reply) {
        $sql_reply_update = "UPDATE RTU_Inquiry_Reply SET content = :reply_content, updated_at = NOW() WHERE inquiry_id = :inquiry_id";
        $stmt_reply_update = $conn->prepare($sql_reply_update);
        $stmt_reply_update->bindParam(':reply_content', $reply_content);
        $stmt_reply_update->bindParam(':inquiry_id', $inquiry_id, PDO::PARAM_INT);
        $stmt_reply_update->execute();
    } else {
        $sql_reply_insert = "INSERT INTO RTU_Inquiry_Reply (inquiry_id, admin_id, content, created_at, updated_at) VALUES (:inquiry_id, :admin_id, :reply_content, NOW(), NOW())";
        $stmt_reply_insert = $conn->prepare($sql_reply_insert);
        $stmt_reply_insert->bindParam(':inquiry_id', $inquiry_id, PDO::PARAM_INT);
        $stmt_reply_insert->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt_reply_insert->bindParam(':reply_content', $reply_content);
        $stmt_reply_insert->execute();
    }

    header("Location: qna_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>QNA 수정</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #4CAF50;
        }
        form {
            margin-top: 20px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], textarea, select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .image-preview {
            max-width: 100%;
            max-height: 300px;
            margin-top: 10px;
            display: block;
        }
    </style>
    <script>
        function confirmStatusChange() {
            var statusSelect = document.querySelector('select[name="status"]');
            var currentStatus = statusSelect.value;

            // 상태가 대기중인 경우 확인 메시지 표시
            if (currentStatus === '0') {
                var confirmChange = confirm("답변을 완료로 변경하시겠습니까?");
                if (confirmChange) {
                    statusSelect.value = '1'; // 답변완료로 변경
                }
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>QNA 수정</h2>
    <form method="post" onsubmit="return confirmStatusChange();">
 
        <label>제목:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($qna['title']) ?>" required>
        
        <label>내용:</label>
        <textarea name="content" required><?= htmlspecialchars($qna['content']) ?></textarea>
        
        <label>상태:</label>
        <select name="status">
            <option value="0" <?= $qna['status'] == '0' ? 'selected' : '' ?>>대기중</option>
            <option value="1" <?= $qna['status'] == '1' ? 'selected' : '' ?>>답변완료</option>
        </select>

        <label>첨부파일:</label>
        <?php if (!empty($qna['original_file_name'])): ?>
            <p>기존 파일: 
                <a href="/uploads/<?= htmlspecialchars($qna['saved_file_name']) ?>" download>
                    <?= htmlspecialchars($qna['original_file_name']) ?>
                </a>
            </p>
            <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $qna['saved_file_name'])): ?>
                <img src="/uploads/<?= htmlspecialchars($qna['saved_file_name']) ?>" alt="첨부 이미지" class="image-preview">
            <?php endif; ?>
        <?php endif; ?>
        <!-- <input type="file" name="attachment"> -->

        <label>답변:</label>
        <textarea name="reply_content"><?= htmlspecialchars($reply['content'] ?? '') ?></textarea>

        <button type="submit">저장</button>
    </form>
</body>
</html>
