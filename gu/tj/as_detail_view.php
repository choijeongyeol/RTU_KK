<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// AS ID 가져오기
$as_id = isset($_GET['as_id']) ? $_GET['as_id'] : 0;

try {
    // 상세 정보 조회
    $sql = "
        SELECT 
            ar.as_id,
            ar.as_num,
            ar.completion_date,
            ar.notes,
			ar.reservation_date,
			ar.as_memo,
            ih.issue_name,
            ih.issue_start_date,
            ih.facility_id,
            ih.lora_idx,
            ih.status AS issue_status,
            ih.fault_description AS fault_description,
            ih.fault_description AS detailed_fault,
            ih.viewline,
            ih.created_at AS issue_created_at,
			CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS 발전소명
        FROM RTU_AS_Request ar
        JOIN RTU_Issue_History_New ih ON ar.issue_id = ih.id
        JOIN RTU_issue_type it ON ih.issue_name = it.issue_type_id
        JOIN RTU_facility f ON ih.facility_id = f.cid
        JOIN RTU_user u ON ih.user_idx = u.user_idx
        JOIN RTU_lora l ON ih.lora_idx = l.id		
		
        WHERE ar.as_id = :as_id
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':as_id', $as_id, PDO::PARAM_INT);
    $stmt->execute();
    $detail = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$detail) {
        echo "해당 AS 내역이 존재하지 않습니다.";
        exit;
    }
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>
<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>AS 내역 상세보기</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 20px; }
        h2 { color: #4CAF50; }
        .detail-box { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; }
    </style>
    <script>
        function cancelAS(asId) {
            if (confirm("정말로 AS를 취소하시겠습니까?")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "as_cancel.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert("AS취소되었습니다");
                        window.location.reload(); // 팝업창 리프레시
                    }
                };
                xhr.send("as_id=" + asId);
            }
        }
    </script>
</head>
<body>
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
		
<h2>AS 현황 / 내역</h2>

<div class="detail-box">
    <strong>AS 상태:</strong> <?php
    switch ($detail['issue_status']) {
        case '2':
            echo "접수완료";
            break;
        case '3':
            echo "AS예정";
            break;
        case '4':
            echo "AS완료";
            break;
        case '5':
            echo "AS취소";
            break;
        default:
            echo "진행중";
    }
    ?>
</div>

<?php if ($detail['issue_status'] != '2'): ?>
<div class="detail-box">
    <strong>AS 번호:</strong> <?= htmlspecialchars($detail['as_num']) ?>
</div>
<?php endif; ?>

<?php if ($detail['issue_status'] == '4'): ?>
<div class="detail-box">
    <strong>AS 완료일자:</strong> <?= htmlspecialchars($detail['completion_date']) ?><br>
    <strong>AS 내용:</strong> <?= htmlspecialchars($detail['as_memo']) ?>
</div>
<?php elseif ($detail['issue_status'] == '3'): ?>
<div class="detail-box">
    <strong>AS 예정일자:</strong> <?= htmlspecialchars($detail['reservation_date']) ?>
</div>
<?php endif; ?>

<div class="detail-box">
    <strong>최초 장애일시:</strong> <?= htmlspecialchars($detail['issue_start_date']) ?><br>
    <strong>장애 증상:</strong> <?= htmlspecialchars($detail['fault_description']) ?>
</div>
<div class="detail-box">
    <strong>발전소:</strong> <?= htmlspecialchars($detail['발전소명']) ?><br>
    <strong>RTU 장비:</strong> 태양광 LoRa RTU<!-- <?= htmlspecialchars($detail['lora_idx']) ?> --><br>
</div>

  <?php if (($detail['issue_status'] == '3') || ($detail['issue_status'] == '4')) {?>
<div class="detail-box">
    <strong>상세 증상:</strong><br>
    <strong><?= htmlspecialchars($detail['notes']) ?></strong>
</div>

<div class="detail-box">
    <strong>첨부 이미지:</strong><br>
    <img src="첨부이미지_예시1.png" alt="첨부 이미지 1" style="width: 100px; height: 100px;">
    <img src="첨부이미지_예시2.png" alt="첨부 이미지 2" style="width: 100px; height: 100px;">
</div>
<?}?>

<?php if (($detail['issue_status'] == '2') || ($detail['issue_status'] == '3')): ?>
<center><input type="button" value="AS취소" onclick="cancelAS(<?= $detail['as_id'] ?>)"></center>
<?php endif; ?>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	
