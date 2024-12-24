<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$category_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];
    $display_order = $_POST['display_order'];

    $sql = "UPDATE RTU_FAQ_Category SET category_name = :category_name, display_order = :display_order WHERE category_id = :category_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category_name', $category_name);
    $stmt->bindParam(':display_order', $display_order);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();
    header("Location: faq_category_list.php");
    exit;
}

$sql = "SELECT * FROM RTU_FAQ_Category WHERE category_id = :category_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':category_id', $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>카테고리 수정</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h2 { color: #4CAF50; font-size: 24px; }
        form { margin-top: 20px; display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        button { padding: 10px 20px; font-size: 16px; color: #fff; background-color: #4CAF50; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h2>FAQ 카테고리 수정</h2>
    <form method="post">
        <label for="category_name">카테고리 이름:</label>
        <input type="text" id="category_name" name="category_name" value="<?= htmlspecialchars($category['category_name']) ?>" required>
        
        <label for="display_order">표시 순서:</label>
        <input type="number" id="display_order" name="display_order" value="<?= htmlspecialchars($category['display_order']) ?>" required>
        
        <button type="submit">수정</button>
    </form>
</body>
</html>
