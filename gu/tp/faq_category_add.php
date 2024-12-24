<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];
    $display_order = $_POST['display_order'];

    $sql = "INSERT INTO RTU_FAQ_Category (category_name, display_order, is_active) VALUES (:category_name, :display_order, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category_name', $category_name);
    $stmt->bindParam(':display_order', $display_order);
    $stmt->execute();
    header("Location: faq_category_list.php");
    exit;
}
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>카테고리 추가</title>
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
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
    <h2>FAQ 카테고리 추가</h2>
    <form method="post">
        <label for="category_name">카테고리 이름:</label>
        <input type="text" id="category_name" name="category_name" required>
        
        <label for="display_order">표시 순서:</label>
        <input type="number" id="display_order" name="display_order" required>
        
        <button type="submit">등록</button>
    </form>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	