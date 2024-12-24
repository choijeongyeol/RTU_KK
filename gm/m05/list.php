<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/topmenu.php'); ?>

	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/top_navigation.php'); ?>
	
<?$queryString = $_SERVER['QUERY_STRING'];?>	
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('입고지시관리','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('입고지시관리조회 권한이 없습니다.');location.href='/gm/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('입고지시관리','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "입고지시관리등록권한없음"; }

       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('입고지시관리','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "display:none;"; $permission_U_txt = "입고지시관리수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('입고지시관리','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>입고지시관리삭제권한없음"; }


	   $result_setting = getwms_setting_state('1'); // 창고앵글 일괄삭제  set_id 값 1	   
	?>	

	<!-- 게시판 리스트 계산 start -->
 
	<?
	/////////////////   검색  start ////////////////////////////////////////////////////////////////////////
	  // 입고상태 
	  $searchStoreStatus = ""; 
	  if (($_GET['searchStoreStatus']=="")||($_GET['searchStoreStatus']=="ALL")) {
		  $searchStoreStatus = "ALL"; 
		  $add_condition     ="";
	  }else{
		  $searchStoreStatus = $_GET['searchStoreStatus']; 
		  $add_condition=" and state = ".$_GET['searchStoreStatus'];
	  }		

	// 검색어
	  $searchType = ""; 
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL"; 
			$add_condition = $add_condition." WHERE (( SELECT warehouse_name FROM wms_warehouses WHERE warehouse_id = i.warehouse_id ) like '%".$_GET['keyword']."%'  ";	
			
			$add_condition = $add_condition." or ( SELECT angle_name FROM wms_angle WHERE angle_id = i.angle_id ) like '%".$_GET['keyword']."%' ";	
			
			$add_condition = $add_condition." or  item_name like '%".$_GET['keyword']."%')";
		

	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];
	
 		if ($searchType=="warehouse_name") {
			$add_condition = $add_condition." WHERE ( SELECT  warehouse_name FROM wms_warehouses WHERE warehouse_id = i.warehouse_id  ) like '%".$_GET['keyword']."%' ";	
		}
 		if ($searchType=="angle_name") {
			$add_condition = $add_condition." WHERE ( SELECT  angle_name FROM wms_angle WHERE angle_id = i.angle_id  ) like '%".$_GET['keyword']."%' ";	
		}
		if ($searchType=="item_name") {
			$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
		}
	}

    // 날짜 종류 선택 (입고예정일 or 입고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 입고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
		//$add_condition = $add_condition." and plan_date = ";
	}else{ // 검색을 했으면,
		$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
	}

	
	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$add_condition = $add_condition." and plan_date  between '". $searchStartDate."' and  '". $searchEndDate."' ";	   
	   }else{
			$add_condition = $add_condition." and rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 
  
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////
 	if ($searchType=="item_name") { 
		$list_condition  = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id and p.partner_id = i.partner_id ";
		$list_condition  = $list_condition." where 1=1 ".$add_condition." and  i.delYN = 'N' and i.partner_id = ".$_SESSION['partner_id'];
	}
 	if ($searchType=="warehouse_name") { 
		$list_condition  = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id and p.partner_id = i.partner_id  ";
		$list_condition = $list_condition." JOIN  wms_warehouses w ON w.warehouse_id = i.warehouse_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N' and i.partner_id = ".$_SESSION['partner_id'];
	}
 	if ($searchType=="angle_name") { 
		$list_condition  = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id and p.partner_id = i.partner_id  ";
		$list_condition = $list_condition." JOIN  wms_angle a ON a.angle_id = i.angle_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N' and i.partner_id = ".$_SESSION['partner_id'];
	}
 	if (($searchType=="")||($searchType=="ALL")) { 
		$list_condition  = " wms_inbound i  JOIN wms_items p  on p.item_id = i.product_id and p.partner_id = i.partner_id  ";
		$list_condition = $list_condition." JOIN  wms_warehouses w ON w.warehouse_id = i.warehouse_id ";		
		$list_condition = $list_condition." JOIN  wms_angle a ON a.angle_id = i.angle_id ";		
		$list_condition = $list_condition.$add_condition." and  i.delYN = 'N' and i.partner_id = ".$_SESSION['partner_id'];
	}
 
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트

	?>	
	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/search.php'); ?>			
	<!-- 게시판 리스트 계산 end -->
 
     <link href="/gm/css/custom.css" rel="stylesheet">
 

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
                    <h2>입고지시관리 > 입고지시<small></small></h2> 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  입고지시를 관리하실 수 있습니다.
								</p>
									<!-- <table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info"  >
									<tr>
										<td>  검색박스 </td>
									</tr>
									</table><br> -->
									
									
            <!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
            <form action="<?echo $_SERVER['PHP_SELF']?>" name="searchfrm" onsubmit="return ;" id="searchfrm" class="data_search">
			<input type="hidden" name="itemsPerPage" value="10">
			<input type="text" name="SearchString2" value="<?echo $SearchString2?>">
			
                <div class="search_form">
                    <table class="table_form">
                        <colgroup>
                            <col width="130"/>
                            <col width="*"/>
                            <col width="130"/>
                            <col width="*"/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>검색어</th>
                            <td colspan="3">
                                <div class="lineup-row type_multi">
                                    <select name="searchType">
                                        <option value="ALL" <?if ($searchType=="ALL") {  echo "selected";  }?>>전체</option>
                                        <option value="item_name" <?if ($searchType=="item_name") {  echo "selected";  }?> >제품명</option>
                                        <option value="warehouse_name" <?if ($searchType=="warehouse_name") {  echo "selected";  }?> >창고명</option>
                                        <option value="angle_name" <?if ($searchType=="angle_name") {  echo "selected";  }?> >앵글명</option>
                                    </select>
									
                                    <input type="text" name="keyword" class="design" style="" <?if ($_GET['keyword']!="") { echo "value='".$_GET['keyword']."'";
                                    }else{ echo "value='' placeholder='검색어' "; }?> />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>날짜</th>
                            <td>
                                <label class='design'>
                                    <input type=radio name='searchStoreDateType' value='STORE_EXPECTED_DATE' <?if($searchStoreDateType=="STORE_EXPECTED_DATE"){ echo "checked"; }?>>입고예정일
                                </label>
                                <label class='design'>
                                    <input type=radio name='searchStoreDateType' value='STORE_DATE'  <?if($searchStoreDateType=="STORE_DATE"){ echo "checked"; }?>>입고일
                                </label>
                                <div class="lineup-row type_date" style="display: inline;">
								 시작 <input  class='date  design js_pic_day' type="date" name="searchStartDate" value="<? echo $searchStartDate; ?>" > <span class="fr_tx">-</span>
									끝 <input  class='date  design js_pic_day' type="date" name="searchEndDate" value="<? echo $searchEndDate; ?>" > 
                                </div>
                            </td>
                            <th>상태</th>
                            <td>
                                <label class='design'>
                                    <input type=radio name='searchStoreStatus' value='ALL'  <? if (($searchStoreStatus=="ALL")|| ($searchStoreStatus=="")) {  echo "checked"; }?>>전체
                                </label>
                                <label class='design'>
                                    <input type=radio name='searchStoreStatus' value='0' <? if ($searchStoreStatus=="0") {  echo "checked"; }?> >
                                    <span class="c_tag gray">입고대기</span>
                                </label>
                                <label class='design'>
                                    <input type=radio name='searchStoreStatus' value='1'  <? if ($searchStoreStatus=="1") {  echo "checked"; }?>>
                                    <span class="c_tag blue">입고완료</span>
                                </label>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- 가운데정렬버튼 -->
                    <div class="c_btnbox">
                        <ul>
                            <li>
                                <span class="c_btn h34 gray">
                                    <input type="button" onclick="location.href='<?echo $_SERVER['PHP_SELF']?>'" value="초기화" accesskey="s"/>
                                </span>
                            </li>
                            <li>
                                <span class="c_btn h34 black">
                                    <input type="submit" value="검색" accesskey="s"/><!--   onclick="searchStoreInstruction();" -->
                                </span>
                            </li>
                            <li>
                                <span class="c_btn h34 blue">
								    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-download"></i> 
                                    <input type="button" style="margin-left:-10px;margin-right:-7px;" onclick="location.href='/m_excel/phpspread_m05.php?<?echo $queryString;?>'" value="엑셀받기" />	
									
                                </span>
                            </li>								
                        </ul>
                    </div>
                </div>
            </form><!-- end data_search -->									
									
									
											
									<table width="100%">
									<tr>
										<td colspan="3" style="text-align:right;width:100%;<?echo $permission_W_button;?>"> <a href="/gm/m05/write.php"><button type='button' class='btn btn-secondary btn-sm' style='padding:2;width:127px;background:#ff0000' >지시생성</button></a></td>
									</tr>

									</table>



			

				<?php
 
				// 제품 목록 가져오기
				$items = getwms_inbounds($start_record_number,$itemsPerPage,$search,$add_condition);
				?>				
					
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
					<!-- <colgroup>
						<col class="w5">
						<col class="w10">
						<col class="w10">
						<col class="w10">
						<col class="w10">
						<col class="w10">
						<col>
						<col class="w15">
					</colgroup> -->
					<thead>
						<tr>
							<th rowspan="2">NO</th>
							<!-- <th>주문번호</th> -->
							<th>제품명</th>
							<th>창고명</th>
							<th  width="5%">예정수량</th>
							<th  width="15%">예정일자</th>
							<th rowspan="2" width="15%">입고상태</th>
							<th rowspan="2" width="15%">관리</th>
						</tr>
						<tr>
							<th>업체명</th>
							<th>앵글명</th>
							<th>입고수량</th>
							<th  style='border-right: 1px solid #dee2e6;'>입고일자</th>
							
						</tr>
					</thead>		
					<tbody>
					<?
  
						if ($items) {
							foreach ($items as $item) {
								
								if ($desc_start_no%2 == 0) {
									$bgcolor = "#f9f9f9";
								}else{
									$bgcolor = "#fff";
								}								
								echo "<tr style='background:".$bgcolor."'>";					
								echo "<td rowspan='2'>".$desc_start_no."</td>";
								//echo "<td><".$permission_U_button."a onclick=popup_win_item_update('item_update','{$item['item_id']}',400,300) style='cursor:pointer'>{$item['item_name']}</a>";							
								echo "<td >{$item['item_name']}</td>";
								echo "<td>{$item['warehouse_name']}</td>";
								echo "<td>{$item['planned_quantity']}</td>";
								
								if ("{$item['plan_date']}"=="0000-00-00") {
								echo "<td >미입력</td>";									
								}else{
								echo "<td >{$item['plan_date']}</td>";									
								}
 
								if ("{$item['state']}"==0) {
								echo "<td rowspan='2'><span class='c_tag gray'>대기</span></td>";									
								}else{
								echo "<td rowspan='2'><span class='c_tag blue'>완료</span></td>";								
								}
								
								
								if ("{$item['state']}"==0) {
								echo "<td rowspan='2'> <button type='button' class='btn btn-info btn-sm' style='padding:2;width:50px;".$permission_U_button."' value='수정' onclick=popup_win_inbound_update('inbound_update','{$item['inbound_id']}',400,500,'{$item['plan_date']}') style='cursor:pointer'>입고</button><button type='button'  class='btn btn-secondary btn-sm' style='padding:2;width:50px;".$permission_D_button."' onclick=popup_win_inbound_del('inbound_del','{$item['inbound_id']}',400,500)>삭제</button></td>";							}else{
								echo "<td rowspan='2'>해당없음</td>";							
								}		

								
								echo "</tr>";
								
								echo "<tr style='background:".$bgcolor."'>";					
								echo "<td>{$item['company_name']}</td>";
								echo "<td>{$item['angle_name']}</td>";
								echo "<td>{$item['inbound_quantity']}</td>";
								
								if ("{$item['rdate']}"=="0000-00-00") {
								echo "<td style='border-right: 1px solid #dee2e6;'> </td>";									
								}else{
								echo "<td style='border-right: 1px solid #dee2e6;'>{$item['rdate']}</td>";									
								}								
 
								echo "</tr>";								
								$desc_start_no=$desc_start_no - 1;	
							}
						} else {
							echo "<tr><td colspan='7'>검색 결과없음</td></tr>";
						}
					?>		
					</tbody>
				</table>
				
				<?
     
				$addpara = "&searchType=".$_GET['searchType']."&keyword=".$_GET['keyword']."&searchStartDate=".$_GET['searchStartDate']."&searchEndDate=".$_GET['searchEndDate']."&searchStoreStatus=".$searchStoreStatus;
				//echo $addpara;
 
				?>

				<div style="width:100%;text-align:center"><?  echo paginate_addpara($totalItems, $itemsPerPage, $currentPage, $url, $addpara);	?></div>		 
				
				<div style="width:98%;text-align:right">
					<!-- <button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win('item')" style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="대상 등록">제품 등록</button> -->	
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