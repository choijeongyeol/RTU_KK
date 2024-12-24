<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$faq_id = $_GET['id'];

// 카테고리 목록을 불러오기
$sql = "SELECT * FROM RTU_FAQ_Category WHERE is_active = 1 ORDER BY display_order ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $display_order = $_POST['display_order'];

    $sql = "UPDATE RTU_FAQ SET category_id = :category_id, question = :question, answer = :answer, display_order = :display_order, updated_at = NOW() WHERE faq_id = :faq_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':answer', $answer);
    $stmt->bindParam(':display_order', $display_order);
    $stmt->bindParam(':faq_id', $faq_id);
    $stmt->execute();
    header("Location: faq_list.php");
    exit;
}

$sql = "SELECT * FROM RTU_FAQ WHERE faq_id = :faq_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':faq_id', $faq_id);
$stmt->execute();
$faq = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FAQ 수정</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h2 { color: #4CAF50; font-size: 24px; }
        form { margin-top: 20px; display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 5px; }
        select, input[type="text"], textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        textarea { resize: vertical; min-height: 150px; }
        button { padding: 10px 20px; font-size: 16px; color: #fff; background-color: #4CAF50; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h2>FAQ 수정</h2>
    <form method="post">
        <label for="category_id">카테고리:</label>
        <select id="category_id" name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['category_id'] ?>" <?= $category['category_id'] == $faq['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['category_name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="question">질문:</label>
        <input type="text" id="question" name="question" value="<?= htmlspecialchars($faq['question']) ?>" required>
        
        <label for="answer">답변:</label>
        <textarea id="answer" name="answer" required><?= htmlspecialchars($faq['answer']) ?></textarea>
        
        <label for="display_order">표시 순서:</label>
        <input type="number" id="display_order" name="display_order" value="<?= htmlspecialchars($faq['display_order']) ?>" required>
        
        <button type="submit">수정</button>
    </form>
</body>
</html>
