<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// 유저 리스트 가져오기
try {
    $sql_users = "SELECT user_idx, user_name, user_id FROM RTU_user";
    $stmt_users = $conn->prepare($sql_users);
    $stmt_users->execute();
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_POST['user_id']; // 선택된 유저 ID

    // 입력값 검증
    if (empty($user_id)) {
        echo "<script>alert('유저를 선택해 주세요.'); history.back();</script>";
        exit;
    }

    // 파일 업로드 처리
    $saved_file_name = null;
    $original_file_name = null;
    if (!empty($_FILES['attachment']['name'])) {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // 업로드 폴더 생성
        }

        $original_file_name = $_FILES['attachment']['name'];
        $saved_file_name = time() . '_' . basename($original_file_name);
        $target_file = $upload_dir . $saved_file_name;

        if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            echo "<script>alert('파일 업로드 실패!'); history.back();</script>";
            exit;
        }
    }

    try {
        $sql = "INSERT INTO RTU_Inquiry (user_id, title, content, status, saved_file_name, original_file_name, created_at, updated_at) 
                VALUES (:user_id, :title, :content, '0', :saved_file_name, :original_file_name, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':saved_file_name', $saved_file_name);
        $stmt->bindParam(':original_file_name', $original_file_name);
        $stmt->execute();

        header("Location: qna_list.php");
        exit;
    } catch (PDOException $e) {
        echo "데이터베이스 오류: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>QNA 등록</title>
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
    </style>
</head>
<body>
    <h2>QNA 등록</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="user_id">유저 선택:</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- 유저 선택 이름 / idx / id --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['user_idx']) ?>"><?= htmlspecialchars($user['user_name']) ?> (<?= htmlspecialchars($user['user_idx']) ?>) <?= htmlspecialchars($user['user_id']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="title">제목:</label>
        <input type="text" name="title" id="title" required>

        <label for="content">내용:</label>
        <textarea name="content" id="content" required></textarea>

        <label for="attachment">첨부파일:</label>
        <input type="file" name="attachment" id="attachment">

        <button type="submit">등록</button>
    </form>
</body>
</html>
