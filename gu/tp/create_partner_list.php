<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');

// 파트너 목록 조회
try {
    $sql = "SELECT code_idx, code_id, code_name, code_tel, company_code, created_at 
            FROM RTU_partner 
            WHERE code_type = 'M'";
			
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
			<h1>기업 목록</h1>
			<a href="create_partner_input.php"><button class="action-button add-button">기업 등록</button></a>
			<table>
				<tr>
					<th>코드 IDX</th>
					<th>코드 ID</th>
					<th>관리기관 이름</th>
					<th>전화번호</th>
					<th>관리기관 코드</th>
					<th>생성일</th>
				</tr>
				<?php foreach ($partners as $partner): ?>
					<tr>
						<td><?= htmlspecialchars($partner['code_idx']) ?></td>
						<td><?= htmlspecialchars($partner['code_id']) ?></td>
						<td><?= htmlspecialchars($partner['code_name']) ?></td>
						<td><?= htmlspecialchars($partner['code_tel']) ?></td>
						<td><?= htmlspecialchars($partner['company_code']) ?></td>
						<td><?= htmlspecialchars($partner['created_at']) ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</main>			
		 <!-- 본문 내용 끝. -->

<?php require_once($root_dir.'inc/footer.php'); ?>	