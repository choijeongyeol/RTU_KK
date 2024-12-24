<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>발전소 통계 분석</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
        }
        h1, h2 {
            text-align: center;
        }
        .section {
            margin: 20px 0;
        }
        .chart-container {
            position: relative;
            width: 100%;
            height: 300px;
        }
        .stats-table, .stats-table th, .stats-table td {
            width: 100%;
            border: 1px solid #ddd;
            border-collapse: collapse;
            padding: 10px;
            text-align: center;
        }
        .stats-table th {
            background-color: #f4f4f4;
        }
        button {
            padding: 8px 16px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h1>에너지 통계 분석</h1>
    
    <!-- 날짜 선택 -->
    <div class="section">
        <label>날짜 선택: </label>
        <input type="date" id="start_date" value="2024-10-15">
        <input type="date" id="end_date" value="2024-10-22">
    </div>

    <!-- 발전소 선택 -->
    <div class="section">
        <label>발전소 선택 (LoRa ID): </label>
        <select id="lora_id">
            <option value="d02544fffef3b13f">발전소 1</option>
            <option value="d02544fffef3b162">발전소 2</option>
        </select>
        <button id="get_stats">통계 보기</button>
    </div>

    <!-- 차트 -->
    <div class="section chart-container">
        <canvas id="myChart"></canvas>
    </div>

    <!-- 통계 정보 -->
    <div class="section">
        <h2>통계 정보</h2>
        <p>오늘(해당 기간) 평균 발전시간: <span id="avg_time">0000</span> 시간</p>
        <p>오늘(해당 기간) 평균 발전량: <span id="avg_energy">0000</span> 단위</p>
        <p>오늘(해당 기간) 누적 발전시간: <span id="total_time">0000</span> 시간</p>
        <p>오늘(해당 기간) 누적 발전량: <span id="total_energy">0000</span> 단위</p>
    </div>

    <!-- 시간대별 발전 상태 -->
    <div class="section">
        <table class="stats-table">
            <thead>
                <tr>
                    <th>시간</th>
                    <th>상태</th>
                    <th>발전시간</th>
                    <th>발전량</th>
                    <th>발전효율</th>
                </tr>
            </thead>
            <tbody id="stats_table">
                <tr>
                    <td>00시</td>
                    <td>상태</td>
                    <td>00시간</td>
                    <td>00단위</td>
                    <td>00%</td>
                </tr>
                <!-- 추가 데이터 행 -->
            </tbody>
        </table>
    </div>

    <script>
        // 차트 생성 함수
        function createChart(labels, data) {
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels, // x축 레이블
                    datasets: [{
                        label: '발전량',
                        data: data, // 발전량 데이터
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // 버튼 클릭 이벤트
        $('#get_stats').on('click', function() {
            var lora_id = $('#lora_id').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            // Ajax 요청을 통해 데이터를 가져옴
            $.ajax({
                url: 'http://your_api_url',  // 실제 API 경로 입력
                type: 'GET',
                data: {
                    lora_id: lora_id,
                    start_date: start_date,
                    end_date: end_date
                },
                success: function(response) {
                    var data = response.data;
                    var labels = [];  // 시간대 (X축)
                    var energyData = [];  // 발전량 데이터 (Y축)
                    var avgTime = 0;  // 평균 발전시간
                    var avgEnergy = 0;  // 평균 발전량

                    // 데이터를 차트와 테이블에 연결
                    $('#stats_table').empty();  // 테이블 초기화
                    data.forEach(function(item) {
                        labels.push(item.time);
                        energyData.push(item.energy);

                        // 시간대별 테이블 업데이트
                        $('#stats_table').append(`
                            <tr>
                                <td>${item.time}</td>
                                <td>${item.status}</td>
                                <td>${item.generation_time}시간</td>
                                <td>${item.energy}단위</td>
                                <td>${item.efficiency}%</td>
                            </tr>
                        `);
                        
                        // 평균값 계산
                        avgTime += item.generation_time;
                        avgEnergy += item.energy;
                    });

                    // 평균값 표시
                    $('#avg_time').text((avgTime / data.length).toFixed(2));
                    $('#avg_energy').text((avgEnergy / data.length).toFixed(2));

                    // 차트 생성
                    createChart(labels, energyData);
                }
            });
        });
    </script>

</body>
</html>
