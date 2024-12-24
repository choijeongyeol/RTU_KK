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
            ar.saved_file_name,
            ar.original_file_name,
            ih.issue_name,
            ih.issue_start_date,
            ih.facility_id,
            ih.lora_idx,
            ih.status AS issue_status,
            ih.fault_description AS fault_description,
            CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS 발전소명
        FROM RTU_AS_Request ar
        JOIN RTU_Issue_History_New ih ON ar.issue_id = ih.id
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
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 20px; }
        h2 { color: #4CAF50; }
        .detail-box { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; }
        #scrolling {
            height: 50vh; /* 화면의 절반 높이 */
            overflow-y: auto; /* 세로 스크롤 활성화 */
            border: 1px solid #ddd;
            padding: 10px;
        }
        .memo-box textarea {
            width: 100%; /* 화면 너비에 맞추기 */
            height: 100px; /* 높이 조절 */
            resize: none; /* 사용자가 크기 조정 못하게 */
        }		
        img { max-width: 100px; max-height: 100px; margin-right: 10px; }
    </style>
    <script>
        function memo_update(asId) {
            const memoContent = document.getElementById("as_memo").value;
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "asmemo_update.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        alert("메모가 업데이트되었습니다.");
                        window.location.reload(); // 업데이트 후 팝업창 리프레시
                    } else {
                        alert("메모 업데이트 중 오류가 발생했습니다.");
                    }
                }
            };
            xhr.send("as_id=" + asId + "&as_memo=" + encodeURIComponent(memoContent));
        }
    </script>
	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>

<h2>AS기사 수리 메모</h2>
<div id="scrolling">


<div class="detail-box">
    <strong>AS 상태:</strong> <?php
    switch ($detail['issue_status']) {
        case '2': echo "접수완료"; break;
        case '3': echo "AS예정"; break;
        case '4': echo "AS완료"; break;
        case '5': echo "AS취소"; break;
        default: echo "진행중";
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

<?php if (($detail['issue_status'] == '3') || ($detail['issue_status'] == '4')): ?>
    <div class="detail-box">
        <strong>상세 증상:</strong><br>
        <strong><?= htmlspecialchars($detail['notes']) ?></strong>
    </div>
<?php endif; ?>



<?php if (!empty($detail['saved_file_name'])): ?>
    <div class="detail-box">
        <strong>첨부파일:</strong><br>
        <a href="/uploads/<?= htmlspecialchars($detail['saved_file_name']) ?>" download>
            <?= htmlspecialchars($detail['original_file_name']) ?>
        </a>
        <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $detail['saved_file_name'])): ?>
            <br><img src="/uploads/<?= htmlspecialchars($detail['saved_file_name']) ?>" alt="첨부 이미지">
        <?php endif; ?>
    </div>
<?php endif; ?>


</div>





<br><br>
<div class="detail-box memo-box">
    <strong>AS 기사메모란</strong><br>
    <textarea id="as_memo"><?= htmlspecialchars($detail['as_memo']) ?></textarea>
</div>
 
<center><input type="button" value="메모업데이트" onclick="memo_update(<?= $detail['as_id'] ?>)"></center>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	