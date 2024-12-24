<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

try {
    // 데이터베이스 연결
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 장애 통계 데이터 조회
    $sql = "SELECT issue_type, percentage FROM RTU_IssueStatistics";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
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
    <title>장애 통계</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        /* canvas 크기 조정 */
        #issueChart {
            width: 500px;
            height: 500px;
            display: block;
            margin: 0 auto; /* 중앙 정렬 */
        }
    </style>	
</head>
<body>
    <canvas id="issueChart" ></canvas>
    <script>
        // PHP 데이터를 JavaScript로 전달
        const data = <?php echo json_encode($data); ?>;

        // 데이터 가공
        const labels = data.map(item => item.issue_type);
        const percentages = data.map(item => parseFloat(item.percentage));

        // 데이터 검증 (콘솔에서 확인 가능)
        console.log('Labels:', labels);
        console.log('Percentages:', percentages);

        // Chart.js 도넛 그래프 생성
        const ctx = document.getElementById('issueChart').getContext('2d');

        const issueChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: '장애 통계',
                    data: percentages,
                    backgroundColor: [
                        'blue', 'orange', 'green', 'yellow', 'gray',
                        'purple', 'lime', 'cyan', 'pink', 'red'
                    ],
                    borderColor: 'white',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: '장애 통계'
                    },
                    datalabels: {
                        color: 'white', // 텍스트 색상
                        formatter: (value, ctx) => {
                            // 데이터 합계 계산
                            const dataArr = ctx.chart.data.datasets[0].data;
                            const sum = dataArr.reduce((acc, cur) => acc + cur, 0);
                            console.log('Sum of data:', sum); // 디버깅
                            if (sum === 0) return '0%'; // 합계가 0인 경우 0%로 표시
                            const percentage = ((value / sum) * 100).toFixed(1) + '%';
                            return percentage;
                        },
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        align: 'center' // 텍스트를 중앙에 맞춤
                    }
                }
            },
            plugins: [ChartDataLabels] // datalabels 플러그인 활성화
        });
    </script>
</body>
</html>
