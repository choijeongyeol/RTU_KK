<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

try {
    // 데이터베이스 연결
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 데이터 조회 쿼리
    $sql = "SELECT hour, avg_generation, target_generation 
            FROM RTU_RegionGeneration 
            WHERE region_name = :region_name
            ORDER BY hour ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['region_name' => 'Region1']);
    $data = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>발전량 비교</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="generationChart" width="800" height="400"></canvas>
    <script>
        // PHP 데이터를 JavaScript로 전달
        const data = <?php echo json_encode($data); ?>;

        // 데이터 가공
        const labels = data.map(item => item.hour + "시");
        const avgData = data.map(item => item.avg_generation);
        const targetData = data.map(item => item.target_generation);

        // Chart.js 그래프 그리기
        const ctx = document.getElementById('generationChart').getContext('2d');
        const generationChart = new Chart(ctx, {
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line', // 선형 그래프
                        label: '평균 발전량',
                        data: avgData,
                        borderColor: 'blue',
                        borderWidth: 2,
                        fill: false,
                        yAxisID: 'y',
                    },
                    {
                        type: 'bar', // 막대 그래프
                        label: '목표 발전량',
                        data: targetData,
                        backgroundColor: 'orange',
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: '발전량'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: '시간'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: '발전량 (kW)'
                        },
                        position: 'left', // 기본 Y축
                    }
                }
            }
        });
    </script>
</body>
</html>
