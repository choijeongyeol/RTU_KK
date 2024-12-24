<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/gm/inc/fn.php');    
session_start();	
 
// 사용자 권한 확인
//$user_role = $userManager->checkUserRole();

?>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gm/inc/sys_head.php'); ?>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gm/inc/sys_topmenu.php'); ?>


<?php 

   if ($_SESSION['sys_partner_id'] == "1111")   {
	include_once($_SERVER['DOCUMENT_ROOT'] . '/gm/inc/sys_sidebar_menu.php');     
  }else{
	include_once($_SERVER['DOCUMENT_ROOT'] . '/gm/inc/sidebar_menu.php');     
   }

?>   

<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gm/inc/sys_top_navigation.php'); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/gm/home/home_permission.php'); ?>

	
<?$queryString = $_SERVER['QUERY_STRING'];?>	
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	   /// 권한 체크 : 변경권한 - display:none  //////////////////////////////////////////////////////////////////////////////////////////////   
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
		$search_add = " and (( a.admin_name like '%".$keyword."%'  ) ";
		$search_add = $search_add." or ( a.admin_id like '%".$keyword."%'  ) ";
		$search_add = $search_add." or ( a.partner_id like '%".$keyword."%'  )) ";
		
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
			$search_add = $search_add." and date(a.admin_rdate)  between '". $searchStartDate."' and  '". $searchEndDate."' ";	 
 	   }else{
			//$add_condition = $add_condition." and admin_rdate  between '". $searchStartDate."' and  '". $searchEndDate."' ";	    
	   }
   } 

	$list_condition = " wms_admin a WHERE a.partner_id <>  ".$_SESSION['sys_partner_id']." and  a.admin_id like 'sysid%'".$search_add;
	
 
  
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	
	/////////////////   검색  end ////////////////////////////////////////////////////////////////////////

	?>			
 	
		
<?php

// 모든 오류 표시
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>		

<script>
    function popup_win(arg1, arg2 = null) {
        // 자식 창을 열고 창 객체를 저장
        var childWindow = window.open('ctrl_' + arg1 + '_input.php' + (arg2 ? '?arg2=' + arg2 : ''), '자식 창', 'width=400,height=300');

        // 부모 창에서 자식 창으로 데이터 전달
        if (childWindow) {
            var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
            childWindow.postMessage(dataToSend, '*');
        }
    }
	
	function sysStatus(myform, sn, seq) {
		if(confirm("처리여부를 수정하시겠습니까?")) {
			myform.sn.value = sn;  // idx
			myform.Process.value = document.getElementById("Process"+seq).value;  // 완료, 대기 등등..
			myform.action="/gm/inc/process_admin.php";
			myform.submit();
			window.location.reload();
		}
	}		
</script>

		
	<!-- 게시판 리스트 계산 start -->
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
                    <h2>HOME > 파트너사관리<small></small></h2> 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  파트너사 운영을 관리 할 수 있습니다.  <? //echo $_SESSION['sys'];?>
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
                                        <option value="a.admin_name" <?if ($searchType=="a.admin_name") {  echo "selected";  }?> >파트너 이름</option>
										<option value="a.admin_id" <?if ($searchType=="a.admin_id") {  echo "selected";  }?> >접속ID</option>
										<option value="a.partner_id" <?if ($searchType=="a.partner_id") {  echo "selected";  }?> >파트너 넘버</option>
                                    </select>
                                    <input type="text" name="keyword" class="design" style="" <?if ($keyword!="") { echo "value='".$keyword."'";
                                    }else{ echo "value='' placeholder='검색어' "; }?> />
                                </div>							
                            </td>
                            <th>등록일자</th>
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
                            <!-- <li>
                                <span class="c_btn h34 blue">
								    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-download"></i> 
                                    <input type="button" style="margin-left:-10px;margin-right:-7px;" onclick="location.href='/m_excel/phpspread_m04.php?<?echo $queryString;?>'" value="엑셀받기" />	
                                </span>
                            </li> -->							
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
				$partner = sys_getwms_admin($start_record_number,$itemsPerPage,$search_add);
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
							<th width="10%">NO</th>
							<th width="20%">파트너 이름</th>
							<th width="10%">파트너 넘버</th>
							<th width="10%">비번 관리</th>
							<th width="5%">비번 수정</th>
							<th width="15%">서비스</th>
							<th width="15%">등록일자</th>
						</tr>
					</thead>		
					<tbody>
					<?
 
						if ($partner) {
 							foreach ($partner as $partnerItem) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$partnerItem['admin_name']}</td>";
								//echo "<td>{$partnerItem['admin_id']}</td>";
								if("{$partnerItem['admin_use']}"=="Y"){
								echo "<td><a href='/gm/login_fromsys.php?partner_id={$partnerItem['partner_id']}&sys_code=sdfw*^34g53y4uy_(H$345dfg@*df' target='blank' style='text-decoration:underline;'>{$partnerItem['partner_id']}</a></td>";
								}else{
								echo "<td onclick=\"alert('서비스 활성화 후, 접속가능합니다.')\" ><span style='text-decoration:underline;cursor:pointer'>{$partnerItem['partner_id']}</span></td>";
								}
								echo "<td>";
								echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('syspw_change','{$partnerItem['admin_id']}') style='padding:2'>비번변경</button> ";								
								echo "</td>";
								echo "<td>{$partnerItem['set_state']}</td>";
								echo "<td>";
									if("{$partnerItem['partner_id']}"=="1234"){ echo "수정불가";
									}else{
								?>
									<select name="Process<?echo $desc_start_no?>" id="Process<?echo $desc_start_no?>"  class="input-sm" style="width:115px;height:30px;font-size:14px;border:1px solid #ccc;text-align:center">
										<option value="Y" <? if ("{$partnerItem['admin_use']}"=="Y") { echo "selected"; }  ?>>활성화</option>
										<option value="N" <? if ("{$partnerItem['admin_use']}"=="N") { echo "selected"; }  ?>>중지(복구가능)</option>
										<option value="D" <? if ("{$partnerItem['admin_use']}"=="D") { echo "selected"; }  ?>>삭제(복구불가)</option>
									</select> 
									<button type="button" class='btn btn-secondary btn-sm' onClick=sysStatus(document.reservation,'<? echo "{$partnerItem['partner_id']}"; ?>','<?echo $desc_start_no?>') >수정 </button>
								<?	
									}
								echo "</td>";
								echo "<td>{$partnerItem['admin_rdate']}</td>";
									
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
				
	
				<div style="width:98%;text-align:right">
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win_partner_reg('partner_reg',400,400)" style='margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="분류 등록">신규파트너 서비스 등록</button>	
				</div>
								 
 
		 

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
 
	<iframe width='0' height='0' frameborder="0" marginwidth="0" marginheight="0" name="inquery" ></iframe>
	<form name="reservation" method="post" target="inquery" style="display:none">
	<input type="hidden" name="sn" value="">
	<input type="hidden" name="Process" value="">
	<input type="hidden" name="location2" value="sys_partner_use">
	</form>

<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/foot.php'); ?>