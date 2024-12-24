<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// 삭제 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_ids'])) {
    $delete_user_ids = $_POST['delete_user_ids'];

    try {
        // 트랜잭션 시작
        $conn->beginTransaction();

        // 선택된 사용자 삭제
        $sql = "UPDATE RTU_user SET delYN = 'Y' WHERE user_idx = :user_idx";
        $stmt = $conn->prepare($sql);

        foreach ($delete_user_ids as $user_idx) {
            $stmt->bindValue(':user_idx', $user_idx, PDO::PARAM_INT);
            $stmt->execute();
        }

        // 트랜잭션 커밋
        $conn->commit();

        // 삭제 완료 메시지
        echo "<script>
                alert('선택된 사용자가 삭제되었습니다.');
                window.location.href = window.location.href;
              </script>";
    } catch (PDOException $e) {
        // 트랜잭션 롤백
        $conn->rollBack();
        echo "<script>
                alert('삭제 중 오류가 발생했습니다: " . addslashes($e->getMessage()) . "');
              </script>";
    }
}

// 선택된 지자체 ID 처리
$selected_spartner_id = $_GET['spartner_id'] ?? '';

// 지자체 목록 가져오기
try {
    $spartner_sql = "SELECT spartner_id, spartner_name FROM RTU_spartner WHERE partner_id = :partner_id";
    $partner_stmt = $conn->prepare($spartner_sql);
    $partner_stmt->bindParam(':partner_id', $_SESSION['partner_id'], PDO::PARAM_INT);
    $partner_stmt->execute();
    $partners = $partner_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "지자체 데이터를 불러오는 중 오류가 발생했습니다: " . $e->getMessage();
    exit;
}

// 사용자 목록 조회
try {
    $sql = "
        SELECT 
            user_idx,
            spartner_id,
            user_id,
            user_name,
            user_tel,
            user_email,
            user_role,
            sms_receive,
            email_receive,
            user_rdate,
            user_use,
            created_at,
            delYN
        FROM RTU_user
        WHERE delYN = 'N' AND partner_id = :partner_id
    ";

    if ($selected_spartner_id !== '') {
        $sql .= " AND spartner_id = :spartner_id";
    }

    $sql .= " ORDER BY user_rdate DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':partner_id', $_SESSION['partner_id'], PDO::PARAM_INT);

    if ($selected_spartner_id !== '') {
        $stmt->bindParam(':spartner_id', $selected_spartner_id, PDO::PARAM_INT);
    }

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    </style>
    <script>
        // 지자체 필터링
        function filterByPartner() {
            const spartnerId = document.getElementById('spartnerSelect').value;
            const url = new URL(window.location.href);
            url.searchParams.set('spartner_id', spartnerId);
            window.location.href = url;
        }

        // 선택/해제 버튼
        function toggleSelect() {
            const checkboxes = document.querySelectorAll('input[name="select_user"]');
            checkboxes.forEach(checkbox => checkbox.checked = !checkbox.checked);
        }

        // 선택 삭제 버튼
        function deleteSelected() {
            const selectedIds = [];
            document.querySelectorAll('input[name="select_user"]:checked').forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });

            if (selectedIds.length === 0) {
                alert('삭제할 사용자를 선택하세요.');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_user_ids[]';
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
		
<h2>이용자 목록</h2>

<!-- 지자체 선택 필터 -->
<?php if (isset($_GET['spartner_id'])) {?>
<a href="user_form.php?spartner_id=<?echo $_GET['spartner_id'];?>">이용자등록</a> / 	
<?}?>

<label for="spartnerSelect">지자체 선택:</label>
<select id="spartnerSelect" onchange="filterByPartner()">
    <option value="">전체보기</option>
    <?php foreach ($partners as $partner): ?>
        <option value="<?= htmlspecialchars($partner['spartner_id']) ?>" <?= $partner['spartner_id'] == $selected_spartner_id ? 'selected' : '' ?>>
            <?= htmlspecialchars($partner['spartner_name']) ?>
        </option>
    <?php endforeach; ?>
</select>

<button onclick="toggleSelect()">선택/해제</button>
<button onclick="deleteSelected()">선택삭제</button>

<table>
    <tr>
        <th>선택</th>
        <th>사용자 ID</th>
        <th>사용자 이름</th>
        <th>전화번호</th>
        <th>이메일</th>
        <!-- <th>SMS 수신</th> -->
        <th>이메일 수신</th>
        <th>등록 날짜</th>
        <th>사용 여부</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><input type="checkbox" name="select_user" value="<?= $user['user_idx'] ?>"></td>
            <td><?= htmlspecialchars($user['user_id']) ?></td>
            <td><?= htmlspecialchars($user['user_name']) ?></td>
            <td><?= htmlspecialchars($user['user_tel']) ?></td>
            <td><?= htmlspecialchars($user['user_email']) ?></td>
            <!-- <td><?= $user['sms_receive'] == 1 ? 'Y' : 'N' ?></td> -->
            <td><?= $user['email_receive'] == 1 ? 'Y' : 'N' ?></td>
            <td><?= htmlspecialchars($user['created_at']) ?></td>
            <td><?= $user['user_use'] == 'Y' ? '사용 중' : '사용 안 함' ?></td>
        </tr>
    <?php endforeach; ?>
</table>
			
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	