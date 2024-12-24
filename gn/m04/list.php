<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/top_navigation.php'); ?>
	
<?$queryString = $_SERVER['QUERY_STRING'];?>	
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('재고','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('재고조회 권한이 없습니다.');location.href='/gn/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 변경권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $permission_U_button="";  
	 $pm_rst_U = permission_ck('재고','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "display:none;"; $permission_W_txt = "재고권한없음"; }
 	  
     $result_setting = getwms_setting_state('2'); // 재고관리 (창고안) 수량 0 노출여부 set_id 값 2
	  
	   if ($result_setting[0]['set_state']=="N") {
		   $add_sql = " and quantity > 0"; 
	   }else{
		   $add_sql = " "; 
	   }	   
 
	?>	
		

	<?
	/////////////////   검색  start ////////////////////////////////////////////////////////////////////////
	  $search_add = "";
	  // 검색박스를 통해 받은 검색추가 질의어
	  if ($_GET['search_add']!="") {
		  $search_add = $_GET['search_add'];
	  }
	  
	  // 출고상태 
	  $searchStoreStatus = ""; 
	  if (($_GET['searchStoreStatus']=="")||($_GET['searchStoreStatus']=="ALL")) {
		//  $searchStoreStatus = "ALL"; 
		//  $add_condition     ="";
	  }else{
		//  $searchStoreStatus = $_GET['searchStoreStatus']; 
		 // $add_condition=" and state = ".$_GET['searchStoreStatus'];
	  }	
	  $add_condition     ="";

	 $searchType = "";  
	 $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
	 
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL";  
		$search_add = " and (( item_name like '%".$keyword."%'  ) ";
		$search_add = $search_add." or ( warehouse_name like '%".$keyword."%'  ) ";
		$search_add = $search_add." or ( angle_name like '%".$keyword."%'  )) ";
		
	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];  
		$search_add = " and ".$searchType." like '%".$keyword."%' ";	
	}

    // 날짜 종류 선택 (출고예정일 or 출고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 출고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
	}else{ // 검색을 했으면,
		//$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$keyword."%'";
	}

	
	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$search_add = $search_add." and date(s.rdate)  between '". $searchStartDate."' and  '". $searchEndDate."' ";	 
 	   }else{
			//$add_condition = $add_condition." and rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 

	//$list_condition = " wms_stock AS s LEFT JOIN  wms_items AS i  ON s.item_id = i.item_id LEFT JOIN  wms_warehouses AS w  ON s.warehouse_id = w.warehouse_id  JOIN  wms_angle AS a  ON s.angle_id = a.angle_id WHERE 1=1  and  w.delYN = 'N' AND a.delYN = 'N' AND w.warehouse_id <> 0 ".$add_sql.$search_add;
	$list_condition = " wms_stock AS s LEFT JOIN  wms_items AS i  ON s.item_id = i.item_id LEFT JOIN  wms_warehouses AS w  ON s.warehouse_id = w.warehouse_id  JOIN  wms_angle AS a  ON s.angle_id = a.angle_id WHERE 1=1  and  w.delYN = 'N' AND a.delYN = 'N' ".$add_sql.$search_add;
 
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////

	?>			
 	
		
<?php

// 모든 오류 표시
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>		
		
	<!-- 게시판 리스트 계산 start -->
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/search.php'); ?>			
	<!-- 게시판 리스트 계산 end -->

     <link href="/gn/css/custom.css" rel="stylesheet">

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
                    <h2>재고관리 > 재고목록<small></small></h2> 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  재고 목록을 조회하실 수 있습니다.
								</p>
								
								

            <!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
			<?
			$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
			?>				
            <form action="<?echo $_SERVER['PHP_SELF']?>" name="searchfrm" onsubmit="return ;" id="searchfrm" class="data_search">
			<input type="hidden" name="itemsPerPage" value="10">
			<input type="text" name="SearchString2" >
			
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
                            <td> 
                                <div class="lineup-row type_multi">
                                    <select name="searchType">
                                        <option value="ALL" <?if ($searchType=="ALL") {  echo "selected";  }?>>전체</option>
                                        <option value="item_name" <?if ($searchType=="item_name") {  echo "selected";  }?> >제품명</option>
										<option value="warehouse_name" <?if ($searchType=="warehouse_name") {  echo "selected";  }?> >창고명</option>
										<option value="angle_name" <?if ($searchType=="angle_name") {  echo "selected";  }?> >앵글명</option>
                                    </select>
                                    <input type="text" name="keyword" class="design" style="" <?if ($keyword!="") { echo "value='".$keyword."'";
                                    }else{ echo "value='' placeholder='검색어' "; }?> />
                                </div>							
                            </td>
                            <th>날짜</th>
                            <td> <input type="hidden" name='searchStoreDateType' value='STORE_EXPECTED_DATE'>
                                 
                                <!-- <label class='design'>
                                    <input type=radio name='searchStoreDateType' value='STORE_DATE'  <?if($searchStoreDateType=="STORE_DATE"){ echo "checked"; }?>>출고일
                                </label> -->
                                <div class="lineup-row type_date" style="display: inline;">
								 시작 <input  class='date  design js_pic_day' type="date" name="searchStartDate" value="<? echo $searchStartDate; ?>" > <span class="fr_tx">-</span>
									끝 <input  class='date  design js_pic_day' type="date" name="searchEndDate" value="<? echo $searchEndDate; ?>" > 
                                </div>
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
                                    <input type="button" style="margin-left:-10px;margin-right:-7px;" onclick="location.href='/excel/phpspread_m04.php?<?echo $queryString;?>'" value="엑셀받기" />	
                                </span>
                            </li>							
                        </ul>
                    </div>
                </div>
            </form><!-- end data_search -->										
								
								
									
								<form method="post" action="<?echo $_SERVER['PHP_SELF']?>" name="search" onsubmit="return ;">
								<input type="hidden" name="itemsPerPage" value="10">									
									<table width="100%">
									<tr>
										<td width="50%">
										<div class="dataTables_length" id="datatable-buttons_length" style="width:100%"> Total : <? echo $totalcount?> 건&nbsp;&nbsp;
										<select name="datatable-buttons_length" aria-controls="datatable-buttons" class="form-control input-sm"   onchange="location.href=this.value;" style="width:120px;font-size:13px">
											<option value="<?echo $_SERVER['PHP_SELF']."?itemsPerPage=10&searchType=".$searchType."&keyword=".$keyword."&searchStartDate=".$searchStartDate."&searchEndDate=".$searchEndDate."&searchStoreStatus=".$searchStoreStatus ?>" <? if ($itemsPerPage=="10"){ echo "selected"; }?>>10개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']."?itemsPerPage=20&searchType=".$searchType."&keyword=".$keyword."&searchStartDate=".$searchStartDate."&searchEndDate=".$searchEndDate."&searchStoreStatus=".$searchStoreStatus ?>"  <? if ($itemsPerPage=="20"){ echo "selected"; }?>>20개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']."?itemsPerPage=50&searchType=".$searchType."&keyword=".$keyword."&searchStartDate=".$searchStartDate."&searchEndDate=".$searchEndDate."&searchStoreStatus=".$searchStoreStatus ?>"  <? if ($itemsPerPage=="50"){ echo "selected"; }?>>50개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']."?itemsPerPage=100&searchType=".$searchType."&keyword=".$keyword."&searchStartDate=".$searchStartDate."&searchEndDate=".$searchEndDate."&searchStoreStatus=".$searchStoreStatus ?>"  <? if ($itemsPerPage=="100"){ echo "selected"; }?>>100개 보기</option>
										</select>									

										 </div>											
										</td>
									</tr>
									</table>
									<input type="hidden" name="search_add" value="<? echo $search_add;?>">
								</form>
			

				<?php
				
				// 현재 재고 상태 가져오기
				$stock = getStock_all($start_record_number,$itemsPerPage,$search_add);
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
							<th>NO</th>
							<th>제품명</th>
							<!-- <th>거래처</th> -->
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
								//echo "<td>{$stockItem['company_name']}</td>";
								echo "<td>{$stockItem['warehouse_name']}</td>";
								echo "<td>{$stockItem['angle_name']}</td>";
								echo "<td>".number_format("{$stockItem['quantity']}")."</td>";
								echo "<td>{$stockItem['rdate']}</td>";
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
											echo "<td><button type='button' class='btn btn-secondary btn-sm' style='".$permission_U_button."padding:2;width:70px' value='상세보기'  onclick=popup_win_stock_move_stock('stock_move',{$stockItem['stock_id']},{$stockItem['angle_id']},{$stockItem['warehouse_id']},{$stockItem['quantity']}) style='".$permission_U_button."cursor:pointer'>재고이동</button></td>";										
										}										
									}


								}
								$desc_start_no = $desc_start_no - 1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td colspan='7'>결과 없음</td></tr>";
						}
					?>			
					</tbody>
				</table>
				
				<?
     
				$addpara = "&searchType=".$searchType."&keyword=".$keyword."&searchStartDate=".$searchStartDate."&searchEndDate=".$searchEndDate."&searchStoreStatus=".$searchStoreStatus;
				//echo $addpara;
 
				?>
				
				<!-- <div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div> -->		 
				<div style="width:100%;text-align:center"><?  echo paginate_addpara($totalItems, $itemsPerPage, $currentPage, $url, $addpara);	?></div>		 
				
				 
 
		 

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


<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/foot.php'); ?>