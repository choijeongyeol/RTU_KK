        <!-- page content -->
        <div class="center_col home_board50" role="main" >
          <div class="">
 
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>테스트 > 테스트목록 <small></small></h2>
                    <div class="clearfix">more▷</div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<!-- <p class="text-muted font-13 m-b-30">
								  관리창고목록을 조회하실 수 있습니다.
								</p> -->

				<?php
				// 현재 재고 상태 가져오기
				$stock = getStock($start_record_number,$itemsPerPage);
				?>				
 
				<table id="tb_border" class="dataCustomTable" >
					<thead>
						<tr>
							<th>NO</th>
							<th>제품명</th>
							<th>창고명</th>
							<th>앵글명</th>
							<th>수량</th>
							<th>업데이트</th>
							<!-- <th>등록자</th>
							<th>IP</th> -->
							<th>관리</th>
						</tr>
					</thead>
					<tbody>
					<?
 
						if ($stock) {
							foreach ($stock as $stockItem) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$stockItem['item_name']}</td>";
								echo "<td>{$stockItem['warehouse_name']}</td>";
								echo "<td>{$stockItem['angle_name']}</td>";
								echo "<td>".number_format("{$stockItem['quantity']}")."</td>";
								echo "<td>{$stockItem['item_rdate']}</td>";
								//echo "<td>관리자</td>";
								//echo "<td>214.33.***.***</td>";
								if ($stockItem['warehouse_id_null'] == "/") {
								echo "<td><a class='btn gray wide' onclick=popup_win('stock_reg',{$stockItem['item_id']},{$stockItem['warehouse_id']},{$stockItem['quantity']}) style='cursor:pointer'>제품등록</a></td>";									
								}else{
									if ($pm_rst_U == 'F'){
										    echo "<td>권한없음</td>";
									}else{
										if ("{$stockItem['quantity']}"==0) {
											echo "<td> </td>";
										}else{
											echo "<td><button type='button' class='btn btn-secondary btn-sm' style='".$permission_U_button."padding:2;width:70px' value='상세보기'  onclick=popup_win_stock_move('stock_move',{$stockItem['item_id']},{$stockItem['angle_id']},{$stockItem['warehouse_id']},{$stockItem['quantity']}) style='".$permission_U_button."cursor:pointer'>제품이동</button></td>";										
										}										
									}


								}
								$desc_start_no = $desc_start_no - 1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td>등록된 재고제품 없음</td></tr>";
						}
					?>			
					</tbody>
				</table>
	 

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
 