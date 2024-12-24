<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/top_navigation.php'); ?>
	
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
	  
	  // HAVING     cate_name LIKE '%검색어%'	  $add_condition     ="";
	 $searchType = ""; 	  $keyword="";  $search_add2="";
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL";  $keyword = $_GET['keyword'];
		//$search_add = " and ( item_name like '%".$_GET['keyword']."%' or HAVING cate_name like '%".$_GET['keyword']."%'  ) ";
		
		$searchType = "item_name";
	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];  $keyword = $_GET['keyword'];
		
		if ($searchType == "item_name") {
			$search_add = " and ".$searchType." like '%".$_GET['keyword']."%' ";	
		}
		if ($searchType == "cate_name") {
			$search_add2 = " HAVING ".$searchType." like '%".$_GET['keyword']."%' ";
			$search_add = " ";
		}else{
			$search_add2 = "";
		}
 	}

    // 날짜 종류 선택 (출고예정일 or 출고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 출고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
	}else{ // 검색을 했으면,
		//$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$_GET['keyword']."%'";
	}


	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$search_add = $search_add." and date(rdate)  between '". $searchStartDate."' and  '". $searchEndDate."' ";	 
 	   }else{
			//$add_condition = $add_condition." and rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 
   // $add_sql="";/////////////
   //$search_add="";///////////
   // SELECT *, (select cate_name from wms_cate where i.item_cate = cate_id ) as cate_name from wms_stock s left join wms_items i on s.item_id = i.item_id HAVING cate_name like '%2%' and s.warehouse_id = 0 and s.angle_id = 0 and quantity > 0;
 
 
    if ($search_add2 != "") {
		$list_condition_cnt = " (    SELECT   s.*,                  i.item_id AS item_id_alias,             (SELECT cate_name FROM wms_cate WHERE i.item_cate = cate_id ) AS cate_name     FROM     wms_stock s     LEFT JOIN wms_items i ON s.item_id = i.item_id     WHERE    (SELECT cate_name FROM wms_cate WHERE i.item_cate = cate_id ) LIKE '%".$_GET['keyword']."%'   AND s.warehouse_id = 0    AND s.angle_id = 0    ".$add_sql." ) AS subquery";
    } else{
		$list_condition_cnt = " wms_stock s left join wms_items i on  s.item_id = i.item_id where s.warehouse_id = 0 and s.angle_id = 0 ".$add_sql.$search_add;		
    }
	// echo $list_condition_cnt;
 
    if ($search_add2 != "") {
		$list_condition = "  wms_stock s LEFT JOIN wms_items i ON s.item_id = i.item_id WHERE (SELECT cate_name FROM wms_cate WHERE i.item_cate = cate_id ) LIKE '%".$_GET['keyword']."%' AND s.warehouse_id = 0    AND s.angle_id = 0  ".$add_sql;
    } else{
		$list_condition = " wms_stock s left join wms_items i on  s.item_id = i.item_id where s.warehouse_id = 0 and s.angle_id = 0 ".$add_sql.$search_add;		
    }
	 
	 
	  
	 
	 
    //echo $list_condition_cnt;
    //exit();
	
	$totalcount = list_total_cnt($list_condition_cnt); // 목록 전체 카운트
	
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
                    <h2>재고관리 > 재고목록(창고 외부) <small></small></h2> 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  관리창고목록을 조회하실 수 있습니다.
								</p>
								
								

            <!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
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
                                        <!-- <option value="ALL" <?if ($searchType=="ALL") {  echo "selected";  }?>>전체</option> -->
                                        <option value="item_name" <?if ($searchType=="item_name") {  echo "selected";  }?> >제품명</option>
										<option value="cate_name" <?if ($searchType=="cate_name") {  echo "selected";  }?> >분류명</option>
                                    </select>
                                    <input type="text" name="keyword" class="design" style="" <?if ($_GET['keyword']!="") { echo "value='".$_GET['keyword']."'";
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
				$result_list = getStock_00('0','0',$start_record_number,$itemsPerPage,$search_add,$searchType,$keyword);
				?>				
					
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
					<thead>
						<tr>
							<th>NO</th>
							<th>제품명</th>
							<th>수량 (창고밖)</th>
							<th>분류명</th>
							<th>업데이트</th>
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
								echo "<td>{$in_stock_item['rdate']}</td>";
								if ($pm_rst_U == 'F'){
									echo "<td>권한없음</td>";
								}else{								
									echo "<td><button type='button' class='btn btn-secondary btn-sm' onclick=popup_win_3('move_stock',{$in_stock_item['item_id']},{$in_stock_item['item_cnt']}) style='padding:2'>앵글로 이동</button></td>";
								}
								$i = $i + 1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td colspan='6'> 결과 없음</td></tr>";
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