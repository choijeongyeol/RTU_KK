<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');

// 현재 세션에서 partner_id 가져오기
if (!isset($_SESSION['partner_id'])) {
    die("Error: 세션에서 파트너 ID를 찾을 수 없습니다.");
}
$partner_id = $_SESSION['partner_id'];
$spartner_id = $_SESSION['spartner_id'];

// 지자체 목록 조회 쿼리
try {
	
	if ($spartner_id <> 0 ) {  // 지자체 이면,  add_sql 추가
		$add_sql = " and spartner_id = :spartner_id ";	
	}else{
		$add_sql = "";	
	}
	
    $sql = "
        SELECT 
            spartner_idx,
            spartner_id,
            spartner_name,
            spartner_tel,
            spartner_email,
            spartner_addr,
            spartner_use,
            created_at
        FROM RTU_spartner
        WHERE partner_id = :partner_id AND delYN = 'N' ".$add_sql."
        ORDER BY created_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
	
	if ($spartner_id <> 0 ) {  // 지자체 이면,  추가
		$stmt->bindParam(':spartner_id', $spartner_id, PDO::PARAM_INT);
	}
		
    $stmt->execute();
    $spartners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>

<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>

    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: center; font-size: 14px; }
        th { background-color: #f2f2f2; }
        .delete-button { color: white; background-color: red; padding: 5px 10px; border: none; cursor: pointer; }
        .custom-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border: 2px solid #000;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .custom-popup h3 { margin: 0 0 10px; }
        .popup-button {
            margin: 10px 5px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .popup-confirm { background-color: green; color: white; }
        .popup-cancel { background-color: red; color: white; }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
    <script>
        // 선택/해제 버튼
        function toggleSelect() {
            const checkboxes = document.querySelectorAll('input[name="select_spartner"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        }

        // 선택 삭제 버튼
        function deleteSelected() {
            const selectedIds = [];
            document.querySelectorAll('input[name="select_spartner"]:checked').forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });

            if (selectedIds.length === 0) {
                alert('삭제할 지자체를 선택하세요.');
                return;
            }

            showPopup(selectedIds);
        }

        // 커스텀 팝업 표시
        function showPopup(selectedIds) {
            const overlay = document.querySelector('.popup-overlay');
            const popup = document.querySelector('.custom-popup');
            overlay.style.display = 'block';
            popup.style.display = 'block';

            document.getElementById('confirm-delete').onclick = function() {
                submitDeleteForm(selectedIds);
            };
        }

        // 팝업 닫기
        function closePopup() {
            document.querySelector('.popup-overlay').style.display = 'none';
            document.querySelector('.custom-popup').style.display = 'none';
        }

        // 삭제 요청 제출
        function submitDeleteForm(selectedIds) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_spartner_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    </script>
	
	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
 
		 <!-- 본문 내용 시작 -->
		<main>
		<h2>지자체 목록</h2>
		<?
			if ($spartner_id < 1000 ) {  // 지자체가 아니면,
		?>
		<button onclick="toggleSelect()">선택/해제</button>
		<button onclick="deleteSelected()">선택삭제</button>
		<button><a href="spartner_input.php">지자체 등록</a></button>
		<?
			}
		?>

		<table>
			<tr>
				<th>선택</th>
				<th>지자체 ID</th>
				<th>지자체 이름</th>
				<th>전화번호</th>
				<th>지자체관리자 관리</th>
				<th>주소</th>
				<th>사용 여부</th>
				<th>등록 날짜</th>
			</tr>
			<?php foreach ($spartners as $spartner): ?>
				<tr>
					<td><input type="checkbox" name="select_spartner" value="<?= $spartner['spartner_idx'] ?>"></td>
					<td><?= htmlspecialchars($spartner['spartner_id']) ?></td>
					<td><?= htmlspecialchars($spartner['spartner_name']) ?></td>
					<td><?= htmlspecialchars($spartner['spartner_tel']) ?></td>
					<td><a href="spartner_adminlist.php?spartner_id=<?= htmlspecialchars($spartner['spartner_id']) ?>">관리 바로가기</a></td>
					<td><?= htmlspecialchars($spartner['spartner_addr']) ?></td>
					<td><?= $spartner['spartner_use'] === 'Y' ? '사용 중' : '사용 안 함' ?></td>
					<td><?= htmlspecialchars($spartner['created_at']) ?></td>
				</tr>
			<?php endforeach; ?>
		</table>

		<!-- 커스텀 팝업 -->
		<div class="popup-overlay" onclick="closePopup()"></div>
		<div class="custom-popup">
			<h3>정말 삭제하시겠습니까?</h3>
			<p>선택된 지자체를 삭제합니다.</p>
			<button class="popup-button popup-confirm" id="confirm-delete">예</button>
			<button class="popup-button popup-cancel" onclick="closePopup()">아니오</button>
		</div>

		</main>
		 <!-- 본문 내용 끝. -->

<?php require_once($root_dir.'inc/footer.php'); ?>	