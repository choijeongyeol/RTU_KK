<?php
// db_connection.php 파일을 include하여 데이터베이스 연결
require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php'); 

// 초기화면에는 최근 15일을 설정
$end_date = date('Y-m-d'); // 오늘 날짜
$start_date = date('Y-m-d', strtotime('-15 days', strtotime($end_date))); // 오늘로부터 15일 전 날짜

try {
    // 시작 날짜부터 종료 날짜까지의 날짜와 해당 날짜의 입고량을 쿼리로 가져옴
    $query = "SELECT DATE(rdate) AS date, COALESCE(SUM(quantity), 0) AS total_quantity "; // COALESCE 함수를 사용하여 입고량이 없는 경우 0으로 반환
    $query .= "FROM wms_in_stock_history "; 
    $query .= "WHERE DATE(rdate) BETWEEN :start_date AND :end_date "; 
    $query .= "GROUP BY DATE(rdate)";
    $statement = $conn->prepare($query);
    $statement->execute(array(':start_date' => $start_date, ':end_date' => $end_date));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    // 결과를 JSON 형식으로 반환
    $response = array();
    $dates = array();
    $quantities = array();
    foreach ($result as $row) {
        $dates[] = $row['date'];
        $quantities[] = (int)$row['total_quantity'];
    }
    $response['dates'] = $dates;
    $response['quantities'] = $quantities;
    //echo json_encode($response);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMS 입고량 변화추이</title>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Chart Container */
        #chart-container {
            width: 800px;
            margin: 20px auto;
        }
    </style>
</head>

<body>
    <!-- Date Range Selection -->
    <form id="dateRangeForm">
        <label for="start_date">시작 날짜:</label>
        <input type="date" id="start_date" name="start_date" value="<?= $start_date ?>"> <!-- 초기화면에 최근 15일로 설정 -->
        <label for="end_date">종료 날짜:</label>
        <input type="date" id="end_date" name="end_date" value="<?= $end_date ?>">
        <button type="submit">변경</button>
    </form>

    <!-- Chart Container -->
    <div id="chart-container">
        <canvas id="line-chart"></canvas>
    </div>

    <!-- JavaScript -->
    <script>
        // Fetch Chart Data Function
        function fetchChartData(startDate, endDate) {
            // AJAX Request to fetch data from fetch_data.php
            fetch('fetch_data.php?start_date=' + startDate + '&end_date=' + endDate)
                .then(response => response.json())
                .then(data => {
                    // Call function to render chart
                    renderChart(data);
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Render Chart Function
        function renderChart(data) {
            var ctx = document.getElementById('line-chart').getContext('2d');
            if (window.myChart != null) {
                window.myChart.destroy();
            }
            window.myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.dates, // Dates from fetched data
                    datasets: [{
                        label: '입고량',
                        data: data.quantities, // Quantities from fetched data
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: 'day' // Change the unit to 'day'
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }

        // Date Range Form submit event listener
        document.getElementById('dateRangeForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission
            var startDate = document.getElementById('start_date').value; // Get start date value
            var endDate = document.getElementById('end_date').value; // Get end date value
            fetchChartData(startDate, endDate); // Fetch chart data using selected date range
        });

        // Fetch initial chart data when page loads
        fetchChartData('<?= $start_date ?>', '<?= $end_date ?>');
    </script>
</body>

</html>
