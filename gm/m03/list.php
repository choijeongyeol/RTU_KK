<?
require_once __DIR__ . '/../../composer/vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorHTML;	
?>	

<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/topmenu.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/top_navigation.php'); ?>
	
<?$queryString = $_SERVER['QUERY_STRING'];?>
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('제품','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('제품조회 권한이 없습니다.');location.href='/gm/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('제품','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "제품등록권한없음"; }

       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $permission_U_button = "";  
	 $pm_rst_U = permission_ck('제품','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = "제품수정권한없음"; }
 	    
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $permission_D_button = "";  
	 $pm_rst_D = permission_ck('제품','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>제품삭제권한없음"; }

       $result_setting = "N";
	   $result_setting = getwms_setting_state('1'); // 창고앵글 일괄삭제  set_id 값 1	   
	?>	
	
		
	<!-- 게시판 리스트 계산 start -->
 
	<?
	/////////////////   검색  start ////////////////////////////////////////////////////////////////////////
	  // 출고상태 
	  $searchStoreStatus = ""; 
	  if (($_GET['searchStoreStatus']=="")||($_GET['searchStoreStatus']=="ALL")) {
		  $searchStoreStatus = "ALL"; 
		  //$add_condition     ="";
	  }else{
		  $searchStoreStatus = $_GET['searchStoreStatus']; 
		  //$add_condition=" and state = ".$_GET['searchStoreStatus'];
	  }	
	  $add_condition = "";

	// 검색어
	  $searchType = ""; 
	  $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
	if (($_GET['searchType']=="")||($_GET['searchType']=="ALL")) {  // 검색을 하지 않았으면,
		$searchType      = "ALL"; 
			$add_condition = $add_condition." and (i.item_name like '%".$keyword."%'  or c.cate_name like '%".$keyword."%'  or i.item_code like '%".$keyword."%' )";
		
	}else{ // 검색을 했으면,
		$searchType = $_GET['searchType'];
		
 		if ($searchType=="item_cate") {
			$add_condition = $add_condition." and c.cate_name like '%".$keyword."%' ";	
		}
		if ($searchType=="item_name") {
			$add_condition = $add_condition." and i.item_name like '%".$keyword."%' ";
		}
		if ($searchType=="item_code") {
			$add_condition = $add_condition." and i.item_code like '%".$keyword."%' ";
		}
	}

    // 날짜 종류 선택 (출고예정일 or 출고일)
     $searchStoreDateType = "";
	if (($_GET['searchStoreDateType']=="")||($_GET['searchStoreDateType']=="STORE_EXPECTED_DATE")) {  // 검색을 하지 않았으면, 출고예정일
		$searchStoreDateType      = "STORE_EXPECTED_DATE"; 
		//$add_condition = $add_condition." and plan_date = ";
	}else{ // 검색을 했으면,
		$searchStoreDateType = "STORE_DATE";
		//$add_condition = $add_condition." and  ".$_GET['searchType']." like '%".$keyword."%'";
	}

	
	
   // 날짜 데이터 (시작일 / 끝일)
   $searchStartDate = "";  $searchEndDate="";
   
   if (($_GET['searchStartDate']!="") && ($_GET['searchEndDate']!="")) {  // r날짜 조건검색 
	   $searchStartDate= $_GET['searchStartDate']; $searchEndDate = $_GET['searchEndDate'];
	   
	   if ($searchStoreDateType=="STORE_EXPECTED_DATE") {
			$add_condition = $add_condition." and item_rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	   
	   }else{
			$add_condition = $add_condition." and item_rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 
  
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////
	
	$list_condition = "wms_items as i, wms_cate as c where i.partner_id =".$_SESSION['partner_id']." and i.item_cate = c.cate_id and i.delYN = 'N' ".$add_condition;
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트

	?>	
		
<?php
// 모든 오류 표시
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
                    <h2>제품관리 > 제품목록 <small></small></h2> 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  관리제품 목록을 조회하실 수 있습니다.
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
                                        <option value="ALL" <?if ($searchType=="ALL") {  echo "selected";  }?>>전체</option>
                                        <option value="item_name" <?if ($searchType=="item_name") {  echo "selected";  }?> >제품명</option>
										<option value="item_code" <?if ($searchType=="item_code") {  echo "selected";  }?> >제품코드</option>
 										<option value="item_cate" <?if ($searchType=="item_cate") {  echo "selected";  }?> >분류명</option>
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
                                    <input type="button" style="margin-left:-10px;margin-right:-7px;" onclick="location.href='/m_excel/phpspread_m03.php?<?echo $queryString;?>'" value="엑셀받기" />	
                                </span>
                            </li>
						
							
                        </ul>
                    </div>
                </div>
            </form><!-- end data_search -->										
								
								
								<?
								$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
								?>	
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
				// 제품 목록 가져오기
				$items = getwms_items_list($start_record_number,$itemsPerPage,$searchType,$keyword,$searchStartDate,$searchEndDate);
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
							<th>분류</th>
							<th>제품명</th>
							<th>제품코드</th>
							<th>등록일자</th>
							<th>등록자</th>
							<th>관리</th>
						</tr>
					</thead>		
					<tbody>
					<?
  
						if ($items) {
							foreach ($items as $item) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$item['item_cate']}"; //{$item['item_cate_num']}
								
								
								$cates = getwms_cate($start_record_number,$itemsPerPage);
								if ($cates) { ?>
									 <select name = 'cate_is'  class='input-sm' style='width:90px;height:30px;font-size:14px;border:1px solid #ccc' onchange=fn_cate_change(<? echo "this.value";?>,<? echo "{$item['item_id']}"; ?>) ><option value='x'>분류변경</option>
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
								
								echo "<td><".$permission_U_button."a onclick=popup_win_item_update('item_update','{$item['item_id']}',400,350) style='cursor:pointer'>{$item['item_name']}</a>";
													 
 
								if ("{$item['sum_quantity_item']}"!="0"){ // 제품이 있는 경우, 삭제 안됨
									//echo " 못지움";
								}else{
									// 제품이 없는데...........
									$xhidden ="";
									if ($result_setting[0]['set_state']=="Y") {  // 창고,앵글 일괄삭제 Y 이면,
										echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_product_del('product_del',{$item['item_id']},400,300) style='".$permission_D_button."cursor:pointer".$xhidden."'>";											
									}else{   // 창고,앵글 일괄삭제 N 이면,
										
										if ("{$item['sum_quantity_item']}"=="0") {
											echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_product_del('product_del',{$item['item_id']},400,300) style='".$permission_D_button."cursor:pointer".$xhidden."'>";												
										}
									}									
								}
																
								
								echo "</td>";
								echo "<td align='center'><BR>";								
										 // 바코드 생성
										$generator = new BarcodeGeneratorHTML();
										echo $generator->getBarcode("{$item['item_code']}", $generator::TYPE_CODE_128);							
										echo "<BR>{$item['item_code']}";						
								echo "</td>";
								
								echo "<td>{$item['item_rdate']}</td>";
								echo "<td>관리자</td>";
								echo "<td><button type='button' class='btn btn-secondary btn-sm' onclick=popup_win_inreg('in_stock',{$item['item_id']}) style='padding:2'>입고등록</button></td>";
								$desc_start_no=$desc_start_no - 1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td colspan='7'>검색 결과없음</td></tr>";
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
				
						
				<div style="width:98%;text-align:right">
				<?
				// 분류 목록 가져오기
				$cates = getwms_cate($start_record_number,$itemsPerPage);	
				if ($cates) {
							
				?>
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win('item')" style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="대상 등록">제품 등록</button>	
				<?}else{ ?>
					<button type='button' class='btn btn-secondary btn-sm'  onclick="alert('제품카테고리 먼저 등록하세요. 제품분류등록으로 이동합니다.');location.href='/gm/m03/cate_list.php';" style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="대상 등록">제품 등록</button>	
				<?	
				}	
				?>
				</div>
 
				<!-- 엑셀업로드 st -->
				<? $this_f_name = "m03_product"; $this_f_txt = "제품 등록 (엑셀 업로드)";// 분류 ?>
				<div style="width:98%;text-align:right;background-color:#cfe9da;<? echo $permission_W_button;?>">
					<form action="/m_excel/upload_<?echo $this_f_name;?>.php" method="post" enctype="multipart/form-data" target="upload_iframe" onsubmit="showPopup();" >
 
						<br><label for="file" style="padding:12;margin-right:0px;margin-bottom:0px" ><u><a href="/m_excel/sample_excel/sample_<?echo $this_f_name;?>.xlsx">제품샘플 다운로드</a></u>&nbsp;|&nbsp;엑셀 파일 선택:<input type="file" name="file" id="file" required></label>
 
						<button type="submit" class='btn btn-secondary btn-sm'  style='<?echo $permission_W_button?>padding:12;margin-right:0px;margin-bottom:0px;width:60%;height:50px;font-weight:bold;background-color:#3c8259'> <?echo $this_f_txt;?></button>
					</form>
					<iframe name="upload_iframe" style="display:none;"></iframe>	
					
					<script>
						function showPopup() {
							// 파일이 업로드된 후 아이프레임이 로드될 때 실행되는 함수를 설정
							document.querySelector('iframe[name="upload_iframe"]').onload = function() {
								// 부모 창을 리로드
								parent.location.reload();
							};
						}
					</script>					
				</div>				
				<!-- 엑셀업로드 end -->			 
	 
	 		 

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