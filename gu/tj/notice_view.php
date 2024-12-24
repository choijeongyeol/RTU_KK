<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
$notice_id = $_GET['id'];

$sql = "SELECT * FROM RTU_Notice WHERE notice_id = :notice_id AND delYN = 'N'";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':notice_id', $notice_id, PDO::PARAM_INT);
$stmt->execute();
$notice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$notice) {
    echo "공지사항이 존재하지 않습니다.";
    exit;
}
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>공지사항 보기</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        h2 {
            color: #4CAF50;
            font-size: 24px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 5px;
        }
        .content {
            font-size: 16px;
            line-height: 1.6;
            margin-top: 20px;
            white-space: pre-line; /* 줄바꿈을 유지 */
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .date {
            color: #888;
            font-size: 0.9em;
            margin-top: 10px;
            text-align: right;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .actions a {
            color: #333;
            text-decoration: none;
            margin-right: 15px;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ccc;
            transition: background-color 0.3s ease;
        }
        .actions a:hover {
            background-color: #4CAF50;
            color: #fff;
            border-color: #4CAF50;
        }
    </style>

	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
    <h2><?= htmlspecialchars($notice['title']) ?></h2>
    <p class="content"><?= htmlspecialchars($notice['content']) ?></p>
    <div class="date">작성일: <?= htmlspecialchars($notice['created_at']) ?></div>
    
    <div class="actions">
        <a href="notice_edit.php?id=<?= $notice['notice_id'] ?>">수정</a>
        <a href="notice_delete.php?id=<?= $notice['notice_id'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
    </div>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	