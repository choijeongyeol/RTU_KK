<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

// SQL 쿼리 작성
$sql = "SELECT cid, business_year, business_type, user_name, install_confirm_date, install_confirm_num, last_access_date, last_reception_date, rtu_company, rtu_company_tel, communication_type, lora_id, multi, address, latitude, longitude, install_type, module_capacity, total_capacity, inverter_manufacturer, inverter_model, inverter_capacity 
        FROM RTU_facility
        ORDER BY cid DESC";  // 최근 등록된 순서대로 정렬

// 데이터 가져오기
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!-- HTML 테이블로 출력 -->
<table border="1" cellpadding="10" cellspacing="0">
    <thead>
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
                <tr>
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
