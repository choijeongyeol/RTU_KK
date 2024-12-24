<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// Technician 목록 조회
try {
    $sql = "SELECT technician_id, name FROM RTU_Technician";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $technicians = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>

<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>

    <title>AS 입력 - 관리자 모드</title>
    <style>
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .input-box, .textarea-box, .select-box {
            width: 100%;
            padding: 8px;
            font-size: 14px;
        }
        .submit-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
		

<h2>AS 입력 - 관리자 모드</h2>

<form action="as_request_submit.php" method="post">
    <div class="form-group">
        <label for="issue_id">Issue ID</label>
        <input type="number" id="issue_id" name="issue_id" class="input-box" required>
    </div>

    <div class="form-group">
        <label for="notes">AS 요청 상세 설명</label>
        <textarea id="notes" name="notes" class="textarea-box" rows="4" placeholder="AS 요청에 대한 상세 설명을 입력하세요"></textarea>
    </div>

    <div class="form-group">
        <label for="technician_id">담당자 선택</label>
        <select id="technician_id" name="technician_id" class="select-box">
            <option value="">선택하세요</option>
            <?php foreach ($technicians as $technician): ?>
                <option value="<?= htmlspecialchars($technician['technician_id']) ?>">
                    <?= htmlspecialchars($technician['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="status">AS 요청 상태</label>
        <select id="status" name="status" class="select-box">
            <option value="1">미신청</option>
            <option value="2" selected>신청</option>
            <option value="3">처리중</option>
            <option value="4">처리완료</option>
        </select>
    </div>

    <button type="submit" class="submit-button">AS 신청</button>
</form>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	