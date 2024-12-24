<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/topmenu.php'); ?>
	<!-- 게시판 리스트 계산 start -->
	<?
	$list_condition = "wms_in_stock_history";
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/search.php'); ?>			
	<!-- 게시판 리스트 계산 end -->
	

<?php
// PHP 코드로 데이터 생성
$labels = [];
$data   = []; // 예제 데이터
?>

<?

	// 제품 history 목록 가져오기
	$in_stock_history = get_in_Stock_history_day_cnt30();

		if ($in_stock_history) {
			
			foreach ($in_stock_history as $in_stock_historyItem) {
				//echo "['{$in_stock_historyItem['input_day']}', {$in_stock_historyItem['total_sum']},";
				
				array_push($labels,"{$in_stock_historyItem['input_day']}");
				array_push($data,"{$in_stock_historyItem['total_sum']}");
			}
		}

?>

<style>body{ background:#fff}</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div id="chart_box" style="width:400px;height:500px">
    <canvas id="myChart"></canvas>
</div>

    <script>
        // PHP에서 생성한 데이터를 JavaScript로 가져옴
        var labels = <?php echo json_encode($labels); ?>;
        var data = <?php echo json_encode($data); ?>;
        
        // Chart.js를 사용하여 그래프 그리기
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'My First Dataset',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
 
    </script>
</body>
</html>
