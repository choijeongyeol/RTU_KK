    <? 
 
	   $result_setting = getRTU_setting_state('1'); // 창고앵글 일괄삭제  set_id 값 1	 
	   
 // js 그래프에 데이터 적용할 배열
$out_labels = [];
$out_data   = []; 
	?>	
<style>body{ background:#fff}</style>

	<!-- 게시판 리스트 계산 start -->
	<?

	// 제품 history 목록 가져오기
	if ($_GET['out_graph']=="30") {
		$out_stock_history = get_out_Stock_history_day_cnt30();
	}else{
		$out_stock_history = get_out_Stock_history_day_cnt7();
	}

		if ($out_stock_history) {
			
			foreach ($out_stock_history as $out_stock_historyItem) {
				//echo "['{$out_stock_historyItem['input_day']}', {$out_stock_historyItem['total_sum']},";
				
				array_push($out_labels,"{$out_stock_historyItem['input_day']}");
				array_push($out_data,"{$out_stock_historyItem['total_sum']}");
			}
		}
	?>
	
	<!-- 게시판 리스트 계산 end -->		
	
	<script>
		function sendit(){
			var out_selectBox = document.getElementById('out_select_box');
			var out_selectV = out_selectBox.value;
			var selectBox = document.getElementById('graph_box');
			var selectV = selectBox.value;			
			location.href='/gu/home/dashboard.php?out_graph='+out_selectV+'&graph='+selectV;
		}	
	</script>
	

		<!-- page content -->
        <div class="center_col home_board50" role="main" >
          <div class="">
 
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 "> 
                <div class="x_panel">
                  <div class="x_title" style="display:flex">
                    <div style="width:70%"><h2><select id="out_select_box" name="out_select_box" onchange="sendit()" style="border-color:#bebebe;font-size:15px;padding:5px">
						<option value="7" <? if ($_GET['out_graph']!="30") { echo "selected"; } ?> >출고현황 (최근  7일)</option>
						<option value="30" <? if ($_GET['out_graph']=="30") { echo "selected"; } ?> >출고현황 (최근 30일)</option>
                    </select>
					<!-- 최근 7일 입고현황  --><small></small></h2></div>
                    <div class="clearfix" style="width:30%;text-align:right"><a href="/gu/m07/list.php?h_loc_code=m06&h_location=입출고관리">more▷</a></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<!-- <p class="text-muted font-13 m-b-30">
								  관리창고목록을 조회하실 수 있습니다.
								</p> -->
								
								 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

								<div id="out_chart_box" style="width:100%;height:40%;">
									<canvas id="out_myChart"></canvas>
								</div>

									<script>
										// PHP에서 생성한 데이터를 JavaScript로 가져옴
										var out_labels = <?php echo json_encode($out_labels); ?>;
										var out_data = <?php echo json_encode($out_data); ?>;
										
										// Chart.js를 사용하여 그래프 그리기
										var ctx = document.getElementById('out_myChart').getContext('2d');
										var out_myChart = new Chart(ctx, {
											type: 'bar',
											data: {
												labels: out_labels,
												datasets: [{
													label: document.getElementById('out_select_box').value+' Days Dataset',
													data: out_data,
													backgroundColor: [
														'rgba(255, 99, 132, 0.2)',
														'rgba(54, 162, 235, 0.2)',
														'rgba(255, 206, 86, 0.2)',
														'rgba(75, 192, 192, 0.2)',
														'rgba(153, 102, 255, 0.2)',
														'rgba(255, 206, 86, 0.2)',
														'rgba(255, 159, 64, 0.2)'
													],
													borderColor: [
														'rgba(255, 99, 132, 1)',
														'rgba(54, 162, 235, 1)',
														'rgba(255, 206, 86, 1)',
														'rgba(75, 192, 192, 1)',
														'rgba(153, 102, 255, 1)',
														'rgba(255, 206, 86, 1)',
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
	 

							</div>
						 </div><!--  class="col-sm-12" -->
					  </div><!--  class="row" -->
				   </div><!--  class="x_content" -->
                </div><!-- x_panel -->
              </div><!-- class="col-md-12 col-sm-12 -->
			</div><!--  class="row" -->
		  </div><!--  class="" -->
	    </div><!--  class="right_col" role="main"> -->		  
        <!-- /page content -->
 