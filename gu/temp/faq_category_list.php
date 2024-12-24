<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// 카테고리 목록 조회
$sql = "SELECT * FROM RTU_FAQ_Category ORDER BY display_order ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>FAQ 카테고리 목록</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h2 { color: #4CAF50; font-size: 24px; }
        a { color: #4CAF50; text-decoration: none; font-weight: bold; }
        a:hover { color: #45a049; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; padding: 10px; border-bottom: 1px solid #ddd; }
        .add-btn { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; border-radius: 5px; text-align: center; margin-bottom: 15px; }
        .action-links { float: right; }
    </style>
</head>
<body>
    <h2>FAQ 카테고리 목록</h2>
    <a href="faq_category_add.php" class="add-btn">카테고리 추가</a>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li>
                <?= htmlspecialchars($category['category_name']) ?> 
                <div class="action-links">
                    <a href="faq_category_edit.php?id=<?= $category['category_id'] ?>">수정</a> |
                    <a href="faq_category_delete.php?id=<?= $category['category_id'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
