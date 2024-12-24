<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/top_navigation.php'); ?>
	
	<!-- 게시판 리스트 계산 start -->
	<?
	$list_condition = "wms_in_stock_history";
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/search.php'); ?>			
	<!-- 게시판 리스트 계산 end -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <!-- <div class="title_left">
                <h3> <small> </small></h3>
              </div> -->
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>제품관리 > 제품history<small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  제품history 조회하실 수 있습니다.
								</p>
								
									
								<form method="post" action="<?echo $_SERVER['PHP_SELF']?>" name="search" onsubmit="return ;">
								<input type="hidden" name="itemsPerPage" value="10">									
									<table width="100%">
									<tr>
										<td width="50%">
										<div class="dataTables_length" id="datatable-buttons_length" style="width:100%"> Total : <? echo $totalcount?> 건&nbsp;&nbsp;
										<select name="datatable-buttons_length" aria-controls="datatable-buttons" class="form-control input-sm"   onchange="location.href=this.value;" style="width:120px;font-size:13px">
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=10&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="10"){ echo "selected"; }?>>10개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=20&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="20"){ echo "selected"; }?>>20개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=50&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="50"){ echo "selected"; }?>>50개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=100&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="100"){ echo "selected"; }?>>100개 보기</option>
										</select>									

										 </div>											
										</td>
										<td width="40%" align="right" style="text-align:right">
										<!-- <input type="text" name="SearchString" size="20" class="form-control col-md-3" style="width:180px;height:30px;margin-top:-4px;margin-left:5px;float:right" required>	
										
										<select name="search" style="width:180px;height:30px;margin-top:-4px;float:right;border:1px solid #D8D8D8;">
											<option value="item_code" <? if ($search=="item_code") { echo "selected"; }?> >제품코드명</option>
											<option value="item_name" <? if ($search=="item_name") { echo "selected"; }?>  >제품명</option>
										</select> -->
										
										</td>
										<td  width="10%" > 
										<!-- <div style="text-align:right;width:100%;">
											 <button type='submit' class='btn btn-secondary btn-sm' style='padding:2;width:61px' value="검색">검색</button><button type='button' class='btn btn-info btn-sm' style='padding:2;width:61px' onClick="location.href='<?echo $_SERVER['PHP_SELF']?>'">초기화</button>
										</div>	 -->										
										</td>
									</tr>
									</table>
								</form>
			

				<?php
				// 제품 목록 가져오기
				$in_stock_history = get_in_Stock_history_detail($start_record_number,$itemsPerPage);
				?>				
					
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
					<thead>
						<tr>
							<th>NO</th>
							<th>제품</th>
							<th>입고창고</th>
							<th>입고앵글</th>
							<th>수량</th>
							<th>등록일자</th>
							<th>등록자</th>
							<th>IP</th>
						</tr>
					</thead>
					<tbody>
					<?
 
						if ($in_stock_history) {
							foreach ($in_stock_history as $in_stock_historyItem) {
								echo "<tr>";					 
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$in_stock_historyItem['item_name']}</td>";
								echo "<td>{$in_stock_historyItem['warehouse_name']}</td>";
								echo "<td>{$in_stock_historyItem['angle_name']}</td>";
								echo "<td>{$in_stock_historyItem['quantity']}</td>";
								echo "<td>{$in_stock_historyItem['rdate']}</td>";
								echo "<td>{$in_stock_historyItem['in_stock_who']}</td>";
								echo "<td>{$in_stock_historyItem['in_stock_ip']}</td>";
								$desc_start_no = $desc_start_no - 1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td>등록된 제품 없음</td></tr>";
						}
					?>					
 
					</tbody>
				</table>

				<div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div>		 
		 

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


<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/foot.php'); ?>