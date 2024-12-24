<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP 선 그래프 예제</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myChart" width="400" height="400"></canvas>

    <?php
    // PHP 코드로 데이터 생성
    $labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
    $data = [65, 59, 80, 81, 56, 55, 40]; // 예제 데이터
    ?>

    <script>
        // PHP에서 생성한 데이터를 JavaScript로 가져옴
        var labels = <?php echo json_encode($labels); ?>;
        var data = <?php echo json_encode($data); ?>;
        
        // Chart.js를 사용하여 그래프 그리기
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'My First Dataset',
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });
    </script>
</body>
</html>
