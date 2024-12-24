<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

$sql = "SELECT * FROM RTU_Notice WHERE delYN = 'N' ORDER BY pinYN DESC, created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>공지사항 목록</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .pin-icon {
            color: #ff0000;
        }
        .date {
            color: #888;
            font-size: 0.9em;
        }
        a {
            color: #333;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>

	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
    <h2>공지사항 목록</h2>
    <table>
        <tr>
            <th>제목</th>
            <th>작성일</th>
            <!-- <th>고정</th> -->
        </tr>
        <?php foreach ($notices as $notice): ?>
            <tr>
                <td>
                    <a href="notice_view.php?id=<?= $notice['notice_id'] ?>">
                        <?= htmlspecialchars($notice['title']) ?>
                    </a>
                </td>
                <td class="date"><?= htmlspecialchars($notice['created_at']) ?></td>
                <!-- <td class="pin-icon"><?= $notice['pinYN'] == 'Y' ? '📌' : '' ?></td> -->
            </tr>
        <?php endforeach; ?>
    </table>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	