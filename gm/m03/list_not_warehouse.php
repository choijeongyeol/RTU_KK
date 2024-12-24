<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/top_navigation.php'); ?>

	
    <?  /// 권한 체크 : 조회권한 - 메인화면 리다이렉트 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R2 = permission_ck('창고','R',$_SESSION['admin_role']);
	 if ($pm_rst_R2 == 'F') {	 echo "<script>alert('창고조회 권한이 없습니다.');location.href='/gm/home/dashboard.php'</script>"; exit();	 }
 	   
       /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W2 = permission_ck('창고','W',$_SESSION['admin_role']); if ($pm_rst_W2 == 'F') {  $permission_W2_button = "display:none;"; $permission_W2_txt = "창고등록권한없음"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U2 = permission_ck('창고','U',$_SESSION['admin_role']); if ($pm_rst_U2 == 'F') {  $permission_U2_button = "!"; $permission_U2_txt = "창고수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D2 = permission_ck('창고','D',$_SESSION['admin_role']); if ($pm_rst_D2 == 'F') {  $permission_D2_button = "display:none;"; $permission_D2_txt = "<BR>창고삭제권한없음"; }
	   
      /// 권한 체크 : 조회권한 - 메인화면 리다이렉트 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('앵글','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('앵글조회 권한이 없습니다.');location.href='/gm/home/dashboard.php'</script>"; exit();	 }
	   
       /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('앵글','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "앵글등록권한없음"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('앵글','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = ""; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('앵글','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>앵글삭제권한없음"; }
 	  
	   $result_setting = getwms_setting_state('1'); // 창고앵글 일괄삭제  set_id 값 1
	   
	?>	
	
	
	<!-- 게시판 리스트 계산 start -->
	<?
  
	// 검색결과 추가조건 sql
	if ($_POST['SearchString']!="") {
		$add_condition = " and ".$_POST['search']." like '%".$_POST['SearchString']."%'";
	}else{
		$add_condition = "";
	}
 
	$list_condition = "wms_warehouses where 1=1 and warehouse_id = 0 and delYN = 'N' ".$add_condition;
	$new_itemsPerPage = 5; // 페이지당 레코드 수 지정
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트	
	
    if (($_GET['angle_id']!="")&&($_GET['order']!="")) {
		update_angle_order($_GET['angle_id'],$_GET['order']);
    }	

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
                    <h2>재고관리 > 창고목록 <small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  관리창고목록을 조회하실 수 있습니다.
								</p>
								
									
								<form method="post" action="<?echo $_SERVER['PHP_SELF']?>" name="search" onsubmit="return ;">
								<input type="hidden" name="itemsPerPage" value="10">									
									<table width="100%">
									<tr>
										<td width="50%">
										<div class="dataTables_length" id="datatable-buttons_length" style="width:100%"> Total : <? echo $totalcount?> 건&nbsp;&nbsp;
										<select name="datatable-buttons_length" aria-controls="datatable-buttons" class="form-control input-sm"   onchange="location.href=this.value;" style="width:120px;font-size:13px">
										<?
										if ($new_itemsPerPage!=0) {
										 ?>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=<?echo $new_itemsPerPage;?>&search=&SearchString=&gubun=free"  <? if ($itemsPerPage==$new_itemsPerPage){ echo "selected"; }?>><?echo $new_itemsPerPage;?>개 보기</option>		
										<?
										 }	
										?>									
										
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=10&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="10"){ echo "selected"; }?>>10개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=20&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="20"){ echo "selected"; }?>>20개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=50&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="50"){ echo "selected"; }?>>50개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=100&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="100"){ echo "selected"; }?>>100개 보기</option>
										</select>		
										

										 </div>											
										</td>
										<td width="40%" align="right" style="text-align:right">
										<input type="text" name="SearchString" size="20" class="form-control col-md-3" style="width:180px;height:30px;margin-top:-4px;margin-left:5px;float:right" required  <? if ($_POST['SearchString']!="") { echo 'value="'.$_POST['SearchString'].'"'; }?> >	
										
										<select name="search" style="width:180px;height:30px;margin-top:-4px;float:right;border:1px solid #D8D8D8;">
											<!-- <option value="warehouse_code" <? if ($search=="warehouse_code") { echo "selected"; }?> >창고코드명</option> -->
											<option value="warehouse_name" <? if ($search=="warehouse_name") { echo "selected"; }?>  >창고명</option>
										</select>
										
										</td>
										<td  width="10%" > 
										<div style="text-align:right;width:100%;">
											 <button type='submit' class='btn btn-secondary btn-sm' style='padding:2;width:61px' value="검색">검색</button><button type='button' class='btn btn-info btn-sm' style='padding:2;width:61px' onClick="location.href='<?echo $_SERVER['PHP_SELF']?>'">초기화</button>
										</div>											
										</td>
									</tr>
									</table>
								</form>
			

				<?php
		// 제품 목록 가져오기
		$result_list =  stock_list('0','0');	// 앵글안의 제품 총수량
				?>				
					
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
					<thead>
						<tr>
							<th>NO</th>
							<th>제품명</th>
							<th>수량</th>
							<th>분류명</th>
							<th>입고처리</th>
						</tr>
					</thead>
					<tbody>
					<?  
						 $i = 1;
						if ($result_list) {
							foreach ($result_list as $in_stock_item) {
								echo "<tr>";					 
								echo "<td>".$i."</td>";
								echo "<td>{$in_stock_item['item_name']}</td>";
								echo "<td>".number_format("{$in_stock_item['item_cnt']}")."</td>";
								echo "<td>{$in_stock_item['cate_name']}</td>";
								echo "<td><button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('in_stock',{$in_stock_item['item_id']}) style='padding:2'>앵글로 이동</button></td>";
								$i = $i + 1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td colspan='5'> 제품 없음</td></tr>";
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