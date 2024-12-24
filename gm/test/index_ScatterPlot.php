<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP 산점도(Scatter Plot) 예제</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myChart" width="400" height="400"></canvas>

    <?php
    // PHP 코드로 데이터 생성
    // 예제로 랜덤한 10개의 데이터를 생성합니다.
    $data = array();
    for ($i = 0; $i < 10; $i++) {
        $data[] = array(rand(0, 100), rand(0, 100)); // X 좌표, Y 좌표
    }
    ?>

    <script>
        // PHP에서 생성한 데이터를 JavaScript로 가져옴
        var data = <?php echo json_encode($data); ?>;
        
        // Chart.js를 사용하여 산점도 그리기
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Scatter Plot',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom'
                    },
                    y: {
                        type: 'linear',
                        position: 'left'
                    }
                }
            }
        });
    </script>
</body>
</html>
