<?php
 
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

try {
    // PDO 객체 생성
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 데이터 조회 쿼리
    $sql = "SELECT hour, max_generation, min_generation, avg_generation, target_generation 
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
    <title>지역 내 발전량 비교</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="generationChart" width="800" height="400"></canvas>
    <script>
        // PHP 데이터를 JavaScript로 전달
        const data = <?php echo json_encode($data); ?>;

        // 데이터 가공
        const labels = data.map(item => item.hour + "시");
        const maxData = data.map(item => item.max_generation);
        const minData = data.map(item => item.min_generation);
        const avgData = data.map(item => item.avg_generation);
        const targetData = data.map(item => item.target_generation);

        // Chart.js 그래프 그리기
        const ctx = document.getElementById('generationChart').getContext('2d');
        const generationChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Max',
                        data: maxData,
                        borderColor: 'blue',
                        fill: false,
                    },
                    {
                        label: 'Min',
                        data: minData,
                        borderColor: 'orange',
                        fill: false,
                    },
                    {
                        label: 'Average',
                        data: avgData,
                        borderColor: 'gray',
                        fill: false,
                    },
                    {
                        label: 'Target',
                        data: targetData,
                        borderColor: 'yellow',
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: '지역 내 발전량 비교'
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
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
