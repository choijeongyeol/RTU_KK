<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// 데이터베이스 연결
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

// 사용자 입력 검색어 처리
$search = $_GET['search'] ?? '';

// 법정동 코드 검색 쿼리
$sql = "SELECT legalcode, legaldong FROM R2_legalcode WHERE legaldong LIKE :search AND USE_YN = 'Y' LIMIT 50";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>법정코드 검색</title>

	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
    <h1>법정코드 검색</h1>
    <form method="get">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">검색</button>
    </form>
    <table border="1">
        <tr>
            <th>법정코드</th>
            <th>법정동명</th>
            <th>선택</th>
        </tr>
        <?php foreach ($results as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['legalcode']) ?></td>
            <td><?= htmlspecialchars($row['legaldong']) ?></td>
            <td>
                <button type="button" onclick="selectLegalcode('<?= $row['legalcode'] ?>', '<?= $row['legaldong'] ?>')">
                    선택
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <script>
    function selectLegalcode(legalcode, legaldong) {
        // 부모 창의 주소 입력 필드에 값 전달
        window.opener.document.getElementById('legalcode').value = legalcode;
        window.opener.document.getElementById('legaldong').value = legaldong;
        window.close();
    }
    </script>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	