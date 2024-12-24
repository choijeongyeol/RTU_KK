<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

$user_id = $_GET['user_id'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$cid = $_GET['cid'] ?? '';
$cid_list = [];
$total_output = 0;
$average_output = 0;
$total_minutes = 0; // 분 단위로 총 발전 시간 저장
$facility_info = [];

// 이전 날짜와 다음 날짜 계산
$prev_date = date('Y-m-d', strtotime($date . ' -1 day'));
$next_date = date('Y-m-d', strtotime($date . ' +1 day'));

// 사용자 ID가 입력된 경우 CID 목록을 가져옴
if ($user_id) {
    $cid_list = get_cid_list_by_user($user_id);

    if (!$cid && count($cid_list) == 1) {
        $cid = $cid_list[0]['cid'];
        $statistics = api_daily_statistics($cid, $date);
    } elseif (!$cid && count($cid_list) > 1) {
        $cid = $cid_list[0]['cid'];
        $statistics = api_daily_statistics($cid, $date);
    } elseif ($cid && $date) {
        $statistics = api_daily_statistics($cid, $date);
    }

    // 토탈정보 계산
    $total_output = getTotalDailyGeneration($cid, $date) / 1000;  // KW로 변환
    $average_output = getAverageDailyGeneration($cid, $date) / 1000;  // KWh로 변환
    $total_minutes = getTotalGenerationTime($cid, $date);  // 총 발전 시간 (분 단위)

    // 발전소 및 LoRa 정보 가져오기
    $facility_info = getFacilityAndLoraInfo($cid);
}

function get_cid_list_by_user($user_id) {
    global $conn;
    $sql = "
        SELECT f.cid, 
               CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(l.powerstation, ' ', -2), ' ', 2), ' 발전소') AS short_powerstation 
        FROM RTU_facility f
        JOIN RTU_lora l ON f.lora_id = l.lora_id
        WHERE f.user_id = :user_id
        ORDER BY f.install_confirm_date ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function api_daily_statistics($cid, $date) {
    global $conn;

    // 시스템의 최대 출력 가능 발전량을 가져옴 (인버터 용량 기준)
    $max_output_sql = "SELECT inverter_capacity * 1000 AS max_output FROM RTU_facility WHERE cid = :cid";
    $max_output_stmt = $conn->prepare($max_output_sql);
    $max_output_stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $max_output_stmt->execute();
    $max_output_row = $max_output_stmt->fetch(PDO::FETCH_ASSOC);
    $max_output = $max_output_row['max_output'] ?? 0;

    $sql = "
        SELECT 
            HOUR(rdate) AS hour,
            CAST((MAX(cumulative_energy) - MIN(cumulative_energy)) AS UNSIGNED) / 1000 AS total_output,   
            ROUND(((MAX(cumulative_energy) - MIN(cumulative_energy)) / :max_output) * 100, 2) AS avg_efficiency,  
            IF(SUM(fault_status) > 0, '비정상', '정상') AS status
        FROM RTU_SolarInputData
        WHERE cid = :cid AND DATE(rdate) = :date
        GROUP BY HOUR(rdate)
        HAVING total_output > 0
        ORDER BY hour
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':max_output', $max_output, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 하루 총 발전량 계산 함수
function getTotalDailyGeneration($cid, $date) {
    global $conn;

    $sql = "
        SELECT SUM(hourly_output) AS total_generation
        FROM (
            SELECT 
                HOUR(rdate) AS hour,
                MAX(cumulative_energy) - MIN(cumulative_energy) AS hourly_output
            FROM RTU_SolarInputData
            WHERE cid = :cid AND DATE(rdate) = :date
            GROUP BY HOUR(rdate)
            HAVING hourly_output > 0
        ) AS hourly_data
    ";
	
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_generation'] ?? 0;
}

// 하루 평균 발전량 계산 함수 (cumulative_energy 사용)
function getAverageDailyGeneration($cid, $date) {
    global $conn;
 
    $sql = "
        SELECT AVG(hourly_output) AS avg_generation
        FROM (
            SELECT 
                HOUR(rdate) AS hour,
                MAX(cumulative_energy) - MIN(cumulative_energy) AS hourly_output
            FROM RTU_SolarInputData
            WHERE cid = :cid AND DATE(rdate) = :date
            GROUP BY HOUR(rdate)
            HAVING hourly_output > 0
        ) AS hourly_data
    ";
	
		
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['avg_generation'] ?? 0;
}

// 총 발전 시간 계산 함수 (분 단위로 계산)
function getTotalGenerationTime($cid, $date) {
    global $conn;

   // $sql = "
    //    SELECT COUNT(DISTINCT CONCAT(HOUR(rdate), ':', MINUTE(rdate))) AS generation_minutes
   //     FROM RTU_SolarInputData
   //     WHERE cid = :cid
    ////      AND DATE(rdate) = :date
   //       AND cumulative_energy IS NOT NULL
   //       AND cumulative_energy > 0	  
  //  "; 
	
	
	// TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)) AS generation_minutes,
      //  CONCAT(
      //      FLOOR(TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)) / 60), '시간 ',
      //      MOD(TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)), 60), '분'
      //  ) AS generation_minutes	
    $sql = "	
     SELECT 
          TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)) AS generation_minutes
    FROM 
        RTU_SolarInputData
    WHERE 
        cid = :cid
        AND DATE(rdate) = :date
        AND cumulative_energy IS NOT NULL
        AND cumulative_energy > 0		
    "; 
	
	
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['generation_minutes'] ?? 0;
}

// 발전소 및 LoRa 정보 가져오는 함수
function getFacilityAndLoraInfo($cid) {
    global $conn;
    $sql = "
        SELECT f.cid, f.user_id, f.install_confirm_date, l.lora_id, l.app_eui, l.powerstation 
        FROM RTU_facility f
        JOIN RTU_lora l ON f.lora_id = l.lora_id
        WHERE f.cid = :cid
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>일별 통계 분석</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { color: #4CAF50; }
        .container { max-width: 800px; margin: auto; }
        .search-box { margin-bottom: 20px; }
        label { font-weight: bold; }
        input[type="text"], input[type="date"], select, button {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        .status { color: green; font-weight: bold; }
        .status-fault { color: red; font-weight: bold; }
        .totals { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>일별 통계 분석</h2>
        <form class="search-box" method="get" action="daily_statistics.php">
            <label for="user_id">사용자 ID:</label>
            <input type="text" id="user_id" name="user_id" value="<?= htmlspecialchars($user_id) ?>" required>

            <?php if (!empty($cid_list)): ?>
                <label for="cid">CID:</label>
                <select id="cid" name="cid">
                    <?php foreach ($cid_list as $cid_option): ?>
                        <option value="<?= htmlspecialchars($cid_option['cid']) ?>" <?= $cid_option['cid'] == $cid ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cid_option['cid'] . ' - ' . $cid_option['short_powerstation']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <label for="date">날짜:</label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
            <button type="submit">검색</button>
        </form>

        <?php if (!empty($statistics)) : ?>
            <div class="totals">
                <h3>토탈정보</h3>
                <p>- 총 발전량: <?= $total_output ?> KW</p>
                <p>- 시간당 발전량: <?= $average_output ?> KWh</p>
                <p>- 총 발전 시간: 
                    <?php 
                        $hours = floor($total_minutes / 60);
                        $minutes = $total_minutes % 60;
                        echo ($hours > 0 ? $hours . '시간 ' : '') . ($minutes > 0 ? $minutes . '분' : '시간 없음');
                    ?>
                </p>
            </div>
            
            <div class="date-navigation">
                <a href="?user_id=<?= htmlspecialchars($user_id) ?>&cid=<?= htmlspecialchars($cid) ?>&date=<?= $prev_date ?>">이전날짜</a>
                <span>조회날짜: <?= htmlspecialchars($date) ?></span>
                <a href="?user_id=<?= htmlspecialchars($user_id) ?>&cid=<?= htmlspecialchars($cid) ?>&date=<?= $next_date ?>">다음날짜</a>
            </div>
            
            <h3>상세정보</h3>
            <table>
                <thead>
                    <tr>
                        <th>시간</th>
                        <th>발전량 (Wh)</th>
                        <th>발전효율 (%)</th>
                        <th>상태</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statistics as $row) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['hour']) ?>시 ~ <?= htmlspecialchars($row['hour']) ?>시 59분 59초</td>
                            <td><?= number_format(htmlspecialchars($row['total_output']) * 1000) ?></td>
                            <td><?= round($row['avg_efficiency'], 2) ?>%</td>
                            <td class="<?= $row['status'] == '정상' ? 'status' : 'status-fault' ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>검색 결과가 없습니다.</p>
        <?php endif; ?>
    </div>
</body>
</html>
