    <? 
	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('제품','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "제품등록권한없음"; }

       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('제품','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = "제품수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('제품','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>제품삭제권한없음"; }


	   $result_setting = getwms_setting_state('1'); // 창고앵글 일괄삭제  set_id 값 1	   
	?>	
	
	<!-- 게시판 리스트 계산 start -->
	<?
	$list_condition = "wms_items as i, wms_cate as c where i.item_cate = c.cate_id and i.delYN = 'N' ";
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>
	
	<!-- 게시판 리스트 계산 end -->		
		<!-- page content -->
        <div class="center_col home_board50" role="main" >
          <div class="">
 
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 "> 
                <div class="x_panel">
                  <div class="x_title" style="display:flex">
                    <div style="width:70%"><h2>제품관리 > 제품목록 <small></small></h2></div>
                    <div class="clearfix" style="width:30%;text-align:right"><a href="/gn/m03/list.php">more▷</a></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<!-- <p class="text-muted font-13 m-b-30">
								  관리창고목록을 조회하실 수 있습니다.
								</p> -->

				<?php
				// 제품 목록 가져오기
				$items = getwms_items($start_record_number,8,$search,$SearchString);
				?>				
							
 
				<table id="tb_border" class="dataCustomTable" >
					<thead>
						<tr>
							<th width="10%">NO</th>
							<th width="30%">분류</th>
							<!-- <th>코드명</th> -->
							<th width="30%">제품명</th>
							<th width="15%">등록일자</th>
							<!-- <th>등록자</th> -->
							<th width="15%">입고관리</th>
						</tr>
					</thead>
					<tbody>
					<?
  
						if ($items) {
									$now_bg = "style='background:#ffffff'";
							foreach ($items as $item) {
								
								if ($now_bg == "style='background:#efefef'") {
									$now_bg = "style='background:#ffffff'";
								}else{
									$now_bg = "style='background:#efefef'";
								}	
								
								echo "<tr ".$now_bg.">";					
								echo "<td>".$totalcount."</td>";
								echo "<td>{$item['item_cate']}"; //{$item['item_cate_num']}
								
								
								$cates = getwms_cate($start_record_number,$itemsPerPage);
								if ($cates) { ?>
									 <select name = 'cate_is'  class='input-sm' style='width:90px;height:30px;font-size:14px;border:1px solid #ccc;<?echo $pm_U_item_button?>' onchange=fn_cate_change(<? echo "this.value";?>,<? echo "{$item['item_id']}"; ?>) ><option value='x'>분류변경</option>
									<? 
									foreach ($cates as $cate) {
									?>
									   <option value=<?echo "{$cate['cate_id']}"?>><?echo "{$cate['cate_name']}"?></option> 									
									<? 
									}
									echo "</select>";
									
								} else {
									//echo "<tr><td colspan='4'>등록된 제품 없음</td></tr>";
								}								
								
								
								
								
								echo "</td>";
								
								echo "<td><".$permission_U_button."a onclick=popup_win_item_update('item_update','{$item['item_id']}',400,300) style='cursor:pointer'>{$item['item_name']}</a>";
								
 
								if ("{$item['sum_quantity_item']}"!="0"){ // 제품이 있는 경우, 삭제 안됨
									//echo " 못지움";
								}else{
									// 제품이 없는데...........
									
									if ($result_setting[0]['set_state']=="Y") {  // 창고,앵글 일괄삭제 Y 이면,
										echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_product_del('product_del',{$item['item_id']},400,300) style='".$permission_D_button."cursor:pointer".$xhidden."'>";											
									}else{   // 창고,앵글 일괄삭제 N 이면,
										
										if ("{$item['sum_quantity_item']}"=="0") {
											echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_product_del('product_del',{$item['item_id']},400,300) style='".$permission_D_button."cursor:pointer".$xhidden."'>";												
										}
									}									
								}
																
								
								echo "</td>";
								echo "<td>{$item['item_rdate']}</td>";
								//echo "<td>관리자</td>";
								echo "<td><button type='button' class='btn btn-secondary btn-sm' onclick=popup_win_in('in_stock',{$item['item_id']}) style='padding:2;".$pm_W_item_button."'>입고등록</button>".$pm_W_item_txt."</td>";
								$totalcount=$totalcount - 1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td colspan='5'>검색 결과없음</td></tr>";
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
 