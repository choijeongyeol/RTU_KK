<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

$user_id = $_GET['user_id'] ?? '';
$month = $_GET['month'] ?? date('Y-m');
$cid = $_GET['cid'] ?? '';
$cid_list = [];
$total_output = 0;
$average_output = 0;
$total_minutes = 0;
$facility_info = [];

// 이전 월과 다음 월 계산
$prev_month = date('Y-m', strtotime($month . ' -1 month'));
$next_month = date('Y-m', strtotime($month . ' +1 month'));

// PDO 에러모드 설정
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 사용자 ID가 입력된 경우 CID 목록을 가져옴
if ($user_id) {
    $cid_list = get_cid_list_by_user($user_id);

    if (!$cid && count($cid_list) == 1) {
        $cid = $cid_list[0]['cid'];
        $statistics = api_monthly_statistics($cid, $month);
    } elseif (!$cid && count($cid_list) > 1) {
        $cid = $cid_list[0]['cid'];
        $statistics = api_monthly_statistics($cid, $month);
    } elseif ($cid && $month) {
        $statistics = api_monthly_statistics($cid, $month);
    }

    // 토탈정보 계산
    $total_output = getTotalMonthlyGeneration($cid, $month) / 1000;  // KW로 변환
    $average_output = getAverageMonthlyGeneration($cid, $month) / 1000;  // KWh로 변환
    $total_minutes = getTotalGenerationTimeMonthly($cid, $month);

    // 발전소 및 LoRa 정보 가져오기
    $facility_info = getFacilityAndLoraInfo($cid);
}

function get_cid_list_by_user($user_id) {
    global $conn;
    try {
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
    } catch (PDOException $e) {
        echo "CID 목록을 가져오는 중 오류 발생: " . $e->getMessage();
        return [];
    }
}

function api_monthly_statistics($cid, $month) {
    global $conn;
    try {
        $max_output_sql = "SELECT inverter_capacity * 1000 AS max_output FROM RTU_facility WHERE cid = :cid";
        $max_output_stmt = $conn->prepare($max_output_sql);
        $max_output_stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $max_output_stmt->execute();
        $max_output_row = $max_output_stmt->fetch(PDO::FETCH_ASSOC);
        $max_output = $max_output_row['max_output'] ?? 0;

        $sql = "
				SELECT 
					DATE(hourly_data.date) AS date,
					SUM(hourly_data.hourly_output) / 1000 AS total_output, 
					ROUND((SUM(hourly_data.hourly_output) / :max_output) * 100, 2) AS avg_efficiency,  
					MAX(daily_data.generation_minutes) AS generation_minutes,
					MIN(daily_data.inverter_status) AS inverter_status
				FROM (
					SELECT 
						DATE(rdate) AS date,
						HOUR(rdate) AS hour,
						MAX(cumulative_energy) - MIN(cumulative_energy) AS hourly_output,
						SUM(fault_status) AS fault_status
					FROM RTU_SolarInputData
					WHERE cid = :cid
					  AND DATE_FORMAT(rdate, '%Y-%m') = :month
					  AND cumulative_energy IS NOT NULL
					  AND cumulative_energy > 0
					GROUP BY DATE(rdate), HOUR(rdate)
					HAVING hourly_output > 0
				) AS hourly_data
				JOIN (
					SELECT 
						DATE(rdate) AS date,
						TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)) AS generation_minutes,
						IF(SUM(fault_status) > 10, '0', '1') AS inverter_status
					FROM RTU_SolarInputData
					WHERE cid = :cid
					  AND DATE_FORMAT(rdate, '%Y-%m') = :month
					  AND cumulative_energy IS NOT NULL
					  AND cumulative_energy > 0
					GROUP BY DATE(rdate)
					HAVING MAX(cumulative_energy) - MIN(cumulative_energy) > 0
				) AS daily_data ON hourly_data.date = daily_data.date
				GROUP BY DATE(hourly_data.date)
				ORDER BY date;
        ";
		//echo $sql;
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt->bindParam(':max_output', $max_output, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "월별 통계 데이터를 가져오는 중 오류 발생: " . $e->getMessage();
        return [];
    }
}

// 월별 총 발전량 계산 함수
function getTotalMonthlyGeneration($cid, $month) {
    global $conn;
    try {
        $sql = "
            SELECT SUM(daily_output) AS total_generation
            FROM (
                SELECT 
                    DATE(rdate) AS date,
                    MAX(cumulative_energy) - MIN(cumulative_energy) AS daily_output
                FROM RTU_SolarInputData
                WHERE cid = :cid AND DATE_FORMAT(rdate, '%Y-%m') = :month
                GROUP BY DATE(rdate)
                HAVING daily_output > 0
            ) AS daily_data
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_generation'] ?? 0;
    } catch (PDOException $e) {
        echo "총 발전량 계산 중 오류 발생: " . $e->getMessage();
        return 0;
    }
}

// 월별 평균 발전량 계산 함수
function getAverageMonthlyGeneration($cid, $month) {
    global $conn;
    try {
        $sql = "
            SELECT AVG(daily_output) AS avg_generation
            FROM (
                SELECT 
                    DATE(rdate) AS date,
                    MAX(cumulative_energy) - MIN(cumulative_energy) AS daily_output
                FROM RTU_SolarInputData
                WHERE cid = :cid AND DATE_FORMAT(rdate, '%Y-%m') = :month
                GROUP BY DATE(rdate)
                HAVING daily_output > 0
            ) AS daily_data
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['avg_generation'] ?? 0;
    } catch (PDOException $e) {
        echo "평균 발전량 계산 중 오류 발생: " . $e->getMessage();
        return 0;
    }
}

// 총 발전 시간 계산 함수
function getTotalGenerationTimeMonthly($cid, $month) {
    global $conn;
    try {
        $sql = "
            SELECT 
                TIMESTAMPDIFF(MINUTE, MIN(rdate), MAX(rdate)) AS generation_minutes
            FROM RTU_SolarInputData
            WHERE cid = :cid AND DATE_FORMAT(rdate, '%Y-%m') = :month AND pv_output > 0
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['generation_minutes'] ?? 0;
    } catch (PDOException $e) {
        echo "총 발전 시간 계산 중 오류 발생: " . $e->getMessage();
        return 0;
    }
}

// 특정 cid에 대한 발전소 및 LoRa 정보를 가져오는 함수
function getFacilityAndLoraInfo($cid) {
    global $conn;
    try {
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
    } catch (PDOException $e) {
        echo "발전소 및 LoRa 정보를 가져오는 중 오류 발생: " . $e->getMessage();
        return [];
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>월별 통계 분석</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { color: #4CAF50; }
        .container { max-width: 800px; margin: auto; }
        .search-box { margin-bottom: 20px; }
        label { font-weight: bold; }
        input[type="text"], input[type="month"], select, button {
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
        <h2>월별 통계 분석</h2>
        <form class="search-box" method="get" action="monthly_statistics.php">
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

            <label for="month">월:</label>
            <input type="month" id="month" name="month" value="<?= htmlspecialchars($month) ?>" required>
            <button type="submit">검색</button>
        </form>

        <?php if (!empty($statistics)) : ?>
            <div class="totals">
                <h3>토탈정보</h3>
                <p>- 총 발전량: <?= $total_output ?> KW</p>
                <p>- 일일 발전량: <?= $average_output ?> KWh</p>
                <p>- 총 발전 시간: 
                    <?php 
                        $hours = floor($total_minutes / 60);
                        $minutes = $total_minutes % 60;
                        echo ($hours > 0 ? $hours . '시간 ' : '') . ($minutes > 0 ? $minutes . '분' : '시간 없음');
                    ?>
                </p>				
				
            </div>
            
            <div class="date-navigation">
                <a href="?user_id=<?= htmlspecialchars($user_id) ?>&cid=<?= htmlspecialchars($cid) ?>&month=<?= $prev_month ?>">이전달</a>
                <span>조회날짜: <?= htmlspecialchars($month) ?></span>
                <a href="?user_id=<?= htmlspecialchars($user_id) ?>&cid=<?= htmlspecialchars($cid) ?>&month=<?= $next_month ?>">다음달</a>
            </div>            
            
            <h3>상세정보</h3>
            <table>
                <thead>
                    <tr>
                        <th>날짜</th>
                        <th>발전량 (Wh)</th>
                        <th>발전시간 (시간)</th>
                        <th>발전효율 (%)</th>
                        <th>상태</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statistics as $row) : ?>
                        <?php
                            $gen_hours = floor($row['generation_minutes'] / 60);
                            $gen_minutes = $row['generation_minutes'] % 60;
                        ?>					
                        <tr>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= number_format(htmlspecialchars($row['total_output']) * 1000) ?></td>
                            <!-- <td><?= htmlspecialchars($row['generation_hours']) ?></td> -->
							<td><?= $gen_hours ?>시간 <?= $gen_minutes ?>분</td>
                            <td><?= round($row['avg_efficiency'], 2) ?>%</td>
                            <td class="<?= $row['inverter_status'] == '1' ? 'status' : 'status-fault' ?>">
                                <?= $row['inverter_status'] == '1' ? '정상' : '비정상' ?>
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
