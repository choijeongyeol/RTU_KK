<?php  session_start();
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// AS 요청 목록 조회 (RTU_Issue_History_New 테이블과 조인하여 status 가져오기)
try {
    $sql = "
        SELECT 
            ar.as_id, 
            ar.issue_id, 
            ar.as_num, 
            ar.request_date, 
            ih.status AS issue_status,  -- RTU_Issue_History_New 테이블의 status
            ar.technician_id, 
            ar.reservation_date, 
            ar.completion_date, 
            ar.notes, 
            ar.created_at, 
            ar.updated_at,
            ar.as_status,
            ar.saved_file_name -- 첨부파일 유무 확인을 위한 필드 추가
        FROM RTU_AS_Request ar
        JOIN RTU_Issue_History_New ih ON ar.issue_id = ih.id
		JOIN RTU_user u ON ih.user_idx = u.user_idx
		WHERE u.partner_id = '".$_SESSION['partner_id']."'
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $as_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}

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

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>AS 접수 목록 - 관리자 모드</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: center; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .input-box { width: 100%; }
        .submit-button { margin-top: 20px; padding: 10px 20px; }
        .date-input { width: 90%; } /* 날짜 입력 필드 크기 조정 */
        .date-icon { font-size: 0.8em; cursor: pointer; } /* 아이콘 크기 조정 */
    </style>
    <script>
        function openPopup(asId) {
            window.open('as_detail_view.php?as_id=' + asId, 'asDetailPopup1', 'width=600,height=800,scrollbars=yes');
        }
        function openPopup_memo(asId) {
            window.open('as_detail_memo.php?as_id=' + asId, 'asDetailPopup2', 'width=600,height=800,scrollbars=yes');
        }
    </script>    
</head>
<body>

<h2>AS 접수 목록 - 관리자 모드</h2>

<form action="as_request_update.php" method="post">
    <table>
        <tr>
            <th>AS ID</th>
            <th>Issue ID</th>
            <th>AS 번호</th>
            <th>이슈 상태</th>
            <th>담당자</th>
            <th>접수 날짜</th>
            <th>AS예정 날짜</th>
            <th>AS완료 날짜</th>
            <th>첨부파일</th> <!-- 첨부파일 항목 추가 -->
            <th>고객메모</th>
            <th>AS 상태</th>
            <th>AS기사 수리메모</th>
            <th>현황/내역</th>
        </tr>
        
        <?php foreach ($as_requests as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['as_id']) ?></td>
                <td><?= htmlspecialchars($row['issue_id']) ?></td>
                <td><?= htmlspecialchars($row['as_num']) ?></td>
                <td>
                    <select name="issue_status[<?= $row['as_id'] ?>]" class="input-box">
                        <option value="1" <?= $row['issue_status'] == 1 ? 'selected' : '' ?>>미접수</option>
                        <option value="2" <?= $row['issue_status'] == 2 ? 'selected' : '' ?>>접수완료</option>
                        <option value="3" <?= $row['issue_status'] == 3 ? 'selected' : '' ?>>AS예정</option>
                        <option value="4" <?= $row['issue_status'] == 4 ? 'selected' : '' ?>>AS완료</option>
                        <option value="5" <?= $row['issue_status'] == 5 ? 'selected' : '' ?>>AS취소</option>
                    </select>
                </td>
                <td>
                    <select name="technician[<?= $row['as_id'] ?>]" class="input-box">
                        <option value="">선택하세요</option>
                        <?php foreach ($technicians as $technician): ?>
                            <option value="<?= htmlspecialchars($technician['technician_id']) ?>"
                                <?= $technician['technician_id'] == $row['technician_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($technician['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><?= htmlspecialchars($row['request_date']) ?></td>
                <td>
                    <input type="datetime-local" name="reservation_date[<?= $row['as_id'] ?>]" 
                           value="<?= !empty($row['reservation_date']) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($row['reservation_date']))) : '' ?>" 
                           class="date-input">
                </td>
                <td><?= htmlspecialchars($row['completion_date']) ?></td>
                <td><?= !empty($row['saved_file_name']) ? 'Y' : 'N' ?></td> <!-- 첨부파일 유무 표시 -->
                <td><?= htmlspecialchars($row['notes']) ?></td>
                <td><?php
                switch ($row['as_status']) {
                    case 1:
                        echo "미접수";
                        break;
                    case 2:
                        echo "접수완료";
                        break;
                    case 3:
                        echo "AS예정";
                        break;
                    case 4:
                        echo "AS완료";
                        break;
                    case 5:
                        echo "AS취소";
                        break;
                    default:
                        echo "알 수 없음";
                }
                ?></td>
                <td><button type="button" onclick="openPopup_memo(<?= $row['as_id'] ?>)">관리</button></td>
                <td><button type="button" onclick="openPopup(<?= $row['as_id'] ?>)">보기</button></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <button type="submit" class="submit-button">수정완료</button>
</form>

</body>
</html>
