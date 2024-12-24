<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');

// 현재 페이지 가져오기 (디폴트 1)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 한 페이지에 표시할 데이터 수
$limit = 7;

// OFFSET 계산
$offset = ($page - 1) * $limit;

// 전체 데이터 개수 가져오기
$totalQuery = "SELECT COUNT(*) as total FROM RTU_facility f
JOIN RTU_user u ON f.user_id = u.user_id
WHERE u.delYN = 'N'";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->execute();
$totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
$total = $totalRow['total'];

// 총 페이지 수 계산
$totalPages = ceil($total / $limit);

// 데이터 가져오기 쿼리
$sql = "SELECT 
    f.cid as cid, 
    f.business_year as business_year, 
    f.business_type as business_type, 
    f.user_name as user_name, 
    f.install_confirm_date as install_confirm_date, 
    f.install_confirm_num as install_confirm_num, 
    f.last_access_date as last_access_date, 
    f.last_reception_date as last_reception_date, 
    f.rtu_company as rtu_company, 
    f.rtu_company_tel as rtu_company_tel, 
    f.communication_type as communication_type, 
    f.lora_id as lora_id, 
    f.multi as multi, 
    f.address as address, 
    f.latitude as latitude, 
    f.longitude as longitude, 
    f.install_type as install_type, 
    f.module_capacity as module_capacity, 
    f.total_capacity as total_capacity, 
    f.inverter_manufacturer as inverter_manufacturer, 
    f.inverter_model as inverter_model, 
    f.inverter_capacity as inverter_capacity
FROM RTU_facility f
JOIN RTU_user u ON f.user_id = u.user_id
WHERE u.delYN = 'N' and u.partner_id='".$_SESSION['partner_id']."' 
ORDER BY f.cid DESC
LIMIT :limit OFFSET :offset";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<style>
    /* 테이블 행 스타일 */
    table tr {
        transition: background-color 0.2s ease; /* 부드러운 색 전환 */
    }

    /* 마우스 오버 시 배경색 변경 */
    table tr:hover {
        background-color: #f0f8ff; /* 연한 파란색 */
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const rows = document.querySelectorAll("tbody tr");

        rows.forEach((row, index) => {
            row.addEventListener("mouseover", () => {
                // 홀수 번째 줄 (0 기반에서 짝수 인덱스)
                if (index % 2 === 0) {
                    row.style.backgroundColor = "#f0f8ff";
                    if (rows[index + 1]) {
                        rows[index + 1].style.backgroundColor = "#f0f8ff"; // 다음 줄 강조
                    }
                } 
                // 짝수 번째 줄 (0 기반에서 홀수 인덱스)
                else {
                    row.style.backgroundColor = "#f0f8ff";
                    if (rows[index - 1]) {
                        rows[index - 1].style.backgroundColor = "#f0f8ff"; // 이전 줄 강조
                    }
                }
            });

            row.addEventListener("mouseout", () => {
                // 홀수 번째 줄 (0 기반에서 짝수 인덱스)
                if (index % 2 === 0) {
                    row.style.backgroundColor = ""; // 원래 색상으로 복구
                    if (rows[index + 1]) {
                        rows[index + 1].style.backgroundColor = ""; // 다음 줄 복구
                    }
                } 
                // 짝수 번째 줄 (0 기반에서 홀수 인덱스)
                else {
                    row.style.backgroundColor = ""; // 원래 색상으로 복구
                    if (rows[index - 1]) {
                        rows[index - 1].style.backgroundColor = ""; // 이전 줄 복구
                    }
                }
            });
        });
    });
</script>


<center><h1>설비 목록</h1></center>
<!-- HTML 테이블로 출력 -->
<div style="width: 100%; height: 83vh; overflow: auto; border: 0px solid #ccc;">
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead style="position: sticky; top: 0; background: #f2f2f2; z-index: 100;">
            <tr>
                <th>CID</th>
                <th>사업 연도</th>
                <th>사업 구분</th>
                <th>사용자</th>
                <th>설치 확인일</th>
                <th>설치확인관리번호</th>
                <th>최종접속일시</th>
                <th>최종수신일시</th>
                <th>RTU 업체</th>
                <th>RTU 업체 연락처</th>
                <th>통신 방식</th>
            </tr>
            <tr>
                <th>LoRa ID</th>
                <th>Multi</th>
                <th>주소</th>
                <th>위도</th>
                <th>경도</th>
                <th>설치 유형</th>
                <th>모듈 용량 (W)</th>
                <th>총 용량 (W)</th>
                <th>인버터 제조사</th>
                <th>인버터 모델</th>
                <th>인버터 용량</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($facilities)): ?>
                <?php foreach ($facilities as $facility): ?>
                    <tr onclick="window.location.href='facility_form.php?cid=<?= htmlspecialchars($facility['cid']) ?>'" style="cursor: pointer;">
                        <td><?= htmlspecialchars($facility['cid']) ?></td>
                        <td><?= htmlspecialchars($facility['business_year']) ?></td>
                        <td><?= htmlspecialchars($facility['business_type']) ?></td>
                        <td><?= htmlspecialchars($facility['user_name']) ?></td>
                        <td><?= htmlspecialchars($facility['install_confirm_date']) ?></td>
                        <td><?= htmlspecialchars($facility['install_confirm_num']) ?></td>
                        <td><?= htmlspecialchars($facility['last_access_date']) ?></td>
                        <td><?= htmlspecialchars($facility['last_reception_date']) ?></td>
                        <td><?= htmlspecialchars($facility['rtu_company']) ?></td>
                        <td><?= htmlspecialchars($facility['rtu_company_tel']) ?></td>
                        <td><?= htmlspecialchars($facility['communication_type']) ?></td>
                    </tr>
                    <tr onclick="window.location.href='facility_form.php?cid=<?= htmlspecialchars($facility['cid']) ?>'" style="cursor: pointer;">
                        <td><?= htmlspecialchars($facility['lora_id']) ?></td>
                        <td><?= htmlspecialchars($facility['multi']) ?></td>
                        <td><?= htmlspecialchars($facility['address']) ?></td>
                        <td><?= htmlspecialchars($facility['latitude']) ?></td>
                        <td><?= htmlspecialchars($facility['longitude']) ?></td>
                        <td><?= htmlspecialchars($facility['install_type']) ?></td>
                        <td><?= htmlspecialchars($facility['module_capacity']) ?></td>
                        <td><?= htmlspecialchars($facility['total_capacity']) ?></td>
                        <td><?= htmlspecialchars($facility['inverter_manufacturer']) ?></td>
                        <td><?= htmlspecialchars($facility['inverter_model']) ?></td>
                        <td><?= htmlspecialchars($facility['inverter_capacity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="22">등록된 설비가 없습니다.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- 페이지 네비게이션 -->
<div style="margin-top: 20px; text-align: center;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" style="margin: 0 5px; <?= $i == $page ? 'font-weight: bold; color: red;' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
