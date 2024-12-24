<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php  
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// 검색 필터 변수 초기화
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';
$searchStatus = $_POST['status'] ?? '';
$searchUserName = $_POST['user_name'] ?? '';
$searchUserID = $_POST['user_id'] ?? '';
$searchCID = $_POST['cid'] ?? ''; // CID 검색 필터 추가
$searchLoRaID = $_POST['lora_id'] ?? ''; // LoRaID 검색 필터 추가

try {
    // 장애 이력 데이터를 조회하는 쿼리 (중복 제거)
    $sql = "
        SELECT 
            ih.id AS issue_id,
            CASE 
                WHEN ih.fault_description IS NOT NULL AND ih.fault_description != '' THEN
                    CASE 
                        WHEN CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')) + 1 > 1 THEN
                            CONCAT(SUBSTRING_INDEX(ih.fault_description, ',', 1), ' 외 ', 
                                   CHAR_LENGTH(ih.fault_description) - CHAR_LENGTH(REPLACE(ih.fault_description, ',', '')))
                        ELSE 
                            ih.fault_description
                    END
                ELSE 
                    it.issue_name
            END AS 장애명, 
            it.issue_type_id AS issue_type_id, 
            earliest.min_date AS 장애최초일시,
            latest.max_date AS 장애최근일시,
            ih.status AS 해결여부,
            f.cid AS CID정보,
            f.lora_id AS LoRaID정보, 
            u.user_name AS 사용자명,
            u.user_id AS 사용자ID,
            ih.fault_description AS 장애설명,  -- 장애설명 필드 유지
            ih.four_hex AS four_hex,           -- four_hex 추가
            ih.created_at AS 생성시각,         -- created_at 추가
            ih.updated_at AS 처리시각,         -- updated_at 추가
            CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS 발전소명        
        FROM RTU_Issue_History_New ih
        JOIN RTU_issue_type it ON ih.issue_name = it.issue_type_id
        JOIN RTU_facility f ON ih.facility_id = f.cid
        JOIN RTU_user u ON ih.user_idx = u.user_idx
        JOIN RTU_lora l ON ih.lora_idx = l.id
        INNER JOIN (
            SELECT facility_id, issue_name, status, four_hex,
                   MIN(issue_start_date) AS min_date, 
                   MAX(issue_last_date) AS max_date
            FROM RTU_Issue_History_New
            WHERE viewline = 1
            GROUP BY facility_id, issue_name, status, four_hex
        ) AS earliest
        ON ih.facility_id = earliest.facility_id
        AND ih.issue_name = earliest.issue_name
        AND ih.status = earliest.status
        AND ih.issue_start_date = earliest.min_date
        AND ih.four_hex = earliest.four_hex
        INNER JOIN (
            SELECT facility_id, issue_name, status, four_hex, MAX(issue_last_date) AS max_date
            FROM RTU_Issue_History_New
            WHERE viewline = 1
            GROUP BY facility_id, issue_name, status, four_hex
        ) AS latest
        ON ih.facility_id = latest.facility_id
        AND ih.issue_name = latest.issue_name
        AND ih.status = latest.status
        AND ih.four_hex = latest.four_hex
        WHERE ih.viewline = 1 and partner_id = '".$_SESSION['partner_id']."' 
    ";

    // 조건 추가
    if ($startDate && $endDate) {
		$endDateQuery = $endDate .' 23:59:59'; // 쿼리에 사용할 endDateQuery
        $sql .= " AND ih.issue_start_date >= :startDate AND issue_last_date <= :endDateQuery";
    }
    if ($searchStatus) {
        $sql .= " AND ih.status = :status";
    }
    if ($searchUserName) {
        $sql .= " AND u.user_name LIKE :user_name";
    }
    if ($searchUserID) {
        $sql .= " AND u.user_id LIKE :user_id";
    }
    if ($searchCID) {
        $sql .= " AND f.cid LIKE :cid";
    }
    if ($searchLoRaID) {
        $sql .= " AND f.lora_id LIKE :lora_id";
    }

    $sql .= "
        ORDER BY 
            CASE WHEN ih.status = '0' THEN 0 
                 WHEN ih.status = '1' THEN 1 
                 WHEN ih.status = '2' THEN 2 
                 WHEN ih.status = '3' THEN 3 
                 WHEN ih.status = '4' THEN 4 
            END, 
            ih.issue_start_date desc
    ";
 

    $stmt = $conn->prepare($sql);
 
 // echo "<PRE>".$sql."</PRE>";

    // 파라미터 바인딩
    if ($startDate && $endDate) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDateQuery', $endDateQuery); // endDateQuery 바인딩
    }
    if ($searchStatus) {
        $stmt->bindParam(':status', $searchStatus);
    }
    if ($searchUserName) {
        $user_name = '%' . $searchUserName . '%';
        $stmt->bindParam(':user_name', $user_name);
    }
    if ($searchUserID) {
        $user_id = '%' . $searchUserID . '%';
        $stmt->bindParam(':user_id', $user_id);
    }
    if ($searchCID) {
        $cid = '%' . $searchCID . '%';
        $stmt->bindParam(':cid', $cid);
    }
    if ($searchLoRaID) {
        $lora_id = '%' . $searchLoRaID . '%';
        $stmt->bindParam(':lora_id', $lora_id);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>

<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
    <title>장애 이력 목록 - 관리자 모드</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .search-box label {
            margin-right: 10px;
        }
    </style>
    <script>
        function openPopup(cid, issueTypeId, fourHex) {
            const url = "issue_cid_popup.php?cid=" + cid + "&issue_type_id=" + encodeURIComponent(issueTypeId) + "&four_hex=" + encodeURIComponent(fourHex);
            window.open(url, "CIDPopup", "width=600,height=400,scrollbars=yes");
        }
    </script>

	</head>
	<body>                                                     
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>

<h2>장애 이력 목록 - 관리자 모드</h2>

<!-- 검색 필터 -->
<form method="post" action="" class="search-box">
    <label for="startDate">기간:</label>
    <input type="date" id="startDate" name="startDate" value="<?= htmlspecialchars($startDate) ?>">
    <label for="endDate">~</label>
    <input type="date" id="endDate" name="endDate" value="<?= htmlspecialchars($endDate) ?>">

    <label for="status">장애 상태:</label>
    <select id="status" name="status">
        <option value="">전체</option>
        <option value="1" <?= $searchStatus == '1' ? 'selected' : '' ?>>미접수</option>
        <option value="2" <?= $searchStatus == '2' ? 'selected' : '' ?>>접수완료</option>
        <option value="3" <?= $searchStatus == '3' ? 'selected' : '' ?>>AS예정</option>
        <option value="4" <?= $searchStatus == '4' ? 'selected' : '' ?>>AS완료</option>
        <option value="5" <?= $searchStatus == '5' ? 'selected' : '' ?>>AS취소</option>
    </select>

    <label for="user_name">사용자명:</label>
    <input type="text" id="user_name" name="user_name" value="<?= htmlspecialchars($searchUserName) ?>">

    <label for="user_id">사용자ID:</label>
    <input type="text" id="user_id" name="user_id" value="<?= htmlspecialchars($searchUserID) ?>">

    <label for="cid">CID:</label>
    <input type="text" id="cid" name="cid" value="<?= htmlspecialchars($searchCID) ?>">

    <label for="lora_id">LoRaID:</label>
    <input type="text" id="lora_id" name="lora_id" value="<?= htmlspecialchars($searchLoRaID) ?>">

    <button type="submit">검색</button>
</form>

<form action="issue_history_update.php" method="post">
    <table>
        <tr>
            <th>NO</th>
            <th>장애명</th>
            <th>장애설명</th> <!-- 장애설명 칼럼 추가 -->
            <th>장애최초일시</th>
            <th>장애최근일시</th>
            <th>CID정보</th>
            <th>LoRaID정보</th>
            <th>사용자명</th>
            <th>사용자ID</th>
            <th>발전소명</th>
            <th>해결여부</th>
            <th>처리시각</th> <!-- 처리시각 추가 -->
        </tr>

        <?php if (!empty($results)): ?>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['issue_id']) ?></td>
                    <td><?= htmlspecialchars($row['장애명']) ?></td>
                    <td><?= htmlspecialchars($row['장애설명']) ?></td> <!-- 장애설명 출력 -->
                    <td><?= htmlspecialchars($row['장애최초일시']) ?></td>
                    <td><?= htmlspecialchars($row['장애최근일시']) ?></td>
                    <td><a href="javascript:void(0);" onclick="openPopup('<?= htmlspecialchars($row['CID정보']) ?>', '<?= htmlspecialchars($row['issue_type_id']) ?>', '<?= htmlspecialchars($row['four_hex']) ?>');"><?= htmlspecialchars($row['CID정보']) ?></a></td>
                    <td><?= htmlspecialchars($row['LoRaID정보']) ?></td>
                    <td><?= htmlspecialchars($row['사용자명']) ?></td>
                    <td><?= htmlspecialchars($row['사용자ID']) ?></td>
                    <td><?= htmlspecialchars($row['발전소명']) ?></td>
                     <td>
                        <select name="status[<?= $row['issue_id'] ?>]">
                            <option value="1" <?= $row['해결여부'] == '1' ? 'selected' : '' ?>>미접수</option>
                            <option value="2" <?= $row['해결여부'] == '2' ? 'selected' : '' ?>>접수완료</option>
                            <option value="3" <?= $row['해결여부'] == '3' ? 'selected' : '' ?>>AS예정</option>
                            <option value="4" <?= $row['해결여부'] == '4' ? 'selected' : '' ?>>AS완료</option>
                            <option value="5" <?= $row['해결여부'] == '5' ? 'selected' : '' ?>>AS취소</option>
                        </select>
                    </td>
                   <td>
					<? if ($row['생성시각']==$row['처리시각']) {
						echo "-";
					}else{
						echo htmlspecialchars($row['처리시각']);
					}
					?></td> <!-- 처리시각 출력 -->
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="11">등록된 장애 이력이 없습니다.</td>
            </tr>
        <?php endif; ?>
    </table>
    <br>
    <button type="submit">수정완료</button>
</form>
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	