<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$notice_id = $_GET['id'];

// 데이터 수정 로직
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    try {
        // 공지사항 업데이트 SQL
        $sql = "UPDATE RTU_Notice SET title = :title, content = :content, updated_at = NOW() WHERE notice_id = :notice_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':notice_id', $notice_id, PDO::PARAM_INT);
        $stmt->execute();

        // 업데이트 후 목록 페이지로 이동
        header("Location: notice_list.php");
        exit;
    } catch (PDOException $e) {
        echo "업데이트 오류: " . $e->getMessage();
    }
}

// 공지사항 상세 조회 로직
try {
    $sql = "SELECT * FROM RTU_Notice WHERE notice_id = :notice_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':notice_id', $notice_id, PDO::PARAM_INT);
    $stmt->execute();
    $notice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notice) {
        echo "공지사항이 존재하지 않습니다.";
        exit;
    }
} catch (PDOException $e) {
    echo "데이터 조회 오류: " . $e->getMessage();
    exit;
}
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>공지사항 수정</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #4CAF50;
            font-size: 24px;
        }
        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            min-height: 150px;
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
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
    <h2>공지사항 수정</h2>
    <form method="post">
        <label for="title">제목:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($notice['title']) ?>" required>

        <label for="content">내용:</label>
        <textarea id="content" name="content" required><?= htmlspecialchars($notice['content']) ?></textarea>

        <button type="submit">수정</button>
    </form>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	