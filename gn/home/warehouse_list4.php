        <!-- page content -->
        <div class="center_col home_board50" role="main" >
          <div class="">
 
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>창고관리 > 창고목록 <small></small></h2>
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
				// 창고 목록 가져오기
				$warehouses = getwms_warehouses($start_record_number,2,$search,$SearchString);
				?>				
 
				<table id="tb_border" class="dataCustomTable" >
					<thead>
						<tr>
							<th width="8%">NO</th>
							<!-- <th>창고ID</th>
							<th>코드명</th> -->
							<th width="23%">창고</th>
							<!-- <th>앵글목록</th>
							<th>등록자</th> -->
							<th width="23%">앵글</th>
							<th width="22%">앵글조정</th>
							<th width="5%">제품수량</th>
							<th width="15%">제품목록</th>
						</tr>
					</thead>
					<tbody>
					<?  
                        $now_wid =""; $cg_wid = ""; $now_bg= "style='background:#d7deea'";$cg_count=0;
						if ($warehouses) {
							//$start_record_number = $start_record_number + 1;
							foreach ($warehouses as $warehouse) {
								
								if($cg_count==0){
									$now_bg= "style='background:#d7deea'";	
									$now_wid = "{$warehouse['warehouse_id']}"; 
								}else{
									$cg_wid = "{$warehouse['warehouse_id']}"; 
									if ($now_wid==$cg_wid) {
										if ($now_bg== "style='background:#d7deea'") {
											$now_bg = "style='background:#d7deea'";
										}else{
											$now_bg = "style='background:#fae6d6'";
										}										
									}else{
										if ($now_bg== "style='background:#d7deea'") {
											$now_bg = "style='background:#eee'";
										}else{
											$now_bg = "style='background:#d7deea'";
										}
									}	
								}
								
								echo "<tr ".$now_bg.">";					
								echo "<td>".$desc_start_no."</td>";
								//echo "<td>{$warehouse['warehouse_id']}</td>";
								//echo "<td>{$warehouse['warehouse_code']}</td>";  
 
								echo "<td><".$permission_U2_button."a onclick=popup_win_warehouse_update('warehouse_update','{$warehouse['warehouse_id']}',400,300) style='cursor:pointer'>{$warehouse['warehouse_name']}</a>";
 
								if ("{$warehouse['sum_quantity_warehouse']}"!="0"){ // 제품이 있는 경우, 창고삭제 안됨
									//echo " 못지움";
								}else{
									// 제품이 없는데...........
									
									if ($result_setting[0]['set_state']=="Y") {  // 창고,앵글 일괄삭제 Y 이면,
										echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_warehouse_del('warehouse_del',{$warehouse['warehouse_id']},400,300) style='".$permission_D2_button."cursor:pointer".$xhidden."'>";											
									}else{   // 창고,앵글 일괄삭제 N 이면,
										
										if ("{$warehouse['angle_cnt']}"==0) {
											echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_warehouse_del('warehouse_del',{$warehouse['warehouse_id']},400,300) style='".$permission_D2_button."cursor:pointer".$xhidden."'>";												
										}
									}									
								}
								
								echo "</td>";
								echo "<td><button type='button' class='btn btn-secondary btn-sm' style='".$permission_W_button."padding:2;width:70px' value='앵글삽입'  onclick=popup_win_size('angle',{$warehouse['warehouse_id']},400,400) style='".$permission_W_button."cursor:pointer'>삽입</button>".$permission_W_txt."</td>";

								echo "<td >  </td>";
								echo "<td >  </td>";
								echo "<td >  </td>";
								//$start_record_number = $start_record_number + 1;
							    echo "</tr>";
							
								$angle_lists = getwms_angle_namelist("{$warehouse['warehouse_id']}");
								if (!empty($angle_lists)) {	
										// 특정 데이터 1개 추출
										if (!empty($angle_lists)) {		
											$updown_i = 0;
											foreach($angle_lists as $row){
												echo "<tr  ".$now_bg." >";	// 앵글목록 start														
													//echo $row['angle_id'];  
													$angle_order = $row['angle_order'];

													echo "<td>ㄴ</td><td> </td>";
													
													
													echo "<td  style='font-weight:bold;text-align:center' >";
													
													
													echo "<".$permission_U_button."a onclick=popup_win_angle_update('angle_update',{$warehouse['warehouse_id']},".$row['angle_id'].",400,300)  style='cursor:pointer'><div style='width:100%;text-align:center'>".$row['angle_name']."</span></a>"; 
													

													if ($row['sum_quantity']==0) { // 앵글에 담긴 수량이 없으면, 삭제가능
														echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_angle_del('angle_del',{$warehouse['warehouse_id']},".$row['angle_id'].",400,300)  style='".$permission_D_button."cursor:pointer".$xhidden."'>";													
													}
													
													
													echo "</td><td>";	
						
													if ($updown_i==0) {
													echo "<button type='button' class='btn btn-secondary btn-sm'  style='background:#dedede;color:#636363;cursor: auto;' title='$angle_order' >up ▲</button>";											
													}else{
													echo "<button type='button' class='btn btn-secondary btn-sm'  style='background:#dedede;color:#636363' onclick=order_up_angle({$warehouse['warehouse_id']},".$row['angle_id'].",".$angle_order.")  title='$angle_order'>up ▲</button> ";
													}

													echo "<button type='button' class='btn btn-secondary btn-sm'  style='background:#dedede;color:#636363' onclick=order_down_angle('{$warehouse['warehouse_id']}','".$row['angle_id']."','".$angle_order."') >▼ down</button>";
													
													
													echo "</td><td>".number_format($row['sum_quantity']);	
													echo "</TD>";
													
													echo "<td ><button type='button' class='btn btn-secondary btn-sm' style='".$permission_W_button."padding:2;width:70px' value='상세보기'  onclick=popup_win_productlist_in_angle('productlist_in_angle',{$warehouse['warehouse_id']},".$row['angle_id'].",600,700) style='".$permission_W_button."cursor:pointer'> 상세보기</button></td>";  	
												 
												echo "</tr>";	
												$updown_i =$updown_i + 1;
											}
										}else{
											    echo " ";
										}									
								}else{
									 
								}								
								$desc_start_no = $desc_start_no - 1;
								$cg_count=$cg_count+1;
							} // foreach ($warehouses as ...
							
						
						} else {
							echo "<tr><td  colspan='4' >등록된 창고 없음</td></tr>";
						}
							echo "</tr>";	// 앵글목록 end							
						
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
 