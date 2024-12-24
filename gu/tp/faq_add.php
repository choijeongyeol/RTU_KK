<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

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

    $sql = "INSERT INTO RTU_FAQ (category_id, question, answer, display_order, is_active) VALUES (:category_id, :question, :answer, :display_order, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':answer', $answer);
    $stmt->bindParam(':display_order', $display_order);
    $stmt->execute();
    header("Location: faq_list.php");
    exit;
}
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>FAQ 추가</title>
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
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
    <h2>FAQ 추가</h2>
    <form method="post">
        <label for="category_id">카테고리:</label>
        <select id="category_id" name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="question">질문:</label>
        <input type="text" id="question" name="question" required>
        
        <label for="answer">답변:</label>
        <textarea id="answer" name="answer" required></textarea>
        
        <label for="display_order">표시 순서:</label>
        <input type="number" id="display_order" name="display_order" required>
        
        <button type="submit">등록</button>
    </form>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	