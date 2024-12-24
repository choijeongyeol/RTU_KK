<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $sql = "INSERT INTO RTU_Notice (title, content, created_at, pinYN, delYN) VALUES (:title, :content, NOW(), 'N', 'N')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->execute();
    header("Location: notice_list.php");
    exit;
}
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>공지사항 글쓰기</title>
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
    <h2>공지사항 글쓰기</h2>
    <form method="post">
        <label for="title">제목:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">내용:</label>
        <textarea id="content" name="content" required></textarea>

        <button type="submit">등록</button>
    </form>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	