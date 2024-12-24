<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>
	<!-- 게시판 리스트 계산 start -->
	<?
	$list_condition = "wms_in_stock_history";
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/search.php'); ?>			
	<!-- 게시판 리스트 계산 end -->
	


<?

	// 제품 history 목록 가져오기
	$in_stock_history = get_in_Stock_history_day_cnt7();
	
	$arr_list = "";

			if ($in_stock_history) {
				foreach ($in_stock_history as $in_stock_historyItem) {
					//echo "['{$in_stock_historyItem['input_day']}', {$in_stock_historyItem['total_sum']},";
					$arr_list = $arr_list."['{$in_stock_historyItem['input_day']}', {$in_stock_historyItem['total_sum']}],";
				}
			}
			
			  //echo $arr_list;


?>




 
    <!-- Google Charts API 로드 -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Google Charts API 로드
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // PHP 코드로 생성한 데이터 가져오기
            var data = google.visualization.arrayToDataTable([
                ['일자', '입고수량'],
				
                <?php
                // PHP 코드로 주간 입고수량 데이터 생성
                //$dates = array("2024-02-24", "2024-02-25", "2024-02-26", "2024-02-27", "2024-02-28", "2024-02-29", "2024-03-01");

				// 현재 날짜와 시간 설정
				$current_date = strtotime('today');

				// 일주일 전의 날짜 설정
				$week_ago_date = strtotime('-1 week', $current_date);

				// 일주일 간의 날짜 배열 생성
				$dates = array();
				for ($i = $week_ago_date; $i < $current_date; $i += 86400) { // 하루는 86400초입니다.
					$dates[] = date('Y-m-d', $i);
				}
 				
				
                $quantities = array();
                foreach ($dates as $date) {
					
                    $quantities[] = rand(50, 200); // 입고수량은 50에서 200 사이의 랜덤한 값으로 설정합니다.
                }


                // 데이터 출력
                foreach ($dates as $key => $date) {
                  //  echo "['{$date}', {$quantities[$key]}],";
                }
				
			      echo $arr_list;
				
                ?>
            ]);

            // 그래프 옵션 설정
            var options = {
                title: '최근 7일간 입고수량 변화',
                curveType: 'function',
                legend: { position: 'bottom' }
            };

            // 그래프 생성
            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            // 그래프 그리기
            chart.draw(data, options);
        }
    </script>
<!-- </head>
<body>
 -->
	
<?php


// 현재 날짜와 시간 설정
$current_date = strtotime('today');   //echo $current_date;  1709564400 //  echo date('Y-m-d', 1709564400);  // 2024-03-05  //  echo strtotime('2024-03-05');



// 일주일 전의 날짜 설정
$week_ago_date = strtotime('-1 week', $current_date);

// 일주일 간의 날짜 배열 생성
$dates = array();
for ($i = $week_ago_date; $i < $current_date; $i += 86400) { // 하루는 86400초입니다.
    $dates[] = date('Y-m-d', $i);
}
 
 

// 데이터 출력   ['2024-02-27', 79],['2024-02-28', 186],['2024-02-29', 191],['2024-03-01', 100],['2024-03-02', 67],['2024-03-03', 148],['2024-03-04', 187],
foreach ($dates as $key => $date) {
	// echo "['{$date}', {$quantities[$key]}],";
	
}

 
?>	
 
    <!-- 그래프를 표시할 요소 -->
    <div id="curve_chart" style="width: 900px; height: 500px"></div>
	
	
	
 
	
</body>
</html>
