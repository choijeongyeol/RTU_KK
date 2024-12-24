<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// FAQ 목록 조회
$sql = "SELECT f.*, c.category_name FROM RTU_FAQ f JOIN RTU_FAQ_Category c ON f.category_id = c.category_id ORDER BY c.display_order, f.display_order";
$stmt = $conn->prepare($sql);
$stmt->execute();
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>FAQ 목록</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h2 { color: #4CAF50; font-size: 24px; }
        a { color: #4CAF50; text-decoration: none; font-weight: bold; }
        a:hover { color: #45a049; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; padding: 10px; border-bottom: 1px solid #ddd; }
        .add-btn { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; border-radius: 5px; text-align: center; margin-bottom: 15px; }
        .action-links { float: right; }
        .category { font-weight: bold; }
    </style>

	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
    <h2>FAQ 목록</h2>
    <a href="faq_add.php" class="add-btn">FAQ 추가</a>
    <ul>
        <?php foreach ($faqs as $faq): ?>
            <li>
                <span class="category">[<?= htmlspecialchars($faq['category_name']) ?>]</span> <?= htmlspecialchars($faq['question']) ?> 
                <div class="action-links">
                    <a href="faq_edit.php?id=<?= $faq['faq_id'] ?>">수정</a> |
                    <a href="faq_delete.php?id=<?= $faq['faq_id'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	