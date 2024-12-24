<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');

// 파트너 목록 조회
try {
    $sql = "
        SELECT 
            admin_idx,
            partner_id,
            admin_id,
            admin_name,
            admin_role,
            admin_rdate,
            admin_use
        FROM RTU_admin
        WHERE delYN = 'N' and partner_id='".$_SESSION['partner_id']."' and spartner_id < 1000
        ORDER BY partner_id ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>


<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
 
		 <!-- 본문 내용 시작 -->
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: center; font-size: 14px; }
        th { background-color: #f2f2f2; }
        .action-button { padding: 5px 10px; margin: 5px; cursor: pointer; }
        .add-button { background-color: green; color: white; }
        .delete-button { background-color: red; color: white; }
    </style>
	
		<main>
			<h1>관리자 목록</h1>
			<a href="partner_input.php"><button class="action-button add-button">관리자 등록</button></a>
			<table>
				<tr>
					<th>파트너 ID</th>
					<th>관리자 ID</th>
					<th>관리자 이름</th>
					<!-- <th>관리자 역할</th> -->
					<th>등록 날짜</th>
					<th>사용 여부</th>
				</tr>
				<?php foreach ($partners as $partner): ?>
					<tr>
						<td><?= htmlspecialchars($partner['partner_id']) ?></td>
						<td><?= htmlspecialchars($partner['admin_id']) ?></td>
						<td><?= htmlspecialchars($partner['admin_name']) ?></td>
						<!-- <td><?= htmlspecialchars($partner['admin_role']) ?></td> -->
						<td><?= htmlspecialchars($partner['admin_rdate']) ?></td>
						<td><?= $partner['admin_use'] === 'Y' ? '사용 중' : '사용 안 함' ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</main>			
		 <!-- 본문 내용 끝. -->

<?php require_once($root_dir.'inc/footer.php'); ?>	